<?php

namespace Bloom\Tests;

use Bloom\Permission\Database\Auth;
use Bloom\Permission\Enum\DataRangeTypeEnum;

class AuthTest extends BaseTestCase
{
    public function testGetKeys()
    {
        $result = app('permission.auth')->getKeys([2]);

        dd(DataRangeTypeEnum::PART());
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertNotEmpty($result);
    }

    public function testCheck()
    {
        $result = app('permission.auth')->check(1230058,['supply.purchase_order.view2']);
        $this->assertIsBool($result);
    }

    public function testGetDataPermissions()
    {
        $result = app('permission.auth')->getDataPermissions(1230058,7);

        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertNotEmpty($result);
    }

    public function testGetUserPackageSetting()
    {
        $result = app('permission.auth')->getUserPackageSetting(1230058,7);
    }

    public function testGetResourceField()
    {
        $result = (new Auth())->getResourceField([1,2,0]);

        dd($result);
    }
}
