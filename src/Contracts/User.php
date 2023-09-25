<?php

namespace Bloom\Permission\Contracts;

interface User
{
    /**
     * 获取下属用户Id,包括下属及下属的下属
     *
     * @param int $userId
     * @return array
     */
    public function getUnderlings(int $userId): array;
}
