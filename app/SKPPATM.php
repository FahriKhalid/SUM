<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SKPPATM extends Model
{
    protected $table = 'tr_skpp_atm';
    protected $primaryKey = 'id_skpp_atm'; 
    public $timestamp = false;

    public function ATM()
    {
        return $this->belongsTo(ATM::class,'id_atm','id_atm')->withDefault([
            'nama' => '-'
        ]);
    }
}
