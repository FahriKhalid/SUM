<?php  

namespace App\Helper;
 
use Session;
use Auth;
use DB;
use Route;
use Crypt;
use DateTime;
use Cookie;

class Helper { 

	public static function encodex( $string, $key="", $url_safe=TRUE) {
	    // you may change these values to your own
	    $secret_key = '01200120';
	    $secret_iv = 'setiagung';

	    $output = false;
	    $encrypt_method = "AES-256-CBC";
	    $key = hash( 'sha256', $secret_key );
	    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

	    $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
	    
	    if ($url_safe)
	    {
	        $output = strtr(
	                $output,
	                array(
	                    '+' => '.',
	                    '=' => '-',
	                    '/' => '~'
	                )
	            );
	    }

	    return $output;
	}

	public static function decodex( $string, $key="", $url_safe=TRUE) {
	    // you may change these values to your own
	    $secret_key = '01200120';
	    $secret_iv = 'setiagung';

	    $output = false;
	    $encrypt_method = "AES-256-CBC";
	    $key = hash( 'sha256', $secret_key );
	    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
	    $string = strtr(
	            $string,
	            array(
	                '.' => '+',
	                '-' => '=',
	                '~' => '/'
	            )
	        );
	    $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );

	    return $output;
	}

	public static function dateFormat($date, $boolean, $format)
	{
		if($boolean == true){
			$dateConvert = str_replace('/', '-', $date);
		}else{
			$dateConvert = str_replace('-', '/', $date);
		}
	    
	    $newDate = date($format, strtotime($dateConvert));
	    return $newDate;  
	} 

	public static function decimal($number){
		$number = str_replace('.', '', $number);
		return str_replace(',', '.', $number);
	}

	public static function currency($number) {
		$number = str_replace('.', ',', $number);
	    while (true) {
	        $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1.$2', $number);
	        if ($replaced != $number) {
	            $number = $replaced;
	        } else {
	            break;
	        }
	    }
	    return $number;
	} 

	public static function toFixed($number, $decimals) {
	  return number_format($number, $decimals, '.', "");
	}

	public static function dateIndo($tanggal){ 
    	$startDateArray = explode('-',$tanggal);
    	$mysqlStartDate = $startDateArray[2]." ".Helper::bulan($startDateArray[1])." ".$startDateArray[0];
    	return $mysqlStartDate;
	}

	public static function bulan($bulan){
	    Switch ($bulan){
	        case 1 : $bulan="Januari";
	    Break;
	        case 2 : $bulan="Februari";
	    Break;
	        case 3 : $bulan="Maret";
	    Break;
	        case 4 : $bulan="April";
	    Break;
	        case 5 : $bulan="Mei";
	    Break;
	        case 6 : $bulan="Juni";
	    Break;
	        case 7 : $bulan="Juli";
	    Break;
	        case 8 : $bulan="Agustus";
	    Break;
	        case 9 : $bulan="September";
	    Break;
	        case 10 : $bulan="Oktober";
	    Break;
	        case 11 : $bulan="November";
	    Break;
	        case 12 : $bulan="Desember";
	    Break;
	    }
	            
	    return $bulan;
	}

	public static function integerToRoman($integer)
	{ 
	 	$integer = intval($integer);
	 	$result = '';
	 
	 	$lookup = array('M' => 1000,
	 	'CM' => 900,
	 	'D' => 500,
	 	'CD' => 400,
	 	'C' => 100,
	 	'XC' => 90,
	 	'L' => 50,
	 	'XL' => 40,
	 	'X' => 10,
	 	'IX' => 9,
	 	'V' => 5,
	 	'IV' => 4,
	 	'I' => 1);
	 
	 	foreach($lookup as $roman => $value){
		  	// Determine the number of matches
		  	$matches = intval($integer/$value);
		 
		  	// Add the same number of characters to the string
		  	$result .= str_repeat($roman,$matches);
	 
	  		// Set the integer to be the remainder of the integer and the value
	  		$integer = $integer % $value;
	 	}
	 
		// The Roman numeral should be built, return it
		return $result;
	}

	public static function penyebut($nilai) {
		$nilai = abs($nilai);
		$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = Helper::penyebut($nilai - 10). " belas";
		} else if ($nilai < 100) {
			$temp = Helper::penyebut($nilai/10)." puluh". Helper::penyebut($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " seratus" . Helper::penyebut($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = Helper::penyebut($nilai/100) . " ratus" . Helper::penyebut($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " seribu" . Helper::penyebut($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = Helper::penyebut($nilai/1000) . " ribu" . Helper::penyebut($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = Helper::penyebut($nilai/1000000) . " juta" . Helper::penyebut($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = Helper::penyebut($nilai/1000000000) . " milyar" . Helper::penyebut(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = Helper::penyebut($nilai/1000000000000) . " trilyun" . Helper::penyebut(fmod($nilai,1000000000000));
		}     
		return ucwords($temp);
	}

	public static function profil_perusahaan(){
		return \DB::table("ms_profil_perusahaan")->first();
	}

	public static function menu_expand(){
		return Cookie::get('menu_expand');
	}

	public static function dateWarning($date)
	{ 
		if($date != '-') {
			$terakhir =	strtotime($date);
	    	$now  =	strtotime(date('Y-m-d'));
	    	$beda = $terakhir - $now; 
	    	$bedahari = ($beda/24/60/60);

	    	if($beda > 0){
	    		if($bedahari < 10)
	    		{
	    			return 'bg-warning text-white';
	    		} else {
	    			return '';
	    		}
	    	} else {
	    		return 'bg-red text-white';
	    	}
		} else {
			return null;
		}
 
	}

	public static function jatuhTempo($date)
	{ 
		if($date != '-') {
			$terakhir =	strtotime($date);
	    	$now  =	strtotime(date('Y-m-d'));
	    	$beda = $terakhir - $now; 
	    	$bedahari = ($beda/24/60/60);

	    	if($beda > 0){
	    		return $bedahari;
	    	} else {
	    		return null;
	    	}
		} else {
			return null;
		}
	}


	public static function RemoveSpecialChar($str) 
	{ 
    	$res = str_replace([ '\'', '"', '/' , ':', '*', '?', '<', '>', '|' ], ' ', $str); 
      
    	return $res; 
    } 
}















