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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE x_wp_extra_conductor_detail SET conductor=%s WHERE id=%s",
                       GetSQLValueString($_POST['conductor'], "text"),
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

$colname_get_conductor = "-1";
if (isset($_GET['id'])) {
  $colname_get_conductor = $_GET['id'];
}
mysql_select_db($database_x_wsso, $x_wsso);
$query_get_conductor = sprintf("SELECT * FROM x_wp_extra_conductor_detail WHERE id = %s", GetSQLValueString($colname_get_conductor, "int"));
$get_conductor = mysql_query($query_get_conductor, $x_wsso) or die(mysql_error());
$row_get_conductor = mysql_fetch_assoc($get_conductor);
$totalRows_get_conductor = mysql_num_rows($get_conductor);
?>
<? include('inc/header.php'); ?>
<body>
<script src="js/nicEdit.js" type="text/javascript"></script>
<script type="text/javascript">
bkLib.onDomLoaded(function() {
	new nicEditor({iconsPath : 'js/nicEditorIcons.gif'}).panelInstance('area1');
});
</script>
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table width="100%" align="center">
    <tr valign="baseline">
      <td align="right" valign="top" nowrap="nowrap">Conductor:</td>
      <td width="100%" valign="baseline"><textarea name="conductor" id="area1" cols="32"><?php echo htmlentities($row_get_conductor['conductor'], ENT_COMPAT, 'utf-8'); ?></textarea></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" value="Update record" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1" />
  <input type="hidden" name="id" value="<?php echo $row_get_conductor['id']; ?>" />
</form>
<? include('inc/footer.php'); ?>
<?php
mysql_free_result($get_conductor);
?>
