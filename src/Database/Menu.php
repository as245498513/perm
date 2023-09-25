<?php

namespace Bloom\Permission\Database;

use Bloom\Permission\Contracts\Menu as MenuContract;
use Illuminate\Support\Facades\DB;

class Menu extends Base implements MenuContract
{
    public function getId(string $menuKey): int
    {
        return DB::connection($this->connection)
            ->table('admin_menu_front')
            ->where('key', $menuKey)
            ->value('id');
    }
}
