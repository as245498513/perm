<?php

namespace Bloom\Permission\Contracts;

interface Menu
{
    /**
     * 获取菜单Id
     *
     * @param string $menuKey
     * @return int
     */
    public function getId(string $menuKey): int;
}
