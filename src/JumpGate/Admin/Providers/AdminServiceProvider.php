<?php

namespace JumpGate\Admin\Providers;

use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViews();
    }

    /**
     * Register views
     *
     * @return void
     */
    protected function loadViews()
    {
        if ($this->app['config']->get('jumpgate.users.load_views')) {
            $viewPath = __DIR__ . '/../../../views/' . $this->app['config']->get('app.css_framework');

            $this->app['view']->addLocation($viewPath);

            $this->publishes([
                $viewPath . '/admin' => resource_path('views/vendor/admin'),
            ]);
        }
    }
}
