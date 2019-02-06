# Lambda Benchmarks
Run a lambda job a number of times and report the metrics.

## Setup
```
git clone git@github.com:stechstudio/lambda-benchmark.git
cd lambda-benchmark
composer install
```

## Usage
```bash
> php artisan help sts:benchmark

Description:
  Given a lambda function and event, benchmark that sucker.

Usage:
  sts:benchmark [options] [--] <function>

Arguments:
  function                         The name or arn of the function to test.

Options:
  -r, --runs[=RUNS]                How many times you would like to execute the test. [default: "1000"]
  -c, --concurrency[=CONCURRENCY]  How many max concurrent tests you want to run. [default: "25"]
  -h, --help                       Display this help message
  -q, --quiet                      Do not output any message
  -V, --version                    Display this application version
      --ansi                       Force ANSI output
      --no-ansi                    Disable ANSI output
  -n, --no-interaction             Do not ask any interactive question
      --env[=ENV]                  The environment the command should run under
  -v|vv|vvv, --verbose             Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

```

## Example
```bash
> php artisan sts:benchmark my_function -r 30 -c 10     
                                    
request_id,duration,billed_duration,memory_size,max_memory_used
c0d912e2-df8b-4b1c-b176-9b074581510e,0.35,100,128,21
505ec09b-d486-4508-9697-408b83e7c307,0.66,100,128,21
c71281c3-c2eb-4681-8600-630b2b2f843c,0.44,100,128,20
b037848f-0d36-4506-af69-a3ae738a60e5,4.52,100,128,21
1a8d924d-350d-4428-989e-d3e3cb93cd9a,11.81,100,128,21
89ef6487-5915-4f71-96fe-03d6a568b4ca,0.33,100,128,21
adc562cf-8fa0-435f-aa45-beb15a817052,13.91,100,128,21
cb3695dc-021a-41f3-b233-a31cccc512ea,9.43,100,128,21
798bca55-fe63-40fc-b4f4-2220a6b02806,10.87,100,128,21
9f69e49a-8c04-4b6b-a8e0-de08f1736367,15.25,100,128,21
b2962a01-a1bf-4413-b004-e92efb58774e,0.52,100,128,22
5c060a3e-6db3-4079-b466-b3920803f510,20.52,100,128,21
de60a57a-0d97-4457-8fc4-a77d7dd19591,33.84,100,128,21
f3e9a51a-f11c-4133-93f4-a10d8a28dfe8,14.1,100,128,21
be9ad4e1-4137-4c88-8c84-b6630600be1a,50.99,100,128,21
aa3ab804-bf84-44e8-a61d-afd08f0508c2,0.29,100,128,21
ecc34c97-83d7-4bfa-972f-a2cd7c133c9a,7.29,100,128,21
26f36312-4423-4e02-aa72-e710445f2e1a,55.48,100,128,20
3619e0b4-c203-4c21-8299-ce0150d108f5,10,100,128,20
1d8bd0f7-9a23-47d9-9512-bd0bd07e8050,35.03,100,128,21
5a5dc0fd-f64a-4686-9977-1a72b1f05389,0.35,100,128,21
ecf69475-7a8a-40e2-9c5b-9c3cc08b4048,0.22,100,128,21
1fa90349-b6ab-45b8-939c-8f43c4792622,53.39,100,128,21
a4b83ca1-e40d-4862-9a5c-ab54447d4b11,22.02,100,128,22
fb8a76cf-4a93-49b4-88bd-aef94ac1264c,0.51,100,128,20
6cef2e94-40e5-4a79-9174-0478d1ec8a60,43.19,100,128,22
59fb7c06-2b5d-4229-9922-8033609796b9,27.62,100,128,21
5fa9a7a3-86c5-4d6d-9552-c6bebc5e7a2c,27.11,100,128,22
b5dbfeea-4b5c-413d-8853-b07cd173efd7,18.25,100,128,21
fb948cab-7fb2-43a7-9a4f-9ec872fb0e9f,10.18,100,128,22
```
