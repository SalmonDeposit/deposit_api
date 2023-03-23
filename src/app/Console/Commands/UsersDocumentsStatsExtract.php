<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Services\AzureStorage;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use League\Csv\Writer;

class UsersDocumentsStatsExtract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extract:users-documents-stats';

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
        $this->fileName = 'users_documents_stats_extract_' . time() . '.csv';
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
                'SELECT
                    u.id                 AS user_id,
                    d.type               AS document_type,
                    COUNT(d.id)          AS number_of_type,
                    ROUND(AVG(d.size),0) AS average_size,
                    MAX(d.size)          AS max_size
                FROM users u
                INNER JOIN documents d on d.user_id = u.id
                GROUP BY d.type, u.id
                ORDER BY u.id, d.type'
            );

            $pages = ceil(sizeof($data) / self::BATCH_SIZE);

            $this->log($pages . ' pages found.');

            if (!is_dir($this->outputPath))
                mkdir($this->outputPath, 0775, true);

            $headers = ['user_id', 'document_type', 'number_of_type', 'average_size', 'max_size'];

            $csv = Writer::createFromPath($this->outputFilePath, 'a+');
            $csv->setDelimiter(';');

            $csv->insertOne($headers);

            for ($i = 1; $i <= $pages; $i++) {
                $this->log('Extracting page : ' . $i);

                $rows = array_splice($data, $i * self::BATCH_SIZE);
                $batch_extract = [];

                foreach ($rows as $row) {
                    $toWriteRow = [];
                    foreach ($headers as $field) {
                        $toWriteRow[] = $row->$field ?? null;
                    }

                    if (!empty($toWriteRow))
                        $batch_extract[] = $toWriteRow;
                }

                if (!empty($batch_extract))
                    $csv->insertAll($batch_extract);
            }

            $endTime = microtime(true);

            $this->log('Successfully extracted documents stats of ' . sizeof($data) . ' users in ' . number_format(($endTime - $startTime), 2) . ' seconds.', 'success');

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
