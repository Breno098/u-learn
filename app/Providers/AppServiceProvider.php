<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\JsonResource;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment('production')) {
            $this->app['request']->server->set('HTTPS','on');
            URL::forceScheme('https');
        }

        JsonResource::withoutWrapping();

        Relation::enforceMorphMap([
            'exam' => 'App\Models\Exam',
            'lesson' => 'App\Models\Lesson',
        ]);
    }
}
