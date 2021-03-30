<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SuratKuasa extends Model
{
    protected $table = 'tr_sk';
    protected $primaryKey = 'id_sk'; 
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

    public function SO(){
        return $this->belongsTo(SO::class,'id_so','id_so')->withDefault();  
    }

    public function Status()
    {
        return $this->belongsTo(Status::class,'id_status','id_status')->withDefault();   
    }
 
    public function Supir()
    {
        return $this->belongsTo(Supir::class,'id_supir','id_supir')->withDefault();
    }

    public function Gudang()
    {
        return $this->belongsTo(Gudang::class,'id_gudang','id_gudang')->withDefault();
    } 

    public function SKSO()
    {
        return $this->hasMany(SKSO::class,'id_sk','id_sk');
    }

    public function totalKuantitasPO()
    {
        return $this->SKSO()->sum("kuantitas");
    }
}
