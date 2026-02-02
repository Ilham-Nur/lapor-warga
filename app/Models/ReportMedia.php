<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportMedia extends Model
{
    use HasFactory;

    protected $table = 'report_media';

    protected $fillable = [
        'report_id',
        'file_path',
        'file_type',
    ];

    // 🔗 Relasi ke laporan
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
