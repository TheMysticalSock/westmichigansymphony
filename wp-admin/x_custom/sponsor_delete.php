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

$colname_get_image = "-1";
if (isset($_GET['id'])) {
  $colname_get_image = $_GET['id'];
}
mysql_select_db($database_x_wsso, $x_wsso);
$query_get_image = sprintf("SELECT * FROM x_wp_extra_sponsor WHERE id = %s", GetSQLValueString($colname_get_image, "int"));
$get_image = mysql_query($query_get_image, $x_wsso) or die(mysql_error());
$row_get_image = mysql_fetch_assoc($get_image);
$totalRows_get_image = mysql_num_rows($get_image);
unlink('C:\\inetpub\\vhosts\\wsso.org\\httpdocs\\staging/extra_images/'.$row_get_image['sponsor_image']); // correct

if ((isset($_GET['id'])) && ($_GET['id'] != "")) {
  $deleteSQL = sprintf("DELETE FROM x_wp_extra_sponsor WHERE id=%s",
                       GetSQLValueString($_GET['id'], "int"));

  mysql_select_db($database_x_wsso, $x_wsso);
  $Result1 = mysql_query($deleteSQL, $x_wsso) or die(mysql_error());

  $deleteGoTo = "event_detail.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}
?>
<?php
mysql_free_result($get_image);
?>
