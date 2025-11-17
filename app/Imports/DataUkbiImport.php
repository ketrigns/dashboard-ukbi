<?php

namespace App\Imports;

use App\Models\DataUkbi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class DataUkbiImport implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation, 
    WithChunkReading,
    WithBatchInserts
{
    public function model(array $row)
    {
        // Abaikan baris kosong
        if (empty($row['no_pendaftaran']) || empty($row['nama_peserta'])) {
            return null;
        }

        // Cek apakah sudah ada dalam database
        $existing = DataUkbi::where('no_pendaftaran', $row['no_pendaftaran'])->first();

        // Jika data sudah ada → update saja, jangan return model!
        if ($existing) {
            $existing->update([
                'tanggal_ujian'        => $row['tanggal_ujian'] ?? null,
                'nama_peserta'         => $row['nama_peserta'] ?? null,
                'terdaftar_sbg'        => $row['terdaftar_sebagai'] ?? null,
                'jenis_kelamin'        => $row['jenis_kelamin'] ?? null,
                'tempat_lahir'         => $row['tempat_lahir'] ?? null,
                'tanggal_lahir'        => $row['tanggal_lahir'] ?? null,
                'kota'                 => $row['kota'] ?? null,
                'titik_koordinat_peta' => $row['titik_koordinat_kota'] ?? null,
                'instansi'             => $row['instansi'] ?? null,
                'seksi_1'              => $row['seksi_i'] ?? null,
                'seksi_2'              => $row['seksi_ii'] ?? null,
                'seksi_3'              => $row['seksi_iii'] ?? null,
                'seksi_4'              => $row['seksi_iv'] ?? null,
                'seksi_5'              => $row['seksi_v'] ?? null,
                'skor'                 => $row['skor'] ?? null,
                'predikat'             => $row['predikat'] ?? null,
            ]);

            return null; // penting! jangan return model existing
        }

        // Jika data belum ada → insert baru melalui batch insert
        return new DataUkbi([
            'no_pendaftaran'        => $row['no_pendaftaran'],
            'tanggal_ujian'        => $row['tanggal_ujian'] ?? null,
            'nama_peserta'         => $row['nama_peserta'] ?? null,
            'terdaftar_sbg'        => $row['terdaftar_sebagai'] ?? null,
            'jenis_kelamin'        => $row['jenis_kelamin'] ?? null,
            'tempat_lahir'         => $row['tempat_lahir'] ?? null,
            'tanggal_lahir'        => $row['tanggal_lahir'] ?? null,
            'kota'                 => $row['kota'] ?? null,
            'titik_koordinat_peta' => $row['titik_koordinat_kota'] ?? null,
            'instansi'             => $row['instansi'] ?? null,
            'seksi_1'              => $row['seksi_i'] ?? null,
            'seksi_2'              => $row['seksi_ii'] ?? null,
            'seksi_3'              => $row['seksi_iii'] ?? null,
            'seksi_4'              => $row['seksi_iv'] ?? null,
            'seksi_5'              => $row['seksi_v'] ?? null,
            'skor'                 => $row['skor'] ?? null,
            'predikat'             => $row['predikat'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.no_pendaftaran'      => 'nullable',
            '*.tanggal_ujian'       => 'nullable',
            '*.nama_peserta'        => 'nullable',
            '*.terdaftar_sebagai'   => 'nullable',
            '*.jenis_kelamin'       => 'nullable',
            '*.tempat_lahir'        => 'nullable',
            '*.tanggal_lahir'       => 'nullable',
            '*.kota'                => 'nullable',
            '*.titik_koordinat_kota'=> 'nullable',
            '*.instansi'            => 'nullable',
            '*.seksi_i'             => 'nullable',
            '*.seksi_ii'            => 'nullable',
            '*.seksi_iii'           => 'nullable',
            '*.seksi_iv'            => 'nullable',
            '*.seksi_v'             => 'nullable',
            '*.skor'                => 'nullable',
            '*.predikat'            => 'nullable',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 100;
    }
}
