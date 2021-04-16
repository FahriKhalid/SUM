<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RiwayatEmail extends Model
{
    protected $table = 'ms_riwayat_email';
    protected $primaryKey = 'id_riwayat_email';
    protected $keyType = 'string'; 
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
}
