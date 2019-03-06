<?php namespace Sebastienheyd\Systempay\Tests;

use Sebastienheyd\Systempay\Facade;
use Sebastienheyd\Systempay\SystempayServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Load package service provider
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return Sebastienheyd\Systempay\SystempayServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [
            SystempayServiceProvider::class
        ];
    }

    /**
     * Load package alias
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Systempay' => Facade::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('systempay', [
            'default' => [
                'site_id' => '12345678',
                'key'     => '1122334455667788',
                'env'     => 'TEST',
            ],
            'sha1' => [
                'site_id' => '12345678',
                'key'     => '1122334455667788',
                'env'     => 'TEST',
                'algo'    => 'sha1',
            ]
        ]);
    }
}