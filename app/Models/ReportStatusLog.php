<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'old_status',
        'new_status',
        'changed_by',
        'note',
    ];

    // 🔗 Relasi ke laporan
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    // 🔗 Relasi ke user (admin/moderator)
    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
