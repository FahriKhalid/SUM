<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Produk extends Model
{
    use SoftDeletes;

    protected $table = 'ms_produk';
    protected $primaryKey = 'id_produk';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama', 'is_aktif', 'spesifikasi', 'created_by', 'updated_by'
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

    public function Stok()
    {
        return $this->belongsTo(Stok::class, 'id_produk', 'id_produk')->withDefault();
    }
}
