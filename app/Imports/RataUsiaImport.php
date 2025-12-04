<?php

namespace App\Imports;

use App\Models\RataUsia;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class RataUsiaImport implements
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
            RataUsia::truncate();
            self::$isCleared = true;
        }
    }

    public function model(array $row)
    {
        return new RataUsia([
            'usia'       => $row['usia'] ?? null,
        ]);
    }

    public function chunkSize(): int { return 1000; }
    public function batchSize(): int { return 100; }
}
