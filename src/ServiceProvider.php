<?php

namespace Silverd\Encryptable;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->app->singleton('encryption', function ($app) {
            return new Encrypter(config('encrypt'));
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->commands([
                Commands\EncryptModel::class,
                Commands\DecryptModel::class
            ]);

            $this->publishes([__DIR__.'/../config' => config_path()], 'config');
        }

        $this->bootValidators();
    }

    private function bootValidators()
    {
        $counts = function ($table, $column, $value) {
            $value = app('encryption')->encrypt($value);
            return \DB::table($table)->where($column, $value)->count();
        };

        \Validator::extend('exists_encrypted', function ($attribute, $value, $parameters, $validator) use ($counts) {
            [$table, $column] = $parameters;
            return $counts($table, $column, $value) > 0;
        });

        \Validator::extend('unique_encrypted', function ($attribute, $value, $parameters, $validator) use ($counts) {
            [$table, $column] = $parameters;
            return $counts($table, $column, $value) == 0;
        });
    }
}
