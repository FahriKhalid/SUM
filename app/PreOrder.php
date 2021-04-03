<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class PreOrder extends Model
{
    use SoftDeletes;
    
    protected $table = 'tr_pre_order';
    protected $primaryKey = 'id_pre_order';
    protected $keyType = 'string';
    protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
         
    ]; 

    public function getCreatedAtAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->format('d/m/Y H:i');
    }

    public function getUpdatedAtAttribute($value)
    {
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

    public function Produsen()
    {
        return $this->belongsTo(Produsen::class,'id_produsen','id_produsen')->withDefault();
    }

    public function Status()
    {
        return $this->belongsTo(Status::class,'id_status','id_status')->withDefault();   
    }

    public function Lampiran()
    {
        return $this->hasMany(Lampiran::class,'id_pre_order','id_pre_order');   
    }

    public function Barang()
    {
        return $this->hasMany(Barang::class,'id_pre_order','id_pre_order');   
    }

    public function TotalPembayaranPO()
    {
        return $this->Barang()->sum('nilai');
    }

    public function SKPP()
    {
        return $this->hasOne(SKPP::class, 'id_pre_order', 'id_pre_order')->withDefault([
            'no_skpp' => '-',
            'terakhir_pembayaran' => '-'
        ])->with("Pembayaran");
    }

    public function Pembayaran()
    {
       return $this->SKPP()->with("Pembayaran");
    }
}
