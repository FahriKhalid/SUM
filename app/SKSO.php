<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SKSO extends Model
{
    protected $table = 'tr_sk_so';
    protected $primaryKey = 'id_sk_so';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $filable = ['id_sk', 'id_so_po', 'kuantitas', 'created_by', 'updated_by']; 

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

    public function SOPO()
    {
        return $this->belongsTo(SOPO::class,'id_so_po','id_so_po')->withDefault();
    }
 
}
