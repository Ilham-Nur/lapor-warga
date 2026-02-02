<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_type_id',
        'occurred_at',
        'description',
        'latitude',
        'longitude',
        'address_text',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_note',
        'reporter_ip',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    // 🔗 Jenis laporan
    public function type()
    {
        return $this->belongsTo(ReportType::class, 'report_type_id');
    }

    // 🔗 Media (foto / video)
    public function media()
    {
        return $this->hasMany(ReportMedia::class);
    }

    // 🔗 Admin yang mereview
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // 🔗 Histori status
    public function statusLogs()
    {
        return $this->hasMany(ReportStatusLog::class);
    }

    // 🧠 Scope penting
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
