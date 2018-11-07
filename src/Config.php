<?php

namespace Encore\Admin\Config;

use Encore\Admin\Admin;
use Encore\Admin\Extension;

class Config extends Extension
{
    /**
     * Load configure into laravel from database.
     *
     * @return void
     */
    public static function load()
    {
        $prefix = config('admin.extensions.config.prefix', "");
        if (!empty($prefix)) $prefix .= ".";

        foreach (ConfigModel::all([
            'name',
            'value'
        ]) as $config) {
            if (config($config['name']) == null) {
                config([$prefix . $config['name'] => $config['value']]);
            } else {
                if (config('admin.extensions.config.override_app_config', false)) {
                    config([$prefix . $config['name'] => $config['value']]);
                } else {
                    throw new \Exception("config with name of " . $config['name']
                        . " exist in app config files!, please change name");
                }

            }
        }
    }

    /**
     * Bootstrap this package.
     *
     * @return void
     */
    public static function boot()
    {
        static::registerRoutes();

        Admin::extend('config', __CLASS__);
    }

    /**
     * Register routes for laravel-admin.
     *
     * @return void
     */
    protected static function registerRoutes()
    {
        parent::routes(function ($router) {
            /* @var \Illuminate\Routing\Router $router */
            $router->resource(
                config('admin.extensions.config.name', 'config'),
                config('admin.extensions.config.controller', 'Encore\Admin\Config\ConfigController')
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        parent::createMenu('Config', 'config', 'fa-toggle-on');

        parent::createPermission('Admin Config', 'ext.config', 'config*');
    }
}
