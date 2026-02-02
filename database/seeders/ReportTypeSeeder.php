<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReportType;

class ReportTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Begal', 'color' => 'red'],
            ['name' => 'Jambret', 'color' => 'orange'],
            ['name' => 'Pengeroyokan', 'color' => 'purple'],
            ['name' => 'Tawuran', 'color' => 'black'],
            ['name' => 'Pelecehan', 'color' => 'yellow'],
            ['name' => 'Pencurian', 'color' => 'blue'],
        ];

        foreach ($types as $type) {
            ReportType::updateOrCreate(
                ['name' => $type['name']],
                ['color' => $type['color']]
            );
        }
    }
}
