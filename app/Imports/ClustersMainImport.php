<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ClustersMainImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Dataset_Clusters' => new DatasetClustersImport(),
            'Centroid_KMeans'  => new CentroidKMeansImport(),
        ];
    }
}
