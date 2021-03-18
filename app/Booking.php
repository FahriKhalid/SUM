<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    protected $table = 'tr_booking';
    protected $primaryKey = 'id_booking';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_pre_order', 'no_booking', 'terakhir_pembayaran', 'total_pembayaran', 'no_skpp', 'file_skpp', 'id_pre_order', 'updated_by'
    ]; 

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

    public function PreOrder()
    {
        return $this->belongsTo(PreOrder::class,'id_pre_order','id_pre_order')->withDefault();
    }
}
