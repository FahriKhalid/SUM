<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PengajuanSo extends Model
{
   	protected $table = 'tr_pengajuan_so';
    protected $primaryKey = 'id_pengajuan_so';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = []; 

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

    public function PreOrder()
    {
        return $this->belongsTo(PreOrder::class, 'id_pre_order', 'id_pre_order');
    }

    public function BarangPengajuanSo()
    {
        return $this->hasMany(BarangPengajuanSo::class,'id_pengajuan_so','id_pengajuan_so');   
    }
}
