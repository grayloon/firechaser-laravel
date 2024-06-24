<?php

namespace GrayLoon\FireChaser;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FireChaserServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 */
	public function boot(): void
	{
        $this->app->booted(function () {
            Route::group([
                'namespace' => 'GrayLoon\FireChaser\Http\Controllers',
            ], fn () => $this->loadRoutesFrom(__DIR__.'/Http/routes.php'));
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/firechaser.php' => config_path('firechaser.php'),
            ], 'config');
        }

		AboutCommand::add('FireChaser', 'Version', '1.0.0');
	}

	/**
	 * Register the application services.
	 */
	public function register(): void
	{
		$this->mergeConfigFrom(
			__DIR__.'/config/firechaser.php', 'firechaser'
		);
	}
}
