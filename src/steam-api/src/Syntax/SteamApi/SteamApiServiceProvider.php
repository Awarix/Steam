<?php namespace Syntax\SteamApi;

use Illuminate\Support\ServiceProvider;

class SteamApiServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('syntax/steam-api');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerAlias();

		$this->app['steam-api'] = $this->app->share(function($app)
		{
			return new Client;
		});
	}

	/**
	 * Register the alias for package.
	 *
	 * @return void
	 */
	protected function registerAlias()
	{
		$this->app->booting(function()
		{
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('Steam', 'Syntax\SteamApi\Facades\SteamApi');
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('steam-api');
	}

}