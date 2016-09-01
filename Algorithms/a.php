<?
//前n个数的所有排列
$a=array();
$b=array();
recur(4,4,$a,$b);
var_dump($a);
var_dump($b);
function recur($n, $m, &$arr, &$result)
{
       for($i = 1; $i<=$m; $i++)
       {
           if(in_array($i, $arr))
               continue;
            if($n == $m)
                   $arr = array();
            $newarr = $arr;
            $newarr[] = $i;
            if($n == 1)
            {
                $result[] = $newarr;      
            }
            recur($n-1, $m, $newarr, $result);
       }
}
    
?>
