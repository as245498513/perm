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
     * 是否管理员
     *
     * @param $userId
     * @return bool
     */
    public function isAdmin($userId): bool;

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


    /**
     * 获取数据包菜单设置
     *
     * @param int $packageId
     * @param int $menuId
     * @return array
     */
    public function getPackageSettings(int $packageId, int $menuId): array;

    /**
     * 获取用户数据包菜单设置
     *
     * @param int $userId
     * @param int $menuId
     * @return array
     */
    public function getUserPackageSetting(int $userId, int $menuId): array;

    /**
     * 获取用户数据权限包
     *
     * @param $userId
     * @return int
     */
    public function getPackageId($userId): int;
}
