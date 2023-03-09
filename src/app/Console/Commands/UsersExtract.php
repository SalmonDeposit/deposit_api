<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Job;
use App\Models\User;
use App\Services\AzureStorage;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use League\Csv\Writer;

class UsersExtract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extract:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract Users';

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
        $this->fileName = 'users_extract_' . time() . '.csv';
        $this->outputPath = storage_path() . '/extracts/';
        $this->outputFilePath = $this->outputPath . $this->fileName;

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
            $job = Job::where('class', '=', self::class)->first();

            if ($job === null) {
                throw new Exception('Cannot find any job related to ' . self::class . ' class.');
            }

            $job->update(['running' => 1, 'launched_at' => Carbon::now()]);
            $this->log('Start ---');

            $startTime = microtime(true);

            $usersCount = User::count('id');
            $pages = ceil($usersCount / self::BATCH_SIZE);

            $this->log($pages . ' pages found.');

            if (!is_dir($this->outputPath))
                mkdir($this->outputPath, 0775, true);

            $headers = User::getTableColumns();

            $csv = Writer::createFromPath($this->outputFilePath, 'a+');
            $csv->setDelimiter(';');

            $csv->insertOne($headers);

            for ($i = 1; $i <= $pages; $i++) {
                $this->log('Extracting page : ' . $i);

                $users = User::paginate(self::BATCH_SIZE, ['*'], 'page', $i);
                $batch_extract = [];

                foreach ($users as $user) {
                    $userRow = [];
                    foreach ($headers as $field) {
                        $userRow[] = $user->$field ?? null;
                    }

                    if (!empty($userRow))
                        $batch_extract[] = $userRow;
                }

                if (!empty($batch_extract))
                    $csv->insertAll($batch_extract);
            }

            $endTime = microtime(true);

            $this->log('Successfully extracted ' . $usersCount . ' users in ' . number_format(($endTime - $startTime), 2) . ' seconds.', 'success');

            // Send to Azure Storage
            AzureStorage::uploadExtract($this->outputFilePath, $this->fileName);

            $this->log('Exported ' . $this->fileName . ' to Azure Storage.', 'success');

            // Send to Power BI
//            $client = new Client([
//                'base_uri' => 'https://api.powerbi.com/',
//                'headers' => [
//                    'Content-Type' => 'text/csv',
//                    'Authorization' => 'Bearer <access_token>',
//                ],
//                'auth' => [
//                    '<username>',
//                    '<password>',
//                ],
//            ]);
//
//            $response = $client->post('/v1.0/myorg/datasets/<dataset_id>/tables/<table_name>/rows', [
//                'body' => [],
//            ]);
//
//            if ($response->getStatusCode() !== 200)
//                throw new Exception(
//                    'Unable to send CSV data to Power BI. Response code : ' . $response->getStatusCode()
//                );

            $job->update([
                'running' => 0,
                'status' => 'OK',
                'message' => '',
                'finished_at' => Carbon::now()
            ]);

            return 0;
        } catch (Exception $e) {
            if (isset($job)) {
                $job->update([
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
