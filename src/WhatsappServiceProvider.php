<?php

namespace Devaspid\WhatsappGateway;

use Devaspid\WhatsappGateway\Channels\WhatsappChannel;
use Devaspid\WhatsappGateway\Contracts\WhatsappGatewayInterface;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class WhatsappServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/whatsapp-gateway.php',
            'whatsapp-gateway'
        );

        // Register WhatsappGateway sebagai singleton
        $this->app->singleton(WhatsappGateway::class, function ($app) {
            return new WhatsappGateway(
                config: $app['config']->get('whatsapp-gateway'),
            );
        });

        // Bind interface ke implementasi
        $this->app->alias(WhatsappGateway::class, WhatsappGatewayInterface::class);

        // Register WhatsappChannel
        $this->app->singleton(WhatsappChannel::class, function ($app) {
            return new WhatsappChannel(
                $app->make(WhatsappGateway::class),
            );
        });
    }

    public function boot(): void
    {
        // Publish config file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/whatsapp-gateway.php' => config_path('whatsapp-gateway.php'),
            ], 'whatsapp-gateway-config');
        }

        // Register notification channel
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('whatsapp', function ($app) {
                return $app->make(WhatsappChannel::class);
            });
        });
    }
}
