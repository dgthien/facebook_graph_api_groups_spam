<?php include('functions.php'); ?>
<?php include('template/header.php'); ?>
	<div id="list">
		<form action="" method="post">
			<textarea name="token" cols="150" rows="2"></textarea><br/>
			<input class="btn btn-primary" type="submit" value="Add"/>
		</form><br/>
		<?php
		$tokenFile = 'txt/token.txt';
		if(isset($_REQUEST['token']))
		{		
			$token = $_REQUEST['token'];
			$lines = file($tokenFile);

			if (!in_array($token."\r\n", $lines)) {
		    	$lines[] = $token;
		    	$fp = fopen($tokenFile, 'w');
		    	foreach ($lines as $line) {
		    		fwrite($fp, trim($line)."\r\n");
		    	}		
				fclose($fp);
		    	echo 'Get token done';
			}else
			{
				echo 'Token exist!';
			}	
		}
		?>
		<br/>Token list:<br/>
		<table class='table'>
			<tr>
				<th class="no">No</th>
				<th>Token</th>
			</tr>
			<?php
			$lines = file($tokenFile);
			foreach ($lines as $key => $line) {
				echo '<tr class="no"><td>'.$key.'</td>';		
				echo '<td>'.substr($line, 0, 50).'... </td></tr>';
			}
			?>	
		</table>
	</div>
<?php include('template/footer.php'); ?>