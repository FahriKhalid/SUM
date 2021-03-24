<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesOrderPembelian extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_skpp'               => 'required',
            'nomor_so'              => 'required|unique:tr_so,no_so,NULL,id_so,deleted_at,NULL', 
            'id_barang'             => 'required|array',
            'id_barang.*'           => 'required|distinct',  
            // 'kuantitas'             => 'required|array',
            // 'kuantitas.*'           => 'required|numeric|min:1',
            'id_pre_order'          => 'required',
            'file'                  => 'required|max:2000|mimes:pdf',
            //'status'                => 'required|in:1,8',
        ];
    }

    public function messages()
    {
        return [
            'nomor_so.required'     => 'Nomor sales order wajib diisi', 
            'nomor_so.unique'       => 'Nomor sales order sudah pernah terdaftar pilih nomor sales order yang lain',
            'supir.required'        => 'Penanggung jawab wajib diisi', 
            'status.required'       => 'Status wajib diisi', 
            'supir.exist'           => 'Penanggung jawab tidak valid', 
            'tujuan.required'       => 'Tujuan wajib diisi', 
            'id_barang.required'    => 'Produk wajib diisi', 
            'kuantitas.required'    => 'kuantitas wajib diisi',
            'kuantitas.*.min'          => 'Kuantitas tidak boleh 0',
            'file.required'      => 'File sales order wajib diisi',
            'file.max'           => 'Ukuran file sales order terlalu besar. Maks 2 Mb',
            'file.mimes'         => 'Ekstensi file sales order tidak valid'

        ];
    }
}
