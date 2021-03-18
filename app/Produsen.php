<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produsen extends Model
{
    protected $table = 'ms_produsen';
    protected $primaryKey = 'id_produsen';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama', 'email', 'perusahaan', 'created_by', 'updated_by'
    ]; 
}
