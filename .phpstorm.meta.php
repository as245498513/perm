<?php
// @formatter:off

namespace PHPSTORM_META {
    override(\app(0), map([
        'permission.auth' => \Bloom\Permission\Contracts\Auth::class,
        'permission.user' => \Bloom\Permission\Contracts\User::class,
        'permission.menu' => \Bloom\Permission\Contracts\Menu::class,
    ]));
}
