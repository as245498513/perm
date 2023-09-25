<?php

namespace Bloom\Permission\Database;

use Bloom\Permission\Contracts\User as UserContract;
use Illuminate\Support\Facades\DB;

class User extends Base implements UserContract
{
    public function getUnderlings(int $userId): array
    {
        $underLingUserIds = DB::connection($this->connection)
            ->table('user')
            ->where('leader_tree', 'like', $userId . '|%')
            ->pluck('id')
            ->toArray();

        return $this->response->array($underLingUserIds);
    }
}
