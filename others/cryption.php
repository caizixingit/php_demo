<?
class cryption {

    function en($str) {
        $ret='';
        $str = base64_encode ($str);
        for ($i=0; $i<=strlen($str)-1; $i++){
            $d_str=substr($str, $i, 1);
            $int =ord($d_str);
            $int=$int^0xAA;
            $hex=strtoupper(dechex($int));
            $ret.=$hex;
        }
        return $ret;
    }

    function de($str) {
        $ret='';
        for ($i=0; $i<=strlen($str)-1; 0){
            $hex=substr($str, $i, 2);
            $dec=hexdec($hex);
            $dec=$dec^0xAA;
            $ret.=chr($dec);
            $i=$i+2;
        }
        return base64_decode($ret);
    }

}
$obj = new cryption();
var_dump($obj->en("''\"<div>sdf</div>"));
var_dump($obj->de("9FE698CD9FCBFD93E3818185DAF9F2C3CDE1C8C3CDE1F3C7E1CD9797"));
