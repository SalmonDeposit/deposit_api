<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Services\AzureStorage;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use League\Csv\Writer;

class DocumentsTypesThreeYearsExtract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extract:documents-types-three-years';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract users documents stats';

    /** @var Job */
    private $job;

    /** @var string */
    private $outputPath;

    /** @var string */
    private $fileName;

    private const BATCH_SIZE = 1000;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->fileName = 'documents_types_three_years_extract_' . time() . '.csv';
        $this->outputPath = storage_path() . '/extracts/';
        $this->outputFilePath = $this->outputPath . $this->fileName;

        $this->job = Job::where('class', '=', self::class)->first();

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            if ($this->job === null) {
                throw new Exception('Cannot find any job related to ' . self::class . ' class.');
            }

            $this->job->update(['running' => 1, 'launched_at' => Carbon::now()]);
            $this->log('Start ---');

            $startTime = microtime(true);

            $data = \DB::select(
                "SELECT
                    u.id AS user_id,
                    o.id AS offer_id,
                    CASE
                        WHEN d.type IN (
                            'text/csv',
                            'application/pdf',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                        ) THEN 'Document'
                        WHEN d.type IN ('video/mp4', 'video/mpeg') THEN 'Video'
                        WHEN d.type IN ('image/jpeg', 'image/png') THEN 'Image'
                        ELSE d.type
                    END AS document_category,
                    d.created_at AS document_created_at,
                    d.size AS document_size
                FROM documents d
                INNER JOIN users u ON u.id = d.user_id
                LEFT JOIN offers o ON o.id = u.offer_id
                ORDER BY document_created_at;"
            );

            $pages = ceil(sizeof($data) / self::BATCH_SIZE);
            $this->log($pages . ' pages found.');

            if (!is_dir($this->outputPath))
                mkdir($this->outputPath, 0775, true);

            $headers = ['user_id', 'offer_id', 'document_category', 'document_created_at', 'document_size'];

            $csv = Writer::createFromPath($this->outputFilePath, 'a+');
            $csv->setDelimiter(';');

            $csv->insertOne($headers);

            for ($i = 1; $i <= $pages; $i++) {
                $this->log('Extracting page : ' . $i);

                if (sizeof($data) > self::BATCH_SIZE) {
                    $rows = array_splice($data, $i * self::BATCH_SIZE);
                } else {
                    $rows = $data;
                }

                $batch_extract = [];

                foreach ($rows as $row) {
                    $toWriteRow = [];
                    foreach ($headers as $field) {
                        if (isset($row->$field)) {
                            $toWriteRow[] = $row->$field;
                        }
                    }

                    if (!empty($toWriteRow))
                        $batch_extract[] = $toWriteRow;
                }

                if (!empty($batch_extract))
                    $csv->insertAll($batch_extract);
            }

            $endTime = microtime(true);

            $this->log('Successfully extracted documents types stats of 3 years old in ' . number_format(($endTime - $startTime), 2) . ' seconds.', 'success');

            // Send to Azure Storage
            AzureStorage::uploadExtract($this->outputFilePath, $this->fileName);

            $this->log('Exported ' . $this->fileName . ' to Azure Storage.', 'success');

            $this->job->update([
                'running' => 0,
                'status' => 'OK',
                'message' => '',
                'finished_at' => Carbon::now()
            ]);

            return 0;
        } catch (Exception $e) {
            if ($this->job instanceof Job) {
                $this->job->update([
                    'running' => 0,
                    'status' => 'ERROR',
                    'message' => $e->getMessage(),
                    'finished_at' => Carbon::now()
                ]);
            }
            $this->log($e->getMessage(), 'error');
        } finally {
            $this->log('--- End');
        }

        return 1;
    }

    /**
     * @param string $message
     * @param string $logLevel
     * @return void
     */
    private function log(string $message, string $logLevel = 'debug'): void
    {
        if (!empty($message)) {
            switch ($logLevel) {
                case 'success':
                    Log::info('[' . self::class . '] ' . $message);
                    $this->output->info('[' . self::class . '] ' . $message);
                    break;
                case 'error':
                    Log::error('[' . self::class . '] ' . $message);
                    $this->output->error('[' . self::class . '] ' . $message);
                    break;
                default:
                    Log::info('[' . self::class . '] ' . $message);
                    $this->output->writeln('[' . self::class . '] ' . $message);
            }
        }
    }
}
