<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['guest']], function ()
{
	Route::get('', 'AuthController@index')->name('login'); 
	Route::post('', 'AuthController@authenticate');
});


Route::group(['middleware' => ['auth']], function ()
{ 
    Route::group(['prefix' => 'dashboard'], function(){
        Route::get('/', 'DashboardController@index')->name('dashboard'); 
        Route::post('filter', 'DashboardController@filter'); 
    });

    Route::group(['prefix' => 'penjualan'], function(){
        Route::group(['prefix' => 'skpp'], function(){
            Route::get('/', 'SKPP\SkppPenjualanController@index');
            Route::get('data', 'SKPP\SkppPenjualanController@data');
            Route::get('create', 'SKPP\SkppPenjualanController@create');
            Route::post('store', 'SKPP\SkppPenjualanController@store');
            Route::get('edit/{id}', 'SKPP\SkppPenjualanController@edit');
            Route::get('show/{id}', 'SKPP\SkppPenjualanController@show');
            Route::get('preview/{id}', 'SKPP\SkppPenjualanController@preview');
            Route::post('update/{id}', 'SKPP\SkppPenjualanController@update');
            Route::get('confirm/{id}', 'SKPP\SkppPenjualanController@confirm');
            Route::post('revisi/{id}', 'SKPP\SkppPenjualanController@revisi');
            Route::post('approve/{id}', 'SKPP\SkppPenjualanController@approve');
            Route::post('unapprove/{id}', 'SKPP\SkppPenjualanController@unapprove');
            Route::delete('destroy/{id}', 'SKPP\SkppPenjualanController@destroy');
            Route::post('send_email/{id}', 'SKPP\SkppPenjualanController@send_email');
        });

        Route::group(['prefix' => 'pembayaran'], function() { 
            Route::get('/', 'Pembayaran\PembayaranPenjualanController@index');
            Route::get('detail/{id}', 'Pembayaran\PembayaranPenjualanController@detail');
            Route::get('data/{id}', 'Pembayaran\PembayaranPenjualanController@data');
            Route::get('show/{id}', 'Pembayaran\PembayaranPenjualanController@show');
            Route::post('store/{id}', 'Pembayaran\PembayaranPenjualanController@store');
            Route::delete('destroy/{id}/{id_header}', 'Pembayaran\PembayaranPenjualanController@destroy');
        });

        Route::group(['prefix' => 'salesorder'], function() { 
            Route::get('/', 'SalesOrder\SalesOrderPenjualanController@index');
            Route::get('detail/{id}', 'SalesOrder\SalesOrderPenjualanController@detail');
            Route::get('{id}/create', 'SalesOrder\SalesOrderPenjualanController@create');
            Route::post('store/{id}', 'SalesOrder\SalesOrderPenjualanController@store');
            Route::get('data/{id}', 'SalesOrder\SalesOrderPenjualanController@data');
            Route::get('index/{id}', 'SalesOrder\SalesOrderPenjualanController@index');
            Route::get('show/{id}', 'SalesOrder\SalesOrderPenjualanController@show');
            Route::get('edit/{id}', 'SalesOrder\SalesOrderPenjualanController@edit');
            Route::post('update/{id}', 'SalesOrder\SalesOrderPenjualanController@update');
            Route::post('update_status', 'SalesOrder\SalesOrderPenjualanController@update_status');
            Route::delete('destroy/{id}', 'SalesOrder\SalesOrderPenjualanController@destroy'); 
            Route::get('surat_so/{id}', 'SalesOrder\SalesOrderPenjualanController@surat_so'); 
            Route::get('sopo/{id}', 'SalesOrder\SalesOrderPenjualanController@sopo');
            Route::post('send_email/{id}', 'SalesOrder\SalesOrderPenjualanController@send_email'); 
        });

        Route::group(['prefix' => 'invoice'], function() {  
            Route::get('index/{id}', 'Invoice\InvoicePenjualanController@index'); 
            Route::get('create/{id}', 'Invoice\InvoicePenjualanController@create'); 
            Route::post('store/{id}', 'Invoice\InvoicePenjualanController@store'); 
            Route::get('data/{id}', 'Invoice\InvoicePenjualanController@data'); 
            Route::get('show/{id}', 'Invoice\InvoicePenjualanController@show');
            Route::get('edit/{id}', 'Invoice\InvoicePenjualanController@edit');
            Route::post('update/{id}', 'Invoice\InvoicePenjualanController@update');
            Route::delete('destroy/{id}', 'Invoice\InvoicePenjualanController@destroy'); 
            Route::get('surat/{id}', 'Invoice\InvoicePenjualanController@surat');
        }); 
    });
 
    Route::group(['prefix' => 'pembelian'], function() {
        Route::group(['prefix' => 'pre_order'], function() {
            Route::get('/', 'PreOrderController@index');
            Route::get('data', 'PreOrderController@data');
            Route::get('create', 'PreOrderController@create');
            Route::post('store', 'PreOrderController@store');
            Route::get('show/{id}', 'PreOrderController@show');
            Route::get('edit/{id}', 'PreOrderController@edit');
            Route::get('surat_po/{id}', 'PreOrderController@surat_po');
            Route::post('update/{id}', 'PreOrderController@update');
            Route::get('confirm/{id}', 'PreOrderController@confirm');
            Route::post('revisi/{id}', 'PreOrderController@revisi');
            Route::post('approve/{id}', 'PreOrderController@approve');
            Route::delete('destroy/{id}', 'PreOrderController@destroy');
            Route::post('send_email/{id}', 'PreOrderController@send_email'); 
        });

        Route::group(['prefix' => 'skpp'], function(){
            Route::get('show/{id}', 'SKPP\SkppPembelianController@show');
            Route::post('store', 'SKPP\SkppPembelianController@store');
            Route::get('edit/{id}', 'SKPP\SkppPembelianController@edit');
            Route::post('update/{id}', 'SKPP\SkppPembelianController@update');
            Route::get('sisa_pembayaran/{id}', 'SKPP\SkppPembelianController@sisa_pembayaran');
            Route::get('sisa_barang/{id}', 'SKPP\SkppPembelianController@sisa_barang');
        }); 

        Route::group(['prefix' => 'pembayaran'], function() {   
            Route::post('store/{id}', 'Pembayaran\PembayaranPembelianController@store');
            Route::delete('destroy/{id}/{id_header}', 'Pembayaran\PembayaranPembelianController@destroy');
        });

        Route::group(['prefix' => 'salesorder'], function() { 
            Route::get('detail/{id}', 'SalesOrder\SalesOrderPembelianController@detail');
            Route::get('{id}/create', 'SalesOrder\SalesOrderPembelianController@create'); 
            Route::get('data/{id}', 'SalesOrder\SalesOrderPembelianController@data'); 
            Route::get('show/{id}', 'SalesOrder\SalesOrderPembelianController@show');
            Route::get('show_produk/{id}', 'SalesOrder\SalesOrderPembelianController@showProduk');
            Route::get('edit/{id}', 'SalesOrder\SalesOrderPembelianController@edit');
            Route::post('store/{id}', 'SalesOrder\SalesOrderPembelianController@store');
            Route::post('update/{id}', 'SalesOrder\SalesOrderPembelianController@update');
            Route::get('destroy/{id}', 'SalesOrder\SalesOrderPembelianController@destroy');  
        });

        Route::group(['prefix' => 'pengajuan_so'], function(){
            Route::get('data/{id}', 'PengajuanSoController@data');
            Route::get('create/{id}', 'PengajuanSoController@create');
            Route::post('store/{id}', 'PengajuanSoController@store');
            Route::get('edit/{id}', 'PengajuanSoController@edit');
            Route::post('update/{id}', 'PengajuanSoController@update');
            Route::get('detail/{id}', 'PengajuanSoController@detail'); 
            Route::delete('destroy/{id}', 'PengajuanSoController@destroy'); 
            Route::get('barang/{id}', 'PengajuanSoController@barang'); 
            Route::get('surat_pengajuan_so/{id}', 'PengajuanSoController@surat_pengajuan_so'); 
            Route::get('table_view/{id}', 'PengajuanSoController@table_view');
        });

        Route::group(['prefix' => 'invoice'], function() {  
            Route::get('index/{id}', 'Invoice\InvoicePembelianController@index'); 
            Route::get('create/{id}', 'Invoice\InvoicePembelianController@create'); 
            Route::post('store/{id}', 'Invoice\InvoicePembelianController@store'); 
            Route::get('data/{id}', 'Invoice\InvoicePembelianController@data'); 
            Route::get('show/{id}', 'Invoice\InvoicePembelianController@show');
            Route::get('edit/{id}', 'Invoice\InvoicePembelianController@edit');
            Route::post('update/{id}', 'Invoice\InvoicePembelianController@update');
            Route::delete('destroy/{id}', 'Invoice\InvoicePembelianController@destroy'); 
            Route::get('surat/{id}', 'Invoice\InvoicePembelianController@surat');
        }); 
    });
  
    Route::group(['prefix' => 'surat_kuasa'], function() { 
        Route::get('index/{id}', 'SuratKuasaController@index');
        Route::get('detail/{id}', 'SuratKuasaController@detail');
        Route::get('{id}/create', 'SuratKuasaController@create');
        Route::get('data/{id}', 'SuratKuasaController@data');
        Route::get('index/{id}', 'SuratKuasaController@index');
        Route::get('show/{id}', 'SuratKuasaController@show');
        Route::get('edit/{id}', 'SuratKuasaController@edit');
        Route::post('store/{id}', 'SuratKuasaController@store');
        Route::post('update/{id}', 'SuratKuasaController@update');
        Route::delete('destroy/{id}', 'SuratKuasaController@destroy'); 
        Route::get('surat_kuasa/{id}', 'SuratKuasaController@surat_kuasa'); 
        Route::post('send_email/{id}', 'SuratKuasaController@send_email'); 
    });

    
 
    Route::group(['prefix' => 'booking'], function() {   
        Route::get('show/{id}', 'BookingController@show'); 
        Route::get('data/{id}', 'BookingController@data'); 
        Route::post('store/{id}', 'BookingController@store'); 
        Route::get('edit/{id}', 'BookingController@edit'); 
        Route::post('update/{id}', 'BookingController@update'); 
        Route::get('detail/{id}', 'BookingController@detail');
        Route::delete('destroy/{id}', 'BookingController@destroy');
        Route::get('sisa_pembayaran/{id}', 'BookingController@sisa_pembayaran');
        Route::get('sisa_jumlah_barang/{id}', 'BookingController@sisa_barang');
    });
 

    Route::group(['prefix' => 'stok'], function() {   
        Route::get('/', 'StokController@index'); 
        Route::get('data', 'StokController@data'); 
        Route::get('jumlah_stok/{id}', 'StokController@jumlah_stok'); 
    });

    Route::group(['prefix' => 'barang'], function() {   
        Route::delete('destroy/{id}', 'BarangController@destroy'); 
    });
	

    Route::group(['prefix' => 'supirso'], function() {  
        Route::post('switch_supir/{id}', 'SupirSOController@switch');
        Route::get('riwayat_supir/{id}', 'SupirSOController@data');
        Route::delete('destroy/{id}', 'SupirSOController@destroy');
    }); 

    Route::group(['prefix' => 'lampiran'], function() { 
        Route::delete('destroy/{id}', 'LampiranController@destroy');
    });

    Route::group(['prefix' => 'customer'], function() {
        Route::get('/', 'CustomerController@index');
        Route::get('data', 'CustomerController@data');
        Route::get('create', 'CustomerController@create');
        Route::post('store', 'CustomerController@store');
        Route::get('show/{id}', 'CustomerController@show');
        Route::post('update/{id}', 'CustomerController@update');
        Route::delete('destroy/{id}', 'CustomerController@destroy');
    });

    Route::group(['prefix' => 'produsen'], function() {
        Route::get('/', 'ProdusenController@index');
        Route::get('data', 'ProdusenController@data');
        Route::get('create', 'ProdusenController@create');
        Route::post('store', 'ProdusenController@store');
        Route::get('show/{id}', 'ProdusenController@show');
        Route::post('update/{id}', 'ProdusenController@update');
        Route::delete('destroy/{id}', 'ProdusenController@destroy');
    });

    Route::group(['prefix' => 'produk'], function() {
        Route::get('/', 'ProdukController@index');
        Route::get('data', 'ProdukController@data');
        Route::get('create', 'ProdukController@create');
        Route::post('store', 'ProdukController@store');
        Route::get('show/{id}', 'ProdukController@show');
        Route::post('update/{id}', 'ProdukController@update');
        Route::delete('destroy/{id}', 'ProdukController@destroy');
    });

    Route::group(['prefix' => 'gudang'], function() {
        Route::get('/', 'GudangController@index');
        Route::get('data', 'GudangController@data');
        Route::get('create', 'GudangController@create');
        Route::post('store', 'GudangController@store');
        Route::get('show/{id}', 'GudangController@show');
        Route::post('update/{id}', 'GudangController@update');
        Route::delete('destroy/{id}', 'GudangController@destroy');
    });

    Route::group(['prefix' => 'supir'], function() {
        Route::get('/', 'SupirController@index');
        Route::get('data', 'SupirController@data');
        Route::get('create', 'SupirController@create');
        Route::post('store', 'SupirController@store');
        Route::get('show/{id}', 'SupirController@show');
        Route::post('update/{id}', 'SupirController@update');
        Route::delete('destroy/{id}', 'SupirController@destroy');
    });

    Route::group(['prefix' => 'user'], function() {
        Route::get('/', 'UserController@index');
        Route::get('data', 'UserController@data');
        Route::get('create', 'UserController@create');
        Route::post('store', 'UserController@store');
        Route::get('show/{id}', 'UserController@show');
        Route::post('update/{id}', 'UserController@update');
        Route::delete('destroy/{id}', 'UserController@destroy');
    });

    Route::group(['prefix' => 'profil_perusahaan'], function() {
        Route::get('/', 'ProfilPerusahaanController@index');
        Route::get('edit', 'ProfilPerusahaanController@edit');
        Route::post('update', 'ProfilPerusahaanController@update'); 
        Route::delete('atm/destroy/{id}', 'ProfilPerusahaanController@destroy');
    });

    Route::group(['prefix' => 'cookie'], function() {
        Route::get('set', 'CookieController@set'); 
        Route::get('unset', 'CookieController@unset');
    });
    
    Route::get('profil', 'AuthController@profil');
    Route::post('profil/update/{id}', 'AuthController@updateProfil');
    Route::get('logout', 'AuthController@logout');

});