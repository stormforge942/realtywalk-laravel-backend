<?php

namespace App\Providers;

use App\Models\Builder;
use App\Models\Polygon;
use App\Models\Property;
use App\Observers\BuilderObserver;
use App\Observers\PolygonObserver;
use App\Observers\PropertyObserver;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (App::environment(['staging', 'production'])) {
            URL::forceScheme('https');
        }

        Builder::observe(BuilderObserver::class);
        Property::observe(PropertyObserver::class);
        Polygon::observe(PolygonObserver::class);
    }
}
