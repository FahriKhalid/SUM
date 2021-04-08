<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pencarian extends Model
{
    protected $table = "vw_all_dokumen";
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public function CreatedBy()
    {
        return $this->belongsTo(User::class,'created_by','created_by')->withDefault([
            'nama' => '-'
        ]);
    }

}
