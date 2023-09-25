<?php

namespace Bloom\Tests;

use App\Enums\Permission\MenuEnum;
use Bloom\Permission\Database\Auth;
use Bloom\Permission\Enum\DataRangeTypeEnum;
use Bloom\Permission\PermissionHelper;

class PermissionHelperTest extends BaseTestCase
{
    public function testRun()
    {
        $packageId = app('permission.auth')->getPackageId(1230058);

//        FilterHelper::instance()
//            ->setBuilder(Null)
//            ->setUser(1230058)
//            ->setMenuKey('purchase_order_middle')
//            ->setPackageId($packageId)
//            ->run();
    }
}
