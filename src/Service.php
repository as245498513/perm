<?php

namespace Bloom\Permission;

/**
 * 各种服务处理的基类
 */
abstract class Service
{
    /**
     * instance() 方法可以通过单例的方式，创建一个实例
     *
     * @return self
     */
    public static function instance(): self
    {
        $abstract = get_called_class();

        app()->singletonIf($abstract);

        return app()->make($abstract);
    }

    /**
     * make() 方法可以帮助 Services 类快速创建一个新实例
     *
     * 例如： UserService::make(...$parameters)，相当于 new UserService(...$parameters)
     *
     * @param mixed ...$parameters
     *
     * @return Service
     */
    public static function make(...$parameters): self
    {
        $class = get_called_class();

        return new $class(...$parameters);
    }

    /**
     * 使用参数创建一个新的实例
     *
     * @param mixed ...$parameters
     *
     * @return Service
     */
    public function new(...$parameters): self
    {
        return static::make(...$parameters);
    }
}
