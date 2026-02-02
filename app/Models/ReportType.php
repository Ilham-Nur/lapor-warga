<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
    ];

    // 🔗 Relasi: satu jenis punya banyak laporan
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
