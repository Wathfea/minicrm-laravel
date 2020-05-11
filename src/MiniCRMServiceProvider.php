<?php


namespace MiniCRMLaravel;


use Illuminate\Support\ServiceProvider;

/**
 * Class MiniCRMServiceProvider
 *
 * @package MiniCRMLaravel
 */
class MiniCRMServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{

	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		if ( !defined('MINICRM_PATH')) {
			define('MINICRM_PATH', realpath(__DIR__.'/../'));
		}

		$this->configure();
		$this->offerPublishing();
	}

	/**
	 * Setup the configuration for MiniCRM.
	 *
	 * @return void
	 */
	protected function configure()
	{
		// Load the default config values
		$this->mergeConfigFrom(
			__DIR__.'/../config/minicrm.php', 'minicrm'
		);
	}

	/**
	 * Setup the resource publishing groups for Horizon.
	 *
	 * @return void
	 */
	protected function offerPublishing()
	{
		if ($this->app->runningInConsole()) {

			$this->publishes([
				__DIR__.'/../config/minicrm.php' => config_path('minicrm.php'),
			], 'minicrm-config');
		}
	}
}
