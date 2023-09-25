<?php

namespace Bloom\Permission;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use RuntimeException;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 配置
        $this->mergeConfigFrom(realpath(__DIR__ . '/../config/permission.php'), 'permission');

        // 数据库配置
        $this->configureDatabase();

        // 数据源
        $this->bindDrivers();

        // 日志 channel 配置
        $this->configureLogging();

        // sql 查询事件监听
        if (!$this->useHttp()) {
            app('events')->listen(QueryExecuted::class, QueryListener::class);
        }
    }

    /**
     * 日志 channel 配置
     */
    protected function configureLogging()
    {
        $channel = \config('permission.logging');

        Config::set('logging.channels.permission-sdk', $channel);
    }

    /**
     * 获取对应驱动的类实例
     *
     * @param string $className
     *
     * @return mixed
     */
    protected function resolve(string $className)
    {
        $driver = \config('permission.driver');
        $namespace = __NAMESPACE__ . '\\' . ucfirst(strtolower($driver)) . '\\';

        $class = $namespace . ucfirst(Str::camel($className));

        if (!class_exists($class)) {
            throw new RuntimeException("类不存在：{$class}");
        }

        return app($class);
    }

    /**
     * 绑定对应驱动实现类到 application 容器
     */
    private function bindDrivers()
    {
        $this->app->bind('permission.auth', function () {
            return $this->resolve('auth');
        });

        $this->app->bind('permission.user', function () {
            return $this->resolve('user');
        });

        $this->app->bind('permission.menu', function () {
            return $this->resolve('menu');
        });
    }

    /**
     * 数据库配置
     */
    private function configureDatabase()
    {
        // 数据库配置
        $connections = config('permission.connections');

        foreach ($connections as $connection => $config) {
            Config::set("database.connections.{$connection}", $config);
        }
    }

    /**
     * 是否使用 http 接口方式
     *
     * @return bool
     */
    private function useHttp(): bool
    {
        return strtolower(\config('permission.driver')) === 'http';
    }
}
