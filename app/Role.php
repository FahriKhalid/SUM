<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = "ms_role";

    public $timestamp = false;

    protected $guarded = [];
}
