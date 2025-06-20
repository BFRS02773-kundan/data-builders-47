<?php

namespace App\Services\MLTraining\DataDumpers;

class CsvDumper
{
    public function dump(array $dataset, string $filename)
    {
        $filePath = storage_path("app/ml_datasets/{$filename}");
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }

        $handle = fopen($filePath, 'w');
        if (empty($dataset)) {
            fclose($handle);
            return;
        }

        // Write headers
        fputcsv($handle, array_keys($dataset[0]));

        foreach ($dataset as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
    }
}
