<?php

namespace Bloom\Permission\Database;

use Bloom\Permission\Contracts\Auth as AuthContract;
use Bloom\Permission\Enum\RoleEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Auth extends Base implements AuthContract
{
    public function check(int $userId, $keys, bool $throwOnDenied = true): bool
    {
        $roleIds = $this->getRoleIds($userId);

        // 超级管理员
        if ($this->isAdminRole($roleIds)) {
            return true;
        }
        $keys = (array)$keys;

        foreach ($this->checkPermissions($keys, $roleIds) as $key => $pass) {
            if (!$pass) {
                Log::debug("check permission denied: ${key}");

                if ($throwOnDenied) {
                    throw new AccessDeniedHttpException("没有权限执行该操作，权限缺失：${key}");
                }

                return false;
            }
        }

        return true;
    }

    public function getKeys(array $roleIds): array
    {
        $keys = DB::connection($this->connection)
            ->table('role_action_permissions as rap')
            ->leftJoin('resource as r', 'r.id', '=', 'rap.resource_id')
            ->whereIn('rap.role_id', $roleIds)
            ->where('r.app_id', $this->getAppId())
            ->pluck('rap.resource_key')
            ->unique()
            ->toArray();

        return $this->response->array($keys);
    }

    public function getDataPermissions(int $userId, int $menuId): array
    {
        $isAdmin = $this->isAdmin($userId);

        if ($isAdmin) {
            return $this->response->array([
                'is_admin' => true,
                'settings' => []
            ]);
        }

        return $this->response->array([
            'is_admin' => false,
            'permissions' => $this->getPackagePermissions($packageId),
            'settings' => $this->getPackageSettings($this->getPackageId($userId), $menuId)
        ]);
    }

    public function isAdmin($userId): bool
    {
        $roleIds = $this->getRoleIds($userId);

        if ($this->isAdminRole($roleIds)) {
            return true;
        }

        return false;
    }

    public function getPackageSettings(int $packageId, int $menuId): array
    {
        $packageSetting = DB::connection($this->connection)
            ->table('package_settings')
            ->where('package_id', $packageId)
            ->where('menu_id', $menuId)
            ->first();

        $packageSetting = (array)$packageSetting;
        $this->multiFieldsToArray($packageSetting);

        $resourceMap = $this->getResourceField(
            [
                $packageSetting['attribute_range_resource_id'],
                $packageSetting['time_range_resource_id'],
                $packageSetting['job_range_resource_id']
            ]);

        $packageSetting['attribute_resource_field'] = $resourceMap[$packageSetting['attribute_range_resource_id']] ?? '';
        $packageSetting['time_resource_field'] = $resourceMap[$packageSetting['time_range_resource_id']] ?? '';
        $packageSetting['job_resource_field'] = $resourceMap[$packageSetting['job_range_resource_id']] ?? '';

        return $packageSetting;
    }

    public function getUserPackageSetting(int $userId, int $menuId): array
    {
        $packageSetting = DB::connection($this->connection)
            ->table('package_settings as ps')
            ->leftJoin('user_packages as up', 'ps.package_id', '=', 'up.package_id')
            ->where('up.user_id', $userId)
            ->where('ps.menu_id', $menuId)
            ->select(['ps.*'])
            ->first();

        $packageSetting = (array)$packageSetting;
        $this->multiFieldsToArray($packageSetting);
        return $packageSetting;
    }

    private function getPackagePermissions($packageId): array
    {
        return DB::connection($this->connection)
            ->table('package_permissions')
            ->where('package_id', $packageId)
            ->pluck('resource_key')
            ->toArray();
    }

    public function getPackageId($userId): int
    {
        return DB::connection($this->connection)
            ->table('user_packages')->where('user_id', $userId)
            ->value('package_id') ?? 0;
    }

    private function isAdminRole($roleIds): bool
    {
        if (in_array(RoleEnum::ADMINISTRATOR_ID, $roleIds)) {
            return true;
        }

        return false;
    }

    private function checkPermissions(array $keys, array $roleIds): array
    {
        $permissions = $this->getKeys($roleIds);

        $results = array_fill_keys($keys, false);

        foreach ($keys as $key) {
            if (in_array($key, $permissions, true)) {
                $results[$key] = true;
            }
        }

        return $results;
    }


    private function getRoleIds($userId): array
    {
        return DB::connection($this->connection)
            ->table('user_roles')->where('user_id', $userId)
            ->pluck('role_id')
            ->toArray();
    }


    private function getAppId()
    {
        return config('permission.app_id');
    }

    private function multiFieldsToArray(&$array): void
    {
        $fields = ['attribute_range_value', 'time_range_value', 'job_range_value'];
        foreach ($fields as $field) {
            if (isset($array[$field])) {
                $array[$field] = $this->jsonToArray($array[$field]);
            }
        }
    }

    private function jsonToArray($json)
    {
        return $json ? json_decode($json, true) : [];
    }

    public function getActionPermissions(int $userId): array
    {
        $roleIds = $this->getRoleIds($userId);
        return $this->getKeys($roleIds);
    }

    public function getResourceField($ids): array
    {
        if (!$ids = array_values(array_unique(array_filter($ids)))) {
            return [];
        }

        return DB::connection($this->connection)
            ->table('resource')
            ->whereIn('id', $ids)
            ->pluck('field', 'id')
            ->toArray();
    }
}
