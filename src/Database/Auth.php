<?php

namespace Bloom\Permission\Database;

use Bloom\Permission\Contracts\Auth as AuthContract;
use Bloom\Permission\Enum\RoleEnum;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
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
        $this->getAppId();

        // todo 只查子系统的权限

        return DB::connection($this->connection)
            ->table('role_action_permissions')
            ->whereIn('role_id', $roleIds)
            ->pluck('resource_key')
            ->unique()
            ->toArray();
    }

    public function getDataPermissions(int $userId, int $menuId): array
    {
        $isAdmin = $this->isAdmin($userId);

        if ($isAdmin) {
            return $this->response->array([
                'is_admin' => true,
                'permissions' => [],
                'settings' => []
            ]);
        }

        $packageId = $this->getPackageId($userId);

        return $this->response->array([
            'is_admin' => false,
            'permissions' => $this->getPackagePermissions($packageId),
            'settings' => $this->getPackageSettings($packageId, $menuId)
        ]);
    }

    private function getPackageSettings($packageId, $menuId): array
    {
        $packageSetting = DB::connection($this->connection)
            ->table('package_settings')
            ->where('package_id', $packageId)
            ->where('menu_id', $menuId)
            ->first();

        $packageSetting = (array)$packageSetting;
        $this->multiFieldsToArray($packageSetting, ['attribute_range_value', 'time_range_value', 'job_range_value']);
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

    private function getPackageId($userId): array
    {
        return DB::connection($this->connection)
            ->table('user_packages')->where('user_id', $userId)
            ->pluck('package_id')
            ->toArray();
    }

    private function isAdmin($userId): bool
    {
        $roleIds = $this->getRoleIds($userId);

        if ($this->isAdminRole($roleIds)) {
            return true;
        }

        return false;
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

    private function multiFieldsToArray(&$array, $fields)
    {
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
}
