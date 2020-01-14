<?php

namespace Netsells\GeoScope\Tests\Unit;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\MySqlBuilder;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Netsells\GeoScope\BuilderScopes\EloquentBuilderScope;
use Netsells\GeoScope\Tests\TestCase;

abstract class UnitTestCase extends TestCase
{
    /**
     * @param string $modelName
     * @param string $driver
     * @param array|null $config
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    protected function getGeoScopeMock(string $modelName, string $driver, array $config = null)
    {
        $model = $this->getModelMock($modelName);

        if ($config) {
            $this->setGeoScopeConfig($config, get_class($model));
        }

        $query = $this->getBuilderMock($driver, $model);

        return app(EloquentBuilderScope::class, [
            'query' => $query,
        ]);
    }

    /**
     * @param string $driver
     * @param $model
     * @return Builder|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected function getBuilderMock(string $driver, $model)
    {
        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('getModel')
            ->andReturn($model);

        $query->shouldReceive('whereRaw')
            ->andReturn($query);

        $query->shouldReceive('orWhereRaw')
            ->andReturn($query);

        $connectionMock = Mockery::mock(ConnectionInterface::class);
        $connectionMock->shouldReceive('getConfig')
            ->andReturn($driver);

        $query->shouldReceive('getConnection')
            ->andReturn($connectionMock);

        return $query;
    }

    /**
     * @param $modelName
     * @return Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected function getModelMock($modelName)
    {
        $mock = Mockery::mock($modelName, Model::class);

        $mock->shouldReceive('getTable')
            ->andReturn('test_table');

        return $mock;
    }

    protected function setMySqlBuilderMock()
    {
        Mockery::mock(MySqlBuilder::class)
            ->shouldReceive('hasColumn')
            ->andReturnTrue();
    }
}
