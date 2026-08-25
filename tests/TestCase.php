<?php

namespace Devaspid\WhatsappGateway\Tests;

use Devaspid\WhatsappGateway\WhatsappServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [WhatsappServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Whatsapp' => \Devaspid\WhatsappGateway\Facades\Whatsapp::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('whatsapp-gateway.base_url', 'https://wa-gateway.test/api/v1');
        $app['config']->set('whatsapp-gateway.client_id', 'test-client-id');
        $app['config']->set('whatsapp-gateway.api_key', 'test-api-key');
        $app['config']->set('whatsapp-gateway.default_device_id', null);
        $app['config']->set('whatsapp-gateway.timeout', 5);
        $app['config']->set('whatsapp-gateway.retry.times', 0);
        $app['config']->set('whatsapp-gateway.retry.sleep', 0);
    }
}
