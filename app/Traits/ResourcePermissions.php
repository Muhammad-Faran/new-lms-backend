<?php

namespace App\Traits;

trait ResourcePermissions
{
    public function __construct()
    {
        if (empty($this->permission_key)) {
            abort(500, 'No permission key set while using ResourcePermissions trait');
        }

        $this->middleware("can:view-{$this->permission_key}");
        $this->middleware("can:create-{$this->permission_key}")->only('store');
        $this->middleware("can:update-{$this->permission_key}")->only('update');
        $this->middleware("can:delete-{$this->permission_key}")->only('delete', 'destroy');
    }
}
