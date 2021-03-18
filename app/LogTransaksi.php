<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogTransaksi extends Model
{
    protected $table = "tr_log_transaksi";
    protected $primaryKey = 'id'; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = []; 

    public function getCreatedAtAttribute($value){
        $date = Carbon::parse($value);
        return $date->format('d/m/Y H:i');
    }

    public function getUpdatedAtAttribute($value){
        $date = Carbon::parse($value);
        return $date->format('d/m/Y H:i');
    }
   
    public function CreatedBy()
    {
        return $this->belongsTo(User::class,'created_by','id_user')->withDefault([
            'nama' => '-'
        ]);
    } 

    public function UpdatedBy()
    {
        return $this->belongsTo(User::class,'updated_by','id_user')->withDefault([
            'nama' => '-'
        ]);
    } 
}
