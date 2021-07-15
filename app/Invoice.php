<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Invoice extends Model
{
    protected $table = "tr_invoice";
    protected $primaryKey = 'id_invoice';
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

    public function SKPP()
    {
        return $this->belongsTo(SKPP::class,'id_skpp','id_skpp')->withDefault()->withTrashed();
    }

    public function SO()
    {
        return $this->belongsTo(SO::class,'id_so','id_so')->withDefault();
    }
}
