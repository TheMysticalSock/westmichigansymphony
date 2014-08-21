<?php require_once('../../Connections/x_wsso.php'); ?>
<?php
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

$colname_get_sponsor = "-1";
if (isset($_GET['id'])) {
  $colname_get_sponsor = $_GET['id'];
}
mysql_select_db($database_x_wsso, $x_wsso);
$query_get_sponsor = sprintf("SELECT * FROM x_wp_extra_media_sponsor WHERE id = %s", GetSQLValueString($colname_get_sponsor, "int"));
$get_sponsor = mysql_query($query_get_sponsor, $x_wsso) or die(mysql_error());
$row_get_sponsor = mysql_fetch_assoc($get_sponsor);
$totalRows_get_sponsor = mysql_num_rows($get_sponsor);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
	
	
  $updateSQL = sprintf("UPDATE x_wp_extra_media_sponsor SET sponsor_name=%s, sponsor_url=%s, sponsor_image=%s WHERE id=%s",
                       GetSQLValueString($_POST['sponsor_name'], "text"),
                       GetSQLValueString($_POST['sponsor_url'], "text"),
                       GetSQLValueString($_POST['sponsor_image'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_x_wsso, $x_wsso);
  $Result1 = mysql_query($updateSQL, $x_wsso) or die(mysql_error());

  $updateGoTo = "event_detail.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table align="center">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Sponsor_name:</td>
      <td><input type="text" name="sponsor_name" value="<?php echo htmlentities($row_get_sponsor['sponsor_name'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Sponsor_url:</td>
      <td><input type="text" name="sponsor_url" value="<?php echo htmlentities($row_get_sponsor['sponsor_url'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Sponsor_image:</td>
      <td><input type="text" name="sponsor_image" value="<?php echo htmlentities($row_get_sponsor['sponsor_image'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" value="Update record" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1" />
  <input type="hidden" name="id" value="<?php echo $row_get_sponsor['id']; ?>" />
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($get_sponsor);
?>
