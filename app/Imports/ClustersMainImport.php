<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ClustersMainImport implements WithMultipleSheets
{
    protected $sheetNames;

    // 1. Terima data nama sheet dari Controller
    public function __construct(array $sheetNames)
    {
        $this->sheetNames = $sheetNames;
    }

    public function sheets(): array
    {
        $sheets = [];
        // 2. Cek: Jika di file Excel ada sheet "Data_Cluster_All_Year"
        if (in_array('Data_Cluster_All_Year', $this->sheetNames)) {
            $sheets['Data_Cluster_All_Year'] = new DatasetClustersImport();
        }

        // 3. Cek: Jika di file Excel ada sheet "Centroid_All_Year"
        if (in_array('Centroid_KMeans', $this->sheetNames)) {
            $sheets['Centroid_KMeans'] = new CentroidKMeansImport();
        }

        return $sheets;
    }
}