<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Report;
use App\Models\ReportMedia;
use App\Models\ReportType;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class PublicReportController extends Controller
{
    /**
     * Halaman peta & data laporan (PUBLIC)
     * Menampilkan laporan yang sudah VERIFIED saja
     */
    public function index(Request $request)
    {
        // 🔹 MASTER DATA
        $types = ReportType::orderBy('name')->get();

        // 🔹 BASE QUERY (shared filter)
        $baseQuery = Report::query()->verified();

        if ($request->filled('type')) {
            $baseQuery->where('report_type_id', $request->type);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $baseQuery->whereBetween(
                DB::raw('DATE(occurred_at)'),
                [$request->from, $request->to]
            );
        }

        /**
         * =====================================================
         * 🔹 SUMMARY (AGGREGATE QUERY)
         * =====================================================
         */
        $summary = [
            'total' => (clone $baseQuery)->count(),

            'by_type' => (clone $baseQuery)
                ->select('report_type_id', DB::raw('COUNT(*) as total'))
                ->groupBy('report_type_id')
                ->pluck('total', 'report_type_id'),
        ];

        /**
         * =====================================================
         * 🔥 HEATMAP DATA (ONLY LAT & LNG)
         * =====================================================
         */
        $heatmapData = (clone $baseQuery)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('latitude', 'longitude')
            ->get()
            ->map(fn($r) => [
                (float) $r->latitude,
                (float) $r->longitude,
                1
            ]);

        /**
         * =====================================================
         * 📍 MARKER DATA (MINIMAL PAYLOAD)
         * =====================================================
         */
        $reports = (clone $baseQuery)
            ->with('type:id,name,color')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select(
                'id',
                'report_type_id',
                'latitude',
                'longitude',
                'occurred_at'
            )
            ->limit(1000) // ⛔ safety limit
            ->get();

        return view('public.index', compact(
            'types',
            'summary',
            'reports',
            'heatmapData'
        ));
    }

    public function show(Report $report)
    {
        // hanya laporan verified yang boleh dibuka publik
        abort_if($report->status !== 'verified', 404);

        $report->load([
            'type',
            'media' => fn($q) => $q->select('id', 'report_id', 'file_path')
        ]);

        return view('public.show', compact('report'));
    }



    /**
     * Halaman form laporan warga
     */
    public function create()
    {
        $types = ReportType::orderBy('name')->get();

        return view('public.create', [
            'types' => $types,
        ]);
    }

    /**
     * Simpan laporan warga (PUBLIC, tanpa login)
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'report_type_id' => 'required|exists:report_types,id',
                'occurred_at'    => 'required|date',
                'latitude'       => 'required|numeric|between:-90,90',
                'longitude'      => 'required|numeric|between:-180,180',
                'description'    => 'nullable|string|max:1000',
                'address_text'   => 'nullable|string|max:255',

                // media (opsional)
                'media'   => 'nullable|array|max:3',
                'media.*' => 'file|mimes:jpg,jpeg,png,heic,heif,webp|max:2048',

                // 🔐 CAPTCHA
                'g-recaptcha-response' => 'required',
            ],
            [
                'report_type_id.required' => 'Jenis laporan wajib dipilih.',
                'report_type_id.exists'   => 'Jenis laporan tidak valid.',

                'occurred_at.required' => 'Tanggal kejadian wajib diisi.',
                'occurred_at.date'     => 'Format tanggal kejadian tidak valid.',

                'latitude.required' => 'Lokasi latitude wajib diisi.',
                'latitude.numeric'  => 'Latitude harus berupa angka.',
                'latitude.between'  => 'Nilai latitude tidak valid.',

                'longitude.required' => 'Lokasi longitude wajib diisi.',
                'longitude.numeric'  => 'Longitude harus berupa angka.',
                'longitude.between'  => 'Nilai longitude tidak valid.',

                'description.max' => 'Deskripsi maksimal 1000 karakter.',
                'address_text.max' => 'Alamat maksimal 255 karakter.',

                'media.array' => 'Media harus berupa daftar file.',
                'media.max'   => 'Maksimal 3 file media yang dapat diunggah.',
                'media.*.mimes' => 'Format gambar harus jpg, jpeg, png, heic, atau webp.',
                'media.*.max'   => 'Ukuran gambar maksimal 2MB.',

                // CAPTCHA
                'g-recaptcha-response.required' => 'Mohon verifikasi captcha terlebih dahulu.',
            ]
        );

        // 🔐 VALIDASI CAPTCHA KE GOOGLE
        $captchaResponse = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret'   => config('services.recaptcha.secret_key'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]
        );

        if (!data_get($captchaResponse->json(), 'success')) {
            return back()
                ->withErrors(['g-recaptcha-response' => 'Verifikasi captcha gagal. Silakan coba lagi.'])
                ->withInput();
        }

        // 1️⃣ SIMPAN LAPORAN
        $report = Report::create([
            'report_type_id' => $validated['report_type_id'],
            'occurred_at'    => $validated['occurred_at'],
            'latitude'       => $validated['latitude'],
            'longitude'      => $validated['longitude'],
            'description'    => $validated['description'] ?? null,
            'address_text'   => $validated['address_text'] ?? null,
            'status'         => 'pending',
            'reporter_ip'    => $request->ip(),
        ]);

        // 2️⃣ SIMPAN MEDIA (JIKA ADA)
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {

                $extension = strtolower($file->getClientOriginalExtension());

                $filename = uniqid() . '.jpg';
                $path = 'reports/' . $report->id . '/' . $filename;

                // Convert HEIC / HEIF / WEBP → JPG
                if (in_array($extension, ['heic', 'heif', 'webp'])) {

                    $image = Image::make($file)
                        ->orientate()
                        ->encode('jpg', 85);

                    Storage::disk('public')->put($path, (string) $image);

                    $mime = 'image/jpeg';
                    $size = strlen((string) $image);
                } else {
                    // JPG / PNG → simpan langsung (tetap distandarkan JPG)
                    $image = Image::make($file)
                        ->orientate()
                        ->encode('jpg', 85);

                    Storage::disk('public')->put($path, (string) $image);

                    $mime = 'image/jpeg';
                    $size = strlen((string) $image);
                }

                ReportMedia::create([
                    'report_id' => $report->id,
                    'file_path' => $path,
                    'file_type' => $mime,
                    'file_size' => $size,
                ]);
            }
        }

        return redirect()
            ->route('public.report.create')
            ->with('success', 'Terima kasih. Laporan Anda telah kami terima dan akan diverifikasi.');
    }
}
