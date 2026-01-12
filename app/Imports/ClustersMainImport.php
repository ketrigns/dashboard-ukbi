<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ClustersMainImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Data_Cluster_All_Year' => new DatasetClustersImport(),
            'Centroid_All_Year'  => new CentroidKMeansImport(),
        ];
    }
}
