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
     * @test
     * @throws Exception
     */
    public function cloud_watch()
    {
        $logFile = "testapp_local.log";
        $appName = "TestApp01";
        $facility = "local0";

// Get instance ID:
//        $url = "http://169.254.169.254/latest/meta-data/instance-id";
//        $instanceId = file_get_contents($url);

        $awsCredentials = [
            'region' => 'ap-northeast-1',
            'version' => 'latest',
            'credentials' => [
                'key' => 'xxxxxxxxxxxxxxxxxxxxxx',
                'secret' => 'xxxxxxxxxxxxxxxxxxxxxx',
            ]
        ];

        $cwClient = new CloudWatchLogsClient($awsCredentials);
// Log group name, will be created if none
        $cwGroupName = 'php-app-logs';
// Log stream name, will be created if none
//        $cwStreamNameInstance = $instanceId;
// Instance ID as log stream name
        $cwStreamNameApp = "TestAuthenticationApp";
// Days to keep logs, 14 by default
        $cwRetentionDays = 90;

//        $cwHandlerInstanceNotice = new CloudWatch($cwClient, $cwGroupName, $cwStreamNameInstance, $cwRetentionDays, 10000, [ 'application' => 'php-testapp01' ],Logger::NOTICE);
//        $cwHandlerInstanceError = new CloudWatch($cwClient, $cwGroupName, $cwStreamNameInstance, $cwRetentionDays, 10000, [ 'application' => 'php-testapp01' ],Logger::ERROR);
        $cwHandlerAppNotice = new CloudWatch($cwClient, $cwGroupName, $cwStreamNameApp, $cwRetentionDays, 10000, [ 'application' => 'php-testapp01' ],Logger::NOTICE);

        $logger = new Logger('PHP Logging');

        $formatter = new LineFormatter(null, null, false, true);
        $syslogFormatter = new LineFormatter("%channel%: %level_name%: %message% %context% %extra%",null,false,true);
        $infoHandler = new StreamHandler(__DIR__."/".$logFile, Logger::INFO);
        $infoHandler->setFormatter($formatter);

        $warnHandler = new SyslogHandler($appName, $facility, Logger::WARNING);
        $warnHandler->setFormatter($syslogFormatter);

//        $cwHandlerInstanceNotice->setFormatter($formatter);
//        $cwHandlerInstanceError->setFormatter($formatter);
        $cwHandlerAppNotice->setFormatter($formatter);

        $logger->pushHandler($warnHandler);
        $logger->pushHandler($infoHandler);
//        $logger->pushHandler($cwHandlerInstanceNotice);
//        $logger->pushHandler($cwHandlerInstanceError);
        $logger->pushHandler($cwHandlerAppNotice);

        $logger->info('Initial test of application logging.');
        $logger->warn('Test of the warning system logging.');
        $logger->notice('Application Auth Event: ',[ 'function'=>'login-action','result'=>'login-success' ]);
//        $logger->notice('Application Auth Event: ',[ 'function'=>'login-action','result'=>'login-failure' ]);
//        $logger->error('Application ERROR: System Error');$logFile = "testapp_local.log";
//        $appName = "TestApp01";
//        $facility = "local0";
//
//// Get instance ID:
//        $url = "http://169.254.169.254/latest/meta-data/instance-id";
//        $instanceId = file_get_contents($url);
//
//        $cwClient = new CloudWatchLogsClient($awsCredentials);
//// Log group name, will be created if none
//        $cwGroupName = 'php-app-logs';
//// Log stream name, will be created if none
//        $cwStreamNameInstance = $instanceId;
//// Instance ID as log stream name
//        $cwStreamNameApp = "TestAuthenticationApp";
//// Days to keep logs, 14 by default
//        $cwRetentionDays = 90;
//
//        $cwHandlerInstanceNotice = new CloudWatch($cwClient, $cwGroupName, $cwStreamNameInstance, $cwRetentionDays, 10000, [ 'application' => 'php-testapp01' ],Logger::NOTICE);
//        $cwHandlerInstanceError = new CloudWatch($cwClient, $cwGroupName, $cwStreamNameInstance, $cwRetentionDays, 10000, [ 'application' => 'php-testapp01' ],Logger::ERROR);
//        $cwHandlerAppNotice = new CloudWatch($cwClient, $cwGroupName, $cwStreamNameApp, $cwRetentionDays, 10000, [ 'application' => 'php-testapp01' ],Logger::NOTICE);
//
//        $logger = new Logger('PHP Logging');
//
//        $formatter = new LineFormatter(null, null, false, true);
//        $syslogFormatter = new LineFormatter("%channel%: %level_name%: %message% %context% %extra%",null,false,true);
//        $infoHandler = new StreamHandler(__DIR__."/".$logFile, Logger::INFO);
//        $infoHandler->setFormatter($formatter);
//
//        $warnHandler = new SyslogHandler($appName, $facility, Logger::WARNING);
//        $warnHandler->setFormatter($syslogFormatter);
//
//        $cwHandlerInstanceNotice->setFormatter($formatter);
//        $cwHandlerInstanceError->setFormatter($formatter);
//        $cwHandlerAppNotice->setFormatter($formatter);
//
//        $logger->pushHandler($warnHandler);
//        $logger->pushHandler($infoHandler);
//        $logger->pushHandler($cwHandlerInstanceNotice);
//        $logger->pushHandler($cwHandlerInstanceError);
//        $logger->pushHandler($cwHandlerAppNotice);
//
//        $logger->info('Initial test of application logging.');
//        $logger->warn('Test of the warning system logging.');
//        $logger->notice('Application Auth Event: ',[ 'function'=>'login-action','result'=>'login-success' ]);
//        $logger->notice('Application Auth Event: ',[ 'function'=>'login-action','result'=>'login-failure' ]);
//        $logger->error('Application ERROR: System Error');

        $this->assertTrue(true);
    }
}