<?php
namespace jhonyspicy\LaravelLogToCloudWatch\Tests;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Exception;
use Maxbanton\Cwh\Handler\CloudWatch;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
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

    /**
     * @test
     * @throws Exception
     */
    public function my_second_test()
    {
        $appName = 'TestApp02';
        $logFile = 'testapp_local.log';
        $facility = 'user';

        $logger = new Logger($appName);

//        $localFormatter = new LineFormatter(null, null, false, true);
        $syslogFormatter = new LineFormatter("%channel%: %level_name%: %message% %context% %extra%",null,false,true);

//        $infoHandler = new StreamHandler(__DIR__."/".$logFile, Logger::INFO);
//        $infoHandler->setFormatter($localFormatter);

        $warnHandler = new SyslogHandler($appName, $facility, Logger::WARNING);
        $warnHandler->setFormatter($syslogFormatter);

//        $logger->pushHandler($infoHandler);
        $logger->pushHandler($warnHandler);

//        $logger->info('Test of PHP application logging.');
        $logger->warn('Test of the warning system logging.');

        $this->assertTrue(true);
    }

    /**
     * AWSの CloudWatch -> ログ からログを確認することができます。
     *
     * @test
     * @throws Exception
     */
    public function cloud_watch()
    {
        $awsCredentials = [
            'region' => 'ap-northeast-1',
            'version' => 'latest',
            'credentials' => [
                'key' => 'xxxxxxxxxxxxxxxxxxxxxx',
                'secret' => 'xxxxxxxxxxxxxxxxxxxxxx',
            ]
        ];

        $cwClient = new CloudWatchLogsClient($awsCredentials);

        $cwHandlerAppNotice = new CloudWatch(
            $cwClient,
            'php-app-logs',
            'TestAuthenticationApp',
            90,
            10000,
            [ 'application' => 'php-testapp01' ],
            Logger::NOTICE
        );

        $logger = new Logger('PHP Logging');

        $formatter = new LineFormatter(null, null, false, true);

        $cwHandlerAppNotice->setFormatter($formatter);

        $logger->pushHandler($cwHandlerAppNotice);

        $logger->notice('This is my test');

        $this->assertTrue(true);
    }
}