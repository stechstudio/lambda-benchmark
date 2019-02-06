<?php
namespace App\Console;


use Aws\CommandInterface;
use Aws\CommandPool;
use Aws\Lambda\LambdaClient;
use Aws\ResultInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Console\Command;
use Aws\Exception\AwsException;
use Illuminate\Support\Str;


class Benchmark extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'sts:benchmark 
                       {function : The name or arn of the function to test.} 
                       {--r|runs=1000 : How many times you would like to execute the test.} 
                       {--c|concurrency=25 : How many max concurrent tests you want to run.}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Given a lambda function, benchmark that sucker.';

    public function handle(): int
    {
        $lambdaClient = new LambdaClient([
            'version' => 'latest',
            'region'  => env('AWS_REGION', 'us-east-1')
        ]);

        $event = '{
              "key3": "value3",
              "key2": "value2",
              "key1": "value1"
            }';

        $commands = collect([]);

        for ($i = 1; $i <= $this->option('runs'); $i++) {
            $commands->push(
                $lambdaClient->getCommand('Invoke', [
                    'FunctionName' => $this->argument('function'),
                    'InvocationType' => 'RequestResponse',
                    'LogType' => 'Tail',
                    'Payload' => $event
                ])
            );
        }
        $metrics[0] = ['request_id', 'duration', 'billed_duration', 'memory_size', 'max_memory_used'];
        $pool = new CommandPool($lambdaClient, $commands, [
            // Only execute X commands at a time
            'concurrency' => $this->option('concurrency'),
            // Invoke this function before executing each command
            'before' => function (CommandInterface $cmd, $iterKey) {
                // nothing do here ... yet.
            },
            // Invoke this function for each successful execution
            'fulfilled' => function (
                ResultInterface $result,
                $iterKey,
                PromiseInterface $aggregatePromise
            ) use(&$metrics){

                $log = base64_decode($result->get('LogResult'), true);
                $lines = explode("\n", $log);
                $lines = array_filter($lines);
                $data = explode("\t", end($lines));
                $metric = [];
                collect($data)->each(function($value) use (&$metric){
                    if (empty($value)) {return;}

                    list($l, $v) = explode(": ", $value);
                    $l = starts_with($l, 'REPORT')? 'RequestId' : $l;
                    $metric[snake_case($l)] = $this->cleanData($v);
                });
                $metrics[] = [$metric['request_id'], $metric['duration'], $metric['billed_duration'], $metric['memory_size'], $metric['max_memory_used']];

            },
            // Invoke this function for each failed execution
            'rejected' => function (
                AwsException $reason,
                $iterKey,
                PromiseInterface $aggregatePromise
            ) {
                $this->warn("Failed {$iterKey}: {$reason}\n");
            },
        ]);

        // Initiate the pool transfers
        $promise = $pool->promise();

        // Force the pool to complete synchronously
        $promise->wait();

        $out = fopen('php://output', 'w');
        foreach ($metrics as $fields) {
            fputcsv($out, $fields);
        }
        fclose($out);
        return 0;
    }

    protected function cleanData(string $data) {
        $data = trim($data);
        if (substr_compare($data, ' MB', -3, 3) === 0){
            return (int)trim(rtrim($data, ' MB'));
        }
        if (substr_compare($data, ' ms', -3, 3) === 0){
            return (float)trim(rtrim($data,' ms'));
        }
        return $data;
    }
}
