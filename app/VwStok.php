<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VwStok extends Model
{
    protected $table = 'vw_stok';
    protected $primaryKey = 'id_produk'; 
    public $timestamps = false;
}
