<?php

namespace Eloquent\ImageAble;

use Illuminate\Support\ServiceProvider;

class ImageAbleServiceProvider extends ServiceProvider {

    /**
     * Publish resources.
     */
    public function boot() {
        $this->publishes([
            __DIR__ . DIRECTORY_SEPARATOR . '../migrations/' => database_path('migrations'),
        ], 'migrations');
    }

    public function register() { }

}