<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SKPP extends Model
{
    use SoftDeletes;

    protected $table = 'tr_skpp';
    protected $primaryKey = 'id_skpp';
    protected $keyType = 'string';
    protected $dates = ['deleted_at'];

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

    public function PreOrder()
    {
        return $this->belongsTo(PreOrder::class,'id_pre_order','id_pre_order')->withDefault();
    }

    public function Customer()
    {
        return $this->belongsTo(Customer::class,'id_customer','id_customer')->withDefault();
    } 

    public function Status()
    {
        return $this->belongsTo(Status::class,'id_status','id_status')->withDefault();   
    }

    public function Pembayaran()
    {
        return $this->belongsTo(Pembayaran::class,'id_skpp','id_skpp')->withDefault();
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class,'created_by','id_user')->withDefault();
    }

    public function UpdatedBy()
    {
        return $this->belongsTo(User::class,'updated_by','id_user')->withDefault();
    }

    public function SKPPATM()
    {
        return $this->hasMany(SKPPATM::class,'id_skpp','id_skpp');   
    }

    public function Lampiran()
    {
        return $this->hasMany(Lampiran::class,'id_skpp','id_skpp');   
    }

    public function Barang()
    {
        return $this->hasMany(Barang::class,'id_skpp','id_skpp');   
    }

    public function SO()
    {
        return $this->hasMany(SO::class,'id_skpp','id_skpp');   
    }

    public function TotalPembayaranPO()
    {
        return $this->Barang()->sum('nilai');
    }

    
}
