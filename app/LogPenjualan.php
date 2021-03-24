<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogPenjualan extends Model
{
    protected $table = "tr_log_penjualan";
    protected $primaryKey = 'id_log';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
         
    ]; 

    public function getCreatedAtAttribute($value){
        $date = Carbon::parse($value);
        return $date->format('d/m/Y H:i');
    }
 
    public function StatusBefore()
    {
        return $this->belongsTo(Status::class,'id_status','id_status')->withDefault();   
    }

    public function StatusTo()
    {
        return $this->belongsTo(Status::class,'id_status','id_status')->withDefault();   
    }

    public function SKPP()
    {
        return $this->belongsTo(SKPP::class,'id_skpp','id_skpp')->withDefault();   
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class,'created_by','id_user')->withDefault([
            'nama' => '-'
        ]);
    }
}
