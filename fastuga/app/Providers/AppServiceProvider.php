<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
     * Register any application services.
     *
     * @return boolean
     */

    public function boot()
    {
        Validator::extend('calculate', function ($attribute, $value, $parameters) {
            foreach ($parameters as $param) {
                return !((intval($param) >= 10 && floor(intval($param) / 10) == $value) || (intval($param) < 10 && $value == 0));
            }

        }, 'The :attribute must be the floor of total paid divided by 10.');

        Validator::extend('same_as', function ($attribute, $value, $parameters) {
            foreach ($parameters as $param) {
                return intval($param) / 2 != $value;
            }

        }, 'The :attribute must be half of the points used to pay.');
    }
}
