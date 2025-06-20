<?php

namespace App\Services\MLTraining\DataDumpers;

class CsvDumper
{
    /**
     * Dump the dataset to a CSV file in storage/app/ml_datasets.
     *
     * @param array $dataset
     * @param string $filename
     * @return void
     */
    public function dump(array $dataset, string $filename): void
    {
        $directory = storage_path('app/ml_datasets');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        $filepath = $directory . DIRECTORY_SEPARATOR . $filename;
        $file = fopen($filepath, 'w');
        if (empty($dataset)) {
            fclose($file);
            return;
        }
        // Write header
        fputcsv($file, array_keys($dataset[0]));
        // Write rows
        foreach ($dataset as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    }
}
