<?php require_once('../../Connections/x_wsso.php'); ?>
<?php
$colname_get_extra = $_GET['post_id'];
define ('MAX_FILE_SIZE', 9024 * 5000);
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
   include('simple_image.php');
  // define constant for upload folder
  
 
 define('UPLOAD_DIR', 'C:\\inetpub\\vhosts\\wsso.org\\httpdocs\\staging/extra_images/');
  
  
  // replace any spaces in original filename with underscores
  $file = str_replace(' ', '_', $_FILES['image']['name']);
  // create an array of permitted MIME types
  $permitted = array('image/gif', 'image/jpeg', 'image/pjpeg','image/png');
  $uid = uniqid();$file = $uid."_".$file;
		  $image = new SimpleImage();
		  $image->load($_FILES['image']['tmp_name'], UPLOAD_DIR . $file);
		  $image->resizeToWidth(215);
		  $image->save(UPLOAD_DIR.$file);		  	
	
  $insertSQL = sprintf("INSERT INTO x_wp_extra_media_sponsor (ex_id, sponsor_name, sponsor_url, sponsor_image) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['ex_id'], "int"),
                       GetSQLValueString($_POST['sponsor_name'], "text"),
                       GetSQLValueString($_POST['sponsor_url'], "text"),
                       GetSQLValueString($file, "text"));

  mysql_select_db($database_x_wsso, $x_wsso);
  $Result1 = mysql_query($insertSQL, $x_wsso) or die(mysql_error());

  $insertGoTo = "event_detail.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING']."&result=".$result;
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" enctype="multipart/form-data">
  <table align="center">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Sponsor_name:</td>
      <td><input type="text" name="sponsor_name" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Sponsor_url:</td>
      <td><input type="text" name="sponsor_url" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Sponsor_image:</td>
      <td>      
      <!-- <input type="text" name="sponsor_image" value="" size="32" />-->
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
          <label for="image">Upload image:</label>
          <input type="file" name="image" id="image" />      
      </td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" value="Insert record" /></td>
    </tr>
  </table>
  <input type="hidden" name="ex_id" value="<?=$colname_get_extra ?>" />
  <input type="hidden" name="MM_insert" value="form1" />
</form>
<p>&nbsp;</p>
</body>
</html>
