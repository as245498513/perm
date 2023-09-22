<?php

namespace Bloom\Permission\Database;

use Bloom\Permission\Response;

class Base
{
    /**
     * 数据库连接
     *
     * @var string
     */
    protected $connection = 'permission';

    /**
     * @var Response
     */
    protected $response;

    public function __construct()
    {
        $this->response = app(Response::class);
    }
}
