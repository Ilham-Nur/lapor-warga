<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 🔗 Relasi: user yang mereview banyak laporan
    public function reviewedReports()
    {
        return $this->hasMany(Report::class, 'reviewed_by');
    }

    // 🔗 Relasi: histori perubahan status
    public function statusLogs()
    {
        return $this->hasMany(ReportStatusLog::class, 'changed_by');
    }

    // 🧠 Helper sederhana
    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }
}
