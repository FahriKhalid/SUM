<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Gudang extends Model
{
    use SoftDeletes;
    
    protected $table = 'ms_gudang';
    protected $primaryKey = 'id_gudang';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama', 'is_aktif', 'created_by', 'updated_by'
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

    public function Produsen()
    {
        return $this->belongsTo(Produsen::class, 'id_produsen', 'id_produsen')->withDefault()->withTrashed();
    }
}
