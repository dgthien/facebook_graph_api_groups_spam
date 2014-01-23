<?php
function find_all_files($dir)
{
    $root = scandir($dir);
    foreach($root as $value)
    {
        if($value === '.' || $value === '..') {continue;}
        if(is_file("$dir/$value")) {$result[]="$dir/$value";continue;}
        foreach(find_all_files("$dir/$value") as $value)
        {
            $result[]=$value;
        }
    }
    return $result;
} 


function getImage($dir)
{
    $root = scandir($dir);
    $result = '';
    foreach($root as $value)
        if(is_file("$dir/$value")) {
        	$result="$dir/$value";
        	break;
        }
    return $result;
} 

function multiCurl($res) {
	if(count($res)<=0) return False;

    $handles = array();

    foreach($res as $k=>$row){
        $ch{$k} = curl_init();
        $options[CURLOPT_URL] = $row['url'];
        @curl_setopt_array($ch{$k}, $row['option']);
        $handles[$k] = $ch{$k};
    }

    $mh = curl_multi_init();

    foreach($handles as $k => $handle){
        curl_multi_add_handle($mh,$handle);
    }

    $running = null;
    do{
            curl_multi_exec($mh,$running);
    }while($running > 0);

    foreach($res as $k=>$row){
        $res[$k]['error'] = curl_error($handles[$k]);
        if(!empty($res[$k]['error']))
            $res[$k]['data']  = '';
        else
            $res[$k]['data']  = curl_multi_getcontent( $handles[$k] );  // get results

        curl_multi_remove_handle($mh, $handles[$k] );
    }
    curl_multi_close($mh);
    return $res; 
}