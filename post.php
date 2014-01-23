<?php
include('functions.php');

header('Content-Type: text/HTML; charset=utf-8');
header( 'Content-Encoding: none; ' );
ob_end_flush();
ob_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);
set_time_limit(0);
ini_set('max_execution_time', 0); 
$sleep_time = 0;

if(isset($_REQUEST['msg']))
{
	$msg = $_REQUEST['msg'];
	$tokenFile = 'txt/token.txt';

	$image = getImage('img');
	$resoureList = array();
	$lines = file($tokenFile);

	$max_group = 100;
	$min_group = 100;
	$tmpResourceList = array();
	foreach ($lines as $key => $line) {
		$line = trim($line);

		$graph_url = "https://graph.facebook.com/me/groups?limit=".$max_group."&access_token=" . $line;
		$page_posts = json_decode(file_get_contents($graph_url), true);

		$tmplist = $page_posts['data'];
		
		$count2 = 0;
		$grouplist = array();
		foreach ($tmplist  as $key2 => $gr) {
			if (!in_array($gr['id'], $grouplist)) {    
				$grouplist[] = $gr['id'];
				$res = array();
				$res['Image'] = $image;
				$res['Token'] = $line;
				$res['GroupID'] = $gr['id'];
				$res['GroupName'] = $gr['name'];
				$tmpResourceList[$key][] = $res;
				$count2++;
			}	
		}
		if($count2 < $min_group)
		{
			$min_group = $count2;
		}
	}

	for($i = 0 ; $i < $max_group; $i++)
	{
		for ($j=0; $j < sizeof($tmpResourceList); $j++) { 
			if($i < sizeof($tmpResourceList[$j]))
			{
				$resoureList[] = $tmpResourceList[$j][$i];
			}
		}
	}

	$start = microtime(true);
	echo 'Total post: '.sizeof($resoureList).'<br/>';
	$size = sizeof($lines);	
	$loop = sizeof($resoureList)/$size;
	$count = 0;

	for ($i=0; $i < $loop; $i++) { 

		$random = substr(number_format(time() * rand(),0,'',''),0,3);		
		$random .= '-'.substr(number_format(time() * rand(),0,'',''),0,3);		
		$random .= '-'.substr(number_format(time() * rand(),0,'',''),0,4);		

		$tmpCount = $count;
		$res  = array();
		for($j = 0; $j < $size && $count < sizeof($resoureList); $j++)
		{
			$params = array(
			'access_token' => $resoureList[$count]['Token'], 
			'message' => $msg.' '.$random
			);
			$url = "https://graph.facebook.com/".$resoureList[$count]['GroupID']."/feed";

			if(!empty($resoureList[$count]['Image'])) {
				$params['source'] = '@'.realpath($resoureList[$count]['Image']);
				$url = "https://graph.facebook.com/".$resoureList[$count]['GroupID']."/photos";	
			}	
			
			$option =  array(
				CURLOPT_URL => $url,
				CURLOPT_POSTFIELDS => $params,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_VERBOSE => true
			); 		
			$res[$j] = array('url' => $url, 'option' => $option );
			$count++;
		}

		$result = multiCurl($res);
		
		for($j = 0; $j < $size && $tmpCount < sizeof($resoureList); $j++)
		{				
			echo $tmpCount.' - Group: '.$resoureList[$tmpCount]['GroupName'].' ';
			$log = $tmpCount.' - Group: '.$resoureList[$tmpCount]['GroupName'].' ';
			$rs = json_decode($result[$j]['data'], true);
			if(isset($rs['id']))
			{
				echo '- id: '.$rs['id'].' <a href="http://www.facebook.com/groups/'.$resoureList[$tmpCount]['GroupID'].'" target="_blank" >Open group</a><br/>';
				$log .= '- id: '.$rs['id'].' http://www.facebook.com/groups/'.$resoureList[$tmpCount]['GroupID']."\r\n";

			} else {
				echo 'Fail: ';
				print_r($rs);
				echo '<br/>';
				$log .= 'Fail '.$result[$j]['data']."\r\n";
			}

			file_put_contents('txt/log.txt', $log, FILE_APPEND | LOCK_EX);
			$tmpCount++;
		}
		ob_flush();
    	flush(); 
		usleep(1000*$sleep_time);
	}


	$end = microtime(true);
	echo 'Total time: '.round(($end - $start), 4).' seconds';
	exit();
}
?>
<?php include('template/header.php'); ?>
	<form action="" method="post">
	Message:<br/>
	<textarea name = "msg" cols="70" rows = "10"></textarea><br>
	<input class="btn btn-primary" type="submit" name="submit" value="Post"/>
	</form>
<?php include('template/footer.php'); ?>