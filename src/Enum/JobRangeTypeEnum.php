<?php

namespace Bloom\Permission\Enum;

/**
 * 数据权限设置:岗位范围
 */
class JobRangeTypeEnum
{
    /**
     * @var 全部
     */
    const All = 0;

    /**
     * @var 自己和下级
     */
    const ONESELF_AND_UNDERLING = 1;

    /**
     * @var 指定岗位人员
     */
    const SPECIFY = 2;


}
