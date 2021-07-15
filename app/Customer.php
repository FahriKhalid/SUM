<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'ms_customer';
    protected $primaryKey = 'id_customer';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama', 'email', 'telpon', 'alamat', 'no_npwp', 'perusahaan', 'created_by', 'updated_by'
    ]; 
 
}
