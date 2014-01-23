<?php
include('functions.php');

error_reporting(E_ALL);
ini_set("display_errors", 1);

if(isset($_FILES["file"]))
{
  if ($_FILES["file"]["error"][0] > 0)
  {
    echo "Upload error!<br/>";
  }
  else
  { 
    $dir = 'img';
    $files = find_all_files($dir);

    foreach ($files as $key => $file) {
      unlink($file);
    }

    $count = 0;
    foreach ($_FILES["file"]["tmp_name"] as $key => $file) {      
      move_uploaded_file($_FILES["file"]["tmp_name"][$key], $dir.'/'.$key.'.jpg');                  
      $count++;
    }    
    echo 'Uploaded: '.$count.' file(s)';    
  }
}
?> 

<?php include('template/header.php'); ?>
  <form action="" method="post" enctype="multipart/form-data">
    <label for="file">Select file or multifiles:</label>
    <input type="file" id="file" name="file[]" id="file" multiple/><br>
    <input class="btn btn-primary" type="submit" name="submit" value="Submit"/>
  </form>
<?php include('template/footer.php');