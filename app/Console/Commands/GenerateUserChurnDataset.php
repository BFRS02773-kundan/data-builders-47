<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MLTraining\DataBuilders\UserChurnDataBuilder;
use App\Services\MLTraining\DataDumpers\CsvDumper;
use App\Models\DatasetBuildLog;

class GenerateUserChurnDataset extends Command
{
    protected $signature = 'ml:generate-user-churn-dataset';
    protected $description = 'Generate User Churn Training Dataset';

    public function handle()
    {
        $this->info("Building dataset...");
        $builder = new UserChurnDataBuilder();
        $dataset = $builder->build();

        $this->info("Dataset built: " . count($dataset) . " records");

        $dumper = new CsvDumper();
        $dumper->dump($dataset, 'user_churn_training_dataset.csv');

        // Log to DB
        DatasetBuildLog::create([
            'dataset_name' => 'user_churn_training_dataset.csv',
            'status' => 'completed',
            'file_path' => 'user_churn_training_dataset.csv', // Only the filename
            'record_count' => count($dataset),
            'error_message' => null,
        ]);

        $this->info("Dataset dumped at: storage/app/ml_datasets/user_churn_training_dataset.csv");
    }
}
//return response()->download(storage_path("app/ml_datasets/ml_datasets/{$log->file_path}"));
