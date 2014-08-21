<?php require_once('../../Connections/x_wsso.php'); ?>
<?php
$colname_get_extra = $_GET['post_id'];
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
		  $image->resizeToWidth(338);
		  $image->save(UPLOAD_DIR.$file);	
	
  $insertSQL = sprintf("INSERT INTO x_wp_extra_conductor (ex_id, conductor, conductor_image) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['ex_id'], "int"),
                       GetSQLValueString($_POST['conductor'], "text"),
					   GetSQLValueString($file, "text"));

  mysql_select_db($database_x_wsso, $x_wsso);
  $Result1 = mysql_query($insertSQL, $x_wsso) or die(mysql_error());

  $insertGoTo = "event_detail.php?post_id=".$colname_get_extra;
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1"  enctype="multipart/form-data">
  <table align="center">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Conductor:</td>
      <td><input type="text" name="conductor" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Image:</td>
      <td> <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
          <label for="image">Upload image:</label>
          <input type="file" name="image" id="image" />  </td>
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