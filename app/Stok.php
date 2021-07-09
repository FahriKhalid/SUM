<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    protected $table = 'tr_stok';
    protected $primaryKey = 'id_stok';
    protected $keyType = 'string';

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

    public function Barang()
    {
        return $this->belongsTo(Barang::class,'id_barang','id_barang')->withDefault();
    }

    public function Produk()
    {
        return $this->belongsTo(Produk::class,'id_produk','id_produk')->withDefault();
    }
}
