<?php

namespace Bloom\Tests;

class AuthTest extends BaseTestCase
{
    public function testGetKeys()
    {
        $result = app('permission.auth')->getKeys([2]);

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
}
