<?php

namespace Netsells\GeoScope\Tests\Unit;

use Netsells\GeoScope\Config\Managers\EloquentBuilderConfigManager;

class ConfigManagerTest extends UnitTestCase
{
    protected $model;
    protected $query;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = $this->getModelMock('TestModel');
        $this->query = $this->getBuilderMock('mysql', $this->model);
    }

    /**
     * @test
     */
    public function gets_basic_model_config()
    {
        $expected = [
            'lat-column' => 'lat',
            'long-column' => 'long',
            'units' => 'kilometers'
        ];

        $this->setGeoScopeConfig($expected, $this->model);

        $actual = $this->getConfigManager()->getConfig();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function gets_nested_model_config()
    {
        $modelConfig = [
            'config1' => [
                'lat-column' => 'lat-column-1',
                'long-column' => 'long-column-1',
                'units' => 'kilometers'
            ],

            'config2' => [
                'lat-column' => 'lat-column-2',
                'long-column' => 'long-column-2',
                'units' => 'meters'
            ],
        ];

        $this->setGeoScopeConfig($modelConfig, $this->model);
        $actual = $this->getConfigManager('config1')->getConfig();

        $this->assertEquals($modelConfig['config1'], $actual);

        $actual2 = $this->getConfigManager('config2')->getConfig();

        $this->assertEquals($modelConfig['config2'], $actual2);
    }

    /**
     * @test
     */
    public function gets_custom_model_config()
    {
        $this->setGeoScopeConfig();

        $expected = [
            'lat-column' => 'lat-column-1',
            'long-column' => 'long-column-1',
            'units' => 'kilometers'
        ];

        $actual = $this->getConfigManager($expected)->getConfig();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function replaces_missing_basic_model_config_values_with_defaults()
    {
        $modelConfig = [
            'lat-column' => 'model-lat-column',
            'long-column' => 'model-long-column',
        ];

        $this->setGeoScopeConfig($modelConfig, $this->model);

        $expected = [
            'lat-column' => $modelConfig['lat-column'],
            'long-column' => $modelConfig['long-column'],
            'units' => config('geoscope.defaults.units')
        ];

        $actual = $this->getConfigManager()->getConfig();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function replaces_missing_nested_model_config_values_with_defaults()
    {
        $modelConfig = [
            'config1' => [
                'lat-column' => 'lat-column-1',
                'long-column' => 'long-column-1',
            ],

            'config2' => [
                'long-column' => 'long-column-2',
                'units' => 'meters'
            ],
        ];

        $this->setGeoScopeConfig($modelConfig, $this->model);

        $expected1 = [
            'lat-column' => $modelConfig['config1']['lat-column'],
            'long-column' => $modelConfig['config1']['long-column'],
            'units' => config('geoscope.defaults.units')
        ];

        $expected2 = [
            'lat-column' => config('geoscope.defaults.lat-column'),
            'long-column' => $modelConfig['config2']['long-column'],
            'units' => $modelConfig['config2']['units'],
        ];

        $actual1 = $this->getConfigManager('config1')->getConfig();
        $actual2 = $this->getConfigManager('config2')->getConfig();

        $this->assertEquals($expected1, $actual1);
        $this->assertEquals($expected2, $actual2);
    }

    /**
     * @test
     */
    public function replaces_missing_custom_config_values_with_defaults()
    {
        $config = [
            'long-column' => 'model-long-column',
        ];

        $this->setGeoScopeConfig();

        $expected = [
            'lat-column' => config('geoscope.defaults.lat-column'),
            'long-column' => $config['long-column'],
            'units' => config('geoscope.defaults.units')
        ];

        $actual = $this->getConfigManager($config)->getConfig();

        $this->assertEquals($expected, $actual);
    }

    private function getConfigManager($configOption = null)
    {
        return app(EloquentBuilderConfigManager::class, [
            'query' => $this->query,
            'configOption' => $configOption
        ]);
    }
}
