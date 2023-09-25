<?php

namespace Bloom\Permission;

use Bloom\Permission\Enum\AttributeRangeTypeEnum;
use Bloom\Permission\Enum\DataRangeTypeEnum;
use Bloom\Permission\Enum\JobRangeTypeEnum;
use Bloom\Permission\Enum\TimeRangeTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class PermissionHelper extends Service
{
    /**
     * @var Builder
     */
    protected $builder;
    protected $userId;
    protected $menuKey;
    protected $packageId;

    public function setBuilder($builder): PermissionHelper
    {
        $this->builder = $builder;
        return $this;
    }

    public function setUser($userId): PermissionHelper
    {
        $this->userId = $userId;
        return $this;
    }

    public function setMenuKey($menuKey): PermissionHelper
    {
        $this->menuKey = $menuKey;
        return $this;
    }

    public function setPackageId($packageId): PermissionHelper
    {
        $this->packageId = $packageId;
        return $this;
    }

    /**
     * 执行
     *
     * @return void
     */
    public function run(): void
    {
        // 1.管理员无需过滤
        if (app('permission.auth')->isAdmin($this->userId)) {
            return;
        }

        if (!$menuId = app('permission.menu')->getId($this->menuKey)) {
            return;
        }

        $setting = app('permission.auth')->getPackageSettings($this->packageId, $menuId);
        // 2.无设置无需过滤
        if (!$setting) {
            return;
        }

        // 3.范围=全部 无需过滤
        if ($setting['data_range_type'] == DataRangeTypeEnum::All) {
            return;
        }

        $this->builder->where(function ($query) use ($setting) {
            // 属性类
            if ($setting['attribute_range_type'] === AttributeRangeTypeEnum::SPECIFY) {
                $query->whereIn($setting['attribute_resource_field'], $setting['attribute_range_value']);
            }

            // 时间范围 绝对范围
            if ($setting['time_range_type'] === TimeRangeTypeEnum::ABSOLUTE) {
                $query->whereBetween($setting['time_resource_field'], $setting['time_range_value']);
            }

            // 时间范围 相对范围
            if ($setting['time_range_type'] === TimeRangeTypeEnum::RELATIVELY) {
                $days = $setting['time_range_value'];
                $today = Carbon::now()->toDateString();
                $start = Carbon::now()->subDays($days)->toDateString();

                $query->whereBetween($setting['time_resource_field'], [$start, $today]);
            }

            // 岗位范围 自己和下级
            if ($setting['job_range_type'] === JobRangeTypeEnum::ONESELF_AND_UNDERLING) {
                $underlingUserIds = app('permission.user')->getUnderlings($this->userId);
                $query->whereIn($setting['job_resource_field'], array_merge($underlingUserIds, [$this->userId]));
            }

            if ($setting['job_range_type'] === JobRangeTypeEnum::SPECIFY) {
                $query->whereIn($setting['job_resource_field'], $setting['job_range_value']);
            }
        });
    }

}
