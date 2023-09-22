<?php

namespace Bloom\Permission\Contracts;

interface Auth
{
    /**
     * 检测是否具备所有指定的权限 (AND)
     *
     * @param int $userId
     * @param $keys
     * @param bool $throwOnDenied
     * @return bool
     */
    public function check(int $userId, $keys, bool $throwOnDenied = true): bool;

    /**
     * 获取权限资源keys
     *
     * @param array $roleIds
     * @return array
     */
    public function getKeys(array $roleIds): array;

    /**
     * 获取用户角色权限
     *
     * @param int $userId
     * @return array
     */
    public function getActionPermissions(int $userId): array;

    /**
     * 获取用户数据权限
     *
     * @param int $userId
     * @param int $menuId
     * @return array
     */
    public function getDataPermissions(int $userId, int $menuId): array;
}
