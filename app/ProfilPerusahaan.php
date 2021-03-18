<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfilPerusahaan extends Model
{
    protected $table = "ms_profil_perusahaan";
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $timestamp = false;

}
