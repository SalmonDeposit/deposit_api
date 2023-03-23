<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Models\Offer;
use App\Services\AzureStorage;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use League\Csv\Writer;

class OffersUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extract:offers-usage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /** @var Job */
    private $job;

    /** @var string */
    private $outputPath;

    /** @var string */
    private $fileName;

    private const BATCH_SIZE = 10000;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->fileName = 'offers_usage_extract_' . time() . '.csv';
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

            if (!is_dir($this->outputPath))
                mkdir($this->outputPath, 0775, true);

            $offers = Offer::orderBy('price_excluding_tax')->get();

            $headers = ['user_id'];

            foreach ($offers as $offer) {
                $headers[] = $offer->id;
            }

            $headers[] = 'document_created_at';

            $csv = Writer::createFromPath($this->outputFilePath, 'a+');
            $csv->setDelimiter(';');

            $csv->insertOne($headers);

            foreach ($offers as $offer_index1 => $offer) {
                $data = \DB::select(
                    "SELECT
                        u.id AS user_id,
                        SUM(d.size) AS documents_size,
                        SUM(d.size / (o.max_available_space * 1000000000) * 100) AS percentage_used_by_document,
                        DATE_FORMAT(d.created_at, '%m-%Y') AS document_created_at
                    FROM users u
                    INNER JOIN offers o ON o.id = u.offer_id
                    INNER JOIN documents d ON d.user_id = u.id
                    WHERE o.wording = :offer
                    GROUP BY document_created_at, user_id", ['offer' => $offer->wording]
                );

                $pages = ceil(sizeof($data) / self::BATCH_SIZE);
                $this->log($pages . ' pages found for offer ' . $offer->wording);

                for ($i = 1; $i <= $pages; $i++) {
                    $this->log('Extracting offer ' . $offer->wording . ' page : ' . $i);

                    if (sizeof($data) > self::BATCH_SIZE) {
                        $rows = array_splice($data, $i * self::BATCH_SIZE);
                    } else {
                        $rows = $data;
                    }

                    $batch_extract = [];

                    foreach ($rows as $row) {
                        $toWriteRow = [];
                        $toWriteRow[0] = $row->user_id;
                        foreach ($offers as $offer_index => $offer) {
                            $toWriteRow[$offer_index + 1] = $offer_index == $offer_index1 ? $row->percentage_used_by_document : null;
                        }
                        $toWriteRow[] = $row->document_created_at;

                        if (!empty($toWriteRow))
                            $batch_extract[] = $toWriteRow;
                    }

                    if (!empty($batch_extract))
                        $csv->insertAll($batch_extract);
                }
            }

            $endTime = microtime(true);

            $this->log('Successfully extracted offers usage stats in ' . number_format(($endTime - $startTime), 2) . ' seconds.', 'success');

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
