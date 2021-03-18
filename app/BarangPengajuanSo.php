<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BarangPengajuanSo extends Model
{
    protected $table = 'tr_barang_pengajuan_so';
    protected $primaryKey = 'id_barang_pengajuan_so';
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

    public function Barang()
    {
        return $this->belongsTo(Barang::class,'id_barang','id_barang')->withDefault();
    }

    public function Produk()
    {
        return $this->belongsTo(Produk::class,'id_produk','id_produk')->withDefault();
    }
}
