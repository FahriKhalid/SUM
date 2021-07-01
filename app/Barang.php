<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Barang extends Model
{
    protected $table = 'tr_barang';
    protected $primaryKey = 'id_barang';
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

    public function SKPP()
    {
        return $this->belongsTo(SKPP::class,'id_skpp','id_skpp')->withDefault();
    }

    public function Produk()
    {
        return $this->belongsTo(Produk::class,'id_produk','id_produk')->withDefault();
    }

    public function SOPO()
    {
        return $this->hasMany(SOPO::class, 'id_barang', 'id_barang');
    }

    public function totalKuantitasPO()
    {
        return $this->SOPO()->sum("kuantitas");
    }

    public function Stok()
    {
        return $this->belongsTo(Stok::class, 'id_produk', 'id_produk')->withDefault();
    }
}
