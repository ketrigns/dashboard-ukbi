<?php

namespace App\Imports;

use App\Models\DatasetClusters;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class DatasetClustersImport implements 
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
            DatasetClusters::truncate();
            self::$isCleared = true;
        }
    }

    public function model(array $row)
    {
        return new DatasetClusters([
            'nama_peserta'   => $row['nama_peserta'] ?? null,
            'kota'           => $row['kota'] ?? null,
            'jenis_kelamin'  => $row['jenis_kelamin'] ?? null,
            'seksi_i'        => $row['seksi_i'] ?? null,
            'seksi_ii'       => $row['seksi_ii'] ?? null,
            'seksi_iii'      => $row['seksi_iii'] ?? null,
            'usia'           => $row['usia'] ?? null,
            'tahun_ujian'    => $row['tahun_ujian'] ?? null,
            'cluster_kmeans' => $row['cluster_kmeans'] ?? null,
            'cluster_usia'   => $row['cluster_usia'] ?? null,
        ]);
    }

    public function chunkSize(): int { return 1000; }
    public function batchSize(): int { return 100; }
}
