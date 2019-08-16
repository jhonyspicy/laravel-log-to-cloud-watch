<?php
namespace jhonyspicy\LaravelLogToCloudWatch\Tests;

use Exception;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Orchestra\Testbench\TestCase;

class InitialTest extends TestCase
{
    /**
     * @test
     * @throws Exception
     */
    public function my_first_test()
    {
        $logFile = "testapp_local.log";

        $logger = new Logger('TestApp01');
        $formatter = new LineFormatter(null, null, false, true);
        $infoHandler = new StreamHandler(__DIR__."/".$logFile, Logger::INFO);
        $infoHandler->setFormatter($formatter);
        $logger->pushHandler($infoHandler);
        $logger->info('Initial test of application logging.');

        $this->assertTrue(true);
    }
}