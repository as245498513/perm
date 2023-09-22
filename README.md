# PERMISSION SDK

## 安装

```
composer require php-package/permission-helper
```

## 配置

* 在 `bootstrap/app.php` 添加以下一行：

```
$app->register(Bloom\Permission\PermissionServiceProvider::class);
```

* 在 `config` 目录添加 `permission.php`，内容如下：

```
<?php

return [
    // sdk 包名
    'name' => 'permission',

    // 应用 Id
    'app_id' => env('APP_ID', ''),

    // 数据源
    'driver' => env('PERMISSION_DRIVER', 'database'),

    // http 配置
    'http' => [
        // basic 认证用户名密码
        'api_key'  => env('PERMISSION_HTTP_USER', '66fd1d82b9a9444a675335906ed20f1d'),
        'password' => env('PERMISSION_HTTP_PASS', 'e405af04d4f96f595b75485fead13004'),

        // 接口域名
        'base_uri' => env('PERMISSION_BASE_URI', ''),
    ],

    // 数据库连接配置
    'connections' => [
        'permission' => [
            'driver' => 'mysql',
            'host' => env('PERMISSION_DB_HOST', '127.0.0.1'),
            'port' => env('PERMISSION_DB_PORT', 3306),
            'database' => env('PERMISSION_DB_DATABASE', 'test'),
            'username' => env('PERMISSION_DB_USERNAME', 'root'),
            'password' => env('PERMISSION_DB_PASSWORD', 'abc123'),
            'unix_socket' => env('PERMISSION_DB_SOCKET', ''),
            'charset' => env('PERMISSION_DB_CHARSET', 'utf8mb4'),
            'collation' => env('PERMISSION_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => env('PERMISSION_DB_PREFIX', ''),
            'strict' => env('PERMISSION_DB_STRICT_MODE', true),
            'engine' => env('PERMISSION_DB_ENGINE', null),
            'timezone' => env('PERMISSION_DB_TIMEZONE', '+00:00'),
        ],
    ],

    // 是否记录 sql 查询
    'log_query' => env('PERMISSION_LOG_QUERY', false),

    // 日志 channel 配置
    'logging' => [
        'driver' => 'daily',
        'path' => storage_path("logs/permission-sdk.log"),
        'level' => env('LOG_DEFAULT_LEVEL', 'debug'),
        'days' => 14,
    ],
];

```

* 在 `env` 添加相关的配置：
    * 使用 HTTP 数据源需要配置用户名和密码
    * 使用数据库作为数据源需要配置数据库连接信息


## 使用

* 鉴权

```
$userId = 1230058;
$keys = ['supply.purchase_order.view'];
app('permission.auth')->check($userId,$keys);
```

* 获取数据权限

```
$userId = 1230058;
$menuId = 7;
$result = app('permission.auth')->getDataPermissions($userId,$menuId);
```

* 获取角色权限

```
$roleIds = [7];
app('permission.auth')->getKeys($roleIds);
```


## 记录 sql

添加以下 `env` 配置：

```
PERMISSION_LOG_QUERY=true
```

记录的 sql 中的 binding 参数会被替换为具体的参数，而不是 "?"。

## 设计思想

通过配置的 `driver` 不同，将不同的实现类绑定到 app 容器，比如，如果想通过从库来获取数据，`app('permission.auth'')` 获取到的是 `\Bloom\Permission\Database\Auth`，
而如果配置的 `driver` 是 `http`，则 `app('permission.auth')` 获取到的是 `\Bloom\Permission\Http\Auth`。这两个类都实现了 `\Bloom\Permission\Contracts\Auth` Contract，
以便后续可以在两种不同数据访问方式之间切换。


## 关于 .phpstorm.meta.php

这个文件可以让 phpstorm 提供一些容器相关实例的语法提示。比如，我们绑定了一个实例：

```
app()->bind('permission.auth', function() {
    return new Bloom\Permission\Database\Auth;
});
```

一般情况下，在 phpstorm 里面敲完 `app('permission.auth')` 之后是不会有语法提示的。但是我们可以通过 `.phpstorm.meta.php` 来让它给出语法补全的提示：

只需在 .phpstorm.meta.php 里面加上下面几行，输入 `app('permission.auth')` 之后会有 `Bloom\Permission\Contracts\Auth` 类相关的方法补全提示。

```
override(\app(0), map([
    'permission.auth' => \Bloom\Permission\Contracts\Auth::class,
]));
```
