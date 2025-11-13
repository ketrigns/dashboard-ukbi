<?php

namespace App\Imports;

use App\Models\DataUkbi;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DataUkbiImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Setiap baris Excel akan di-mapping ke model DataUkbi
     * Key array sesuai dengan nama kolom header di Excel
     */
    public function model(array $row)
    {
        // Abaikan baris kosong
        if (empty($row['no_pendaftaran']) || empty($row['nama_peserta'])) {
            return null;
        }

        return DataUkbi::updateOrCreate(
            [
                'no_pendaftaran' => $row['no_pendaftaran'],
            ],
            [
                'tanggal_ujian'         => $row['tanggal_ujian'] ?? null,
                'nama_peserta'          => $row['nama_peserta'] ?? null,
                'terdaftar_sbg'         => $row['terdaftar_sebagai'] ?? null,
                'jenis_kelamin'         => $row['jenis_kelamin'] ?? null,
                'tempat_lahir'          => $row['tempat_lahir'] ?? null,
                'tanggal_lahir'         => $row['tanggal_lahir'] ?? null,
                'kota'                  => $row['kota'] ?? null,
                'titik_koordinat_peta'  => $row['titik_koordinat_kota'] ?? null,
                'instansi'              => $row['instansi'] ?? null,
                'seksi_1'               => $row['seksi_i'] ?? null,
                'seksi_2'               => $row['seksi_ii'] ?? null,
                'seksi_3'               => $row['seksi_iii'] ?? null,
                'seksi_4'               => $row['seksi_iv'] ?? null,
                'seksi_5'               => $row['seksi_v'] ?? null,
                'skor'                  => $row['skor'] ?? null,
                'predikat'              => $row['predikat'] ?? null,
            ]
        );

        // return DataUkbi::create(
        //     [
        //         'no_pendaftaran' => $row['no_pendaftaran'] ?? null,
        //         'tanggal_ujian'         => $row['tanggal_ujian'] ?? null,
        //         'nama_peserta'          => $row['nama_peserta'] ?? null,
        //         'terdaftar_sbg'         => $row['terdaftar_sebagai'] ?? null,
        //         'jenis_kelamin'         => $row['jenis_kelamin'] ?? null,
        //         'tempat_lahir'          => $row['tempat_lahir'] ?? null,
        //         'tanggal_lahir'         => $row['tanggal_lahir'] ?? null,
        //         'kota'                  => $row['kota'] ?? null,
        //         'titik_koordinat_peta'  => $row['titik_koordinat_kota'] ?? null,
        //         'kelas'                 => $row['kelas'] ?? null,
        //         'instansi'              => $row['instansi'] ?? null,
        //         'seksi_1'               => $row['seksi_i'] ?? null,
        //         'seksi_2'               => $row['seksi_ii'] ?? null,
        //         'seksi_3'               => $row['seksi_iii'] ?? null,
        //         'seksi_4'               => $row['seksi_iv'] ?? null,
        //         'seksi_5'               => $row['seksi_v'] ?? null,
        //         'skor'                  => $row['skor'] ?? null,
        //         'predikat'              => $row['predikat'] ?? null,
        //     ]
        // );
    }

    /**
     * Validasi tiap kolom berdasarkan nama kolom di Excel
     */
    public function rules(): array
    {
        return [
            'no_pendaftaran'     => 'nullable',
            'tanggal_ujian'      => 'nullable',
            'nama_peserta'       => 'nullable',
            'terdaftar_sbg'      => 'nullable',
            'jenis_kelamin'      => 'nullable',
            'tempat_lahir'       => 'nullable',
            'tanggal_lahir'      => 'nullable',
            'kota'               => 'nullable',
            'titik_koordinat_peta' => 'nullable',
            'instansi'           => 'nullable',
            'seksi_1'            => 'nullable',
            'seksi_2'            => 'nullable',
            'seksi_3'            => 'nullable',
            'seksi_4'            => 'nullable',
            'seksi_5'            => 'nullable',
            'skor'               => 'nullable',
            'predikat'           => 'nullable',
        ];
    }
}
