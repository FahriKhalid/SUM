<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SO extends Model
{
    use SoftDeletes;

    protected $table = 'tr_so';
    protected $primaryKey = 'id_so'; 
    protected $keyType = 'string';
    protected $dates = ['deleted_at'];

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


    public function Status()
    {
        return $this->belongsTo(STatus::class,'id_status','id_status')->withDefault([
            "status" => "-"
        ]);   
    }

    public function SKPP()
    {
        return $this->belongsTo(SKPP::class,'id_skpp','id_skpp')->withDefault();
    }

    public function Supir()
    {
        return $this->belongsTo(Supir::class,'id_supir','id_supir')->withDefault();
    }

    public function SupirSO()
    {
        return $this->hasMany(SupirSO::class,'id_so','id_so');
    }

    public function SupirAktif()
    { 
        return $this->SupirSO()->where('is_aktif', 1);
    }

    public function SOPO()
    {
        return $this->hasMany(SOPO::class, 'id_so', 'id_so');
    }

    public function totalKuantitasPO()
    {
        return $this->SOPO()->sum("kuantitas");
    }

    public function Invoice()
    {
        return $this->hasOne(Invoice::class, 'id_so', 'id_so');
    }
}
