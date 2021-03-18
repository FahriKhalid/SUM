<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SOPO extends Model
{
    use SoftDeletes;

    protected $table = 'tr_so_po';
    protected $primaryKey = 'id_so_po';
    protected $keyType = 'string';
    protected $dates = ['deleted_at'];

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

    public function Barang()
    {
        return $this->belongsTo(Barang::class,'id_barang','id_barang')->withDefault();
    }

    public function SO()
    {
        return $this->belongsTo(SO::class,'id_so','id_so')->withDefault();
    } 

    
}
