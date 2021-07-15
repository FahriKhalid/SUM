<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SupirSO extends Model
{
    protected $table = 'tr_supir_so';
    protected $primaryKey = 'id_supir_so'; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        //'id_so', 'id_supir', 'is_aktif', 'keterangan', 'created_by', 'updated_by'
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

    public function SO()
    {
    	return $this->belongsTo(SO::class, 'id_so', 'id_so')->withDefault();
    }

    public function Supir()
    {
    	return $this->belongsTo(Supir::class, 'id_supir', 'id_supir')->withDefault()->withTrashed();
    }
}
