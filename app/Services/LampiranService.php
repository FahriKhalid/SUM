<?php

namespace App\Services;
use Illuminate\Support\Str; 
use App\Lampiran;
use Auth;
use Helper;

class LampiranService 
{

	public function store($request, $id, $kategori){
		try {
			$new_lampiran = [];
        	for ($i=0; $i < count($request->new_file) ; $i++) { 
	            $namafile = 'lampiran-'.Str::random(8).'.'.$request->new_file[$i]->getClientOriginalExtension();
	            if($kategori == "skpp"){
                    $z["id_skpp"] = $id;
                }else{
                    $z["id_pre_order"] = $id;
                }
	            $z["nama"] = $request->new_nama_file[$i];
	            $z["file"] = $namafile;
	            $z["size"]  = $request->new_file[$i]->getSize(); 
	            $z["ekstensi"] = $request->new_file[$i]->getClientOriginalExtension();
	            $z["keterangan"] = $request->new_keterangan_file[$i];
	            $z["created_by"] = Auth::user()->id_user; 
	            $new_lampiran[] = $z; 

	            // upload file
	            $request->new_file[$i]->move('lampiran', $namafile);
	        } 
	       	Lampiran::insert($new_lampiran);
		} catch (\Exception $e) {
			throw new \Exception("Tambah lampiran tidak berhasil ".$e->getMessage(), 1);
		}
	}

	public function update($request){

		try {
			for ($i=0; $i < count($request->nama_file) ; $i++) { 
	        	$id_lampiran = Helper::decodex($request->id_lampiran[$i]);

		        $lampiran = Lampiran::findOrFail($id_lampiran);
		        $lampiran->nama = $request->nama_file[$i];
		        $lampiran->keterangan = $request->keterangan_file[$i];
		        $lampiran->updated_by = Auth::user()->id_user;

		        if(isset($request->file[$i]) && $request->file[$i] != "undefined"){
		            if(file_exists('lampiran/'.$lampiran->file)){
		                unlink('lampiran/'.$lampiran->file);
		            }

		            $namafile = 'lampiran-'.Str::random(8).'.'.$request->file[$i]->getClientOriginalExtension();
		            $lampiran->file = $namafile;
		            $lampiran->size = $request->file[$i]->getSize(); 
		            $lampiran->ekstensi = $request->file[$i]->getClientOriginalExtension();

		            // upload file
		            $request->file[$i]->move('lampiran', $namafile);
		        }

		        $lampiran->save();
		    }
		} catch (\Exception $e) {
			throw new \Exception("Update lampiran tidak berhasil ".$e->getMessage(), 1);
		}
	}

	public function destroy($id, $kategori){
		try {
			$lampiran = Lampiran::where(function($query) use ($id,$kategori){
				if($kategori == "skpp"){
					$query->where("id_skpp", $id);
				}else{
					$query->where("id_pre_order", $id);
				}
			})->get();

			if(count($lampiran) > 0){
				foreach ($lampiran as $item) {
					if(file_exists('lampiran/'.$item->file)){
		                unlink('lampiran/'.$item->file);
		            }
		            $item->delete();
				}
			}
		} catch (\Exception $e) { 
			throw new \Exception("Hapus lampiran tidak berhasil ".$e->getMessage(), 1);
		} 
	}
}
