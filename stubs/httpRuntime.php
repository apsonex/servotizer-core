<?php

use Apsonex\ServotizerCore\Runtime\HttpHandlerFactory;
use Apsonex\ServotizerCore\Runtime\LambdaContainer;
use Apsonex\ServotizerCore\Runtime\LambdaRuntime;
use Apsonex\ServotizerCore\Runtime\Secrets;

ini_set('display_errors', '1');

error_reporting(E_ALL);

/*
|--------------------------------------------------------------------------
| Inject SSM Secrets Into Environment
|--------------------------------------------------------------------------
|
| Next, we will inject any of the application's secrets stored in AWS
| SSM into the environment variables. These variables may be a bit
| larger than the variables allowed by Lambda which has a limit.
|
*/

Secrets::addToEnvironment(
    $_ENV['SERVOTIZER_SSM_PATH'],
    json_decode($_ENV['SERVOTIZER_SSM_VARIABLES'] ?? '[]', true),
    __DIR__.'/vaporSecrets.php'
);

/*
|--------------------------------------------------------------------------
| Listen For Lambda Invocations
|--------------------------------------------------------------------------
|
| When using FPM, we will listen for Lambda invocations and proxy them
| through the FPM process. We'll then return formatted FPM response
| back to the user. We'll monitor FPM to make sure it is running.
|
*/

$invocations = 0;

$lambdaRuntime = LambdaRuntime::fromEnvironmentVariable();

while (true) {
    $lambdaRuntime->nextInvocation(function ($invocationId, $event) {
        return HttpHandlerFactory::make($event)
                    ->handle($event)
                    ->toApiGatewayFormat();
    });

    LambdaContainer::terminateIfInvocationLimitHasBeenReached(
        ++$invocations, (int) ($_ENV['SERVOTIZER_MAX_REQUESTS'] ?? 250)
    );
}
