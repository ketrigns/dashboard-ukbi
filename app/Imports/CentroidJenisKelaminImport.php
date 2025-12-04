<?php

namespace App\Imports;

use App\Models\CentroidJenisKelamin;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class CentroidJenisKelaminImport implements
    ToModel,
    WithHeadingRow,
    WithChunkReading,
    WithBatchInserts,
    SkipsEmptyRows
{
    protected static $isCleared = false;

    public function __construct()
    {
        if (!self::$isCleared) {
            CentroidJenisKelamin::truncate();
            self::$isCleared = true;
        }
    }

    public function model(array $row)
    {
        return new CentroidJenisKelamin([
            'jenis_kelamin'       => $row['jenis_kelamin'] ?? null,
            'seksi_i'       => $row['seksi_i'] ?? null,
            'seksi_ii'      => $row['seksi_ii'] ?? null,
            'seksi_iii'     => $row['seksi_iii'] ?? null,
        ]);
    }

    public function chunkSize(): int { return 1000; }
    public function batchSize(): int { return 100; }
}

