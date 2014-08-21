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

$colname_get_extra = "-1";
if (isset($_GET['post_id'])) {
  $colname_get_extra = $_GET['post_id'];
} 
mysql_select_db($database_x_wsso, $x_wsso);
$query_get_extra = sprintf("SELECT * FROM x_wp_extra WHERE post_id = %s", GetSQLValueString($colname_get_extra, "int"));
$get_extra = mysql_query($query_get_extra, $x_wsso) or die(mysql_error());
$row_get_extra = mysql_fetch_assoc($get_extra);
$totalRows_get_extra = mysql_num_rows($get_extra);

// if this is not set
if($totalRows_get_extra == 0) {
	$xquery_get_extra = ("INSERT INTO x_wp_extra (post_id) VALUES (".$colname_get_extra.")");
	mysql_query($xquery_get_extra, $x_wsso) or die(mysql_error());
	header('Location: event_detail.php?post_id='.$colname_get_extra);	
}
	
mysql_select_db($database_x_wsso, $x_wsso);
$query_get_conductor = "SELECT * FROM x_wp_extra_conductor WHERE ex_id = $colname_get_extra";
$get_conductor = mysql_query($query_get_conductor, $x_wsso) or die(mysql_error());
$row_get_conductor = mysql_fetch_assoc($get_conductor);
$totalRows_get_conductor = mysql_num_rows($get_conductor);

mysql_select_db($database_x_wsso, $x_wsso);
$query_get_conductor_detail = "SELECT * FROM x_wp_extra_conductor_detail WHERE ex_id = $colname_get_extra";
$get_conductor_detail = mysql_query($query_get_conductor_detail, $x_wsso) or die(mysql_error());
$row_get_conductor_detail = mysql_fetch_assoc($get_conductor_detail);
$totalRows_get_conductor_detail = mysql_num_rows($get_conductor_detail);

mysql_select_db($database_x_wsso, $x_wsso);
$query_get_dates = "SELECT * FROM x_wp_extra_dates WHERE ex_id = $colname_get_extra";
$get_dates = mysql_query($query_get_dates, $x_wsso) or die(mysql_error());
$row_get_dates = mysql_fetch_assoc($get_dates);
$totalRows_get_dates = mysql_num_rows($get_dates);

mysql_select_db($database_x_wsso, $x_wsso);
$query_get_sponsor = "SELECT * FROM x_wp_extra_sponsor WHERE ex_id = $colname_get_extra";
$get_sponsor = mysql_query($query_get_sponsor, $x_wsso) or die(mysql_error());
$row_get_sponsor = mysql_fetch_assoc($get_sponsor);
$totalRows_get_sponsor = mysql_num_rows($get_sponsor);
?>
<? include('inc/header.php'); ?>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td nowrap="nowrap">Record ID</td>
    <td width="100%"><?php echo $row_get_extra['ex_id']; ?></td>
    <td nowrap="nowrap">&nbsp;</td>
  </tr>
  <tr>
    <td nowrap="nowrap">Post ID</td>
    <td><?php echo $row_get_extra['post_id']; ?></td>
    <td nowrap="nowrap">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" nowrap="nowrap"><hr size="1" noshade></td>
  </tr>
  <tr>
    <td nowrap="nowrap">Conductor</td>
    <td><a href="conductor_add.php?post_id=<?=$colname_get_extra ?>">Add Conductor</a></td>
    <td nowrap="nowrap">&nbsp;</td>
  </tr>
  <?php do { ?>
    <tr>
      <td nowrap="nowrap">&nbsp;</td>
      <td><img src="../../extra_images/<?php echo $row_get_conductor['conductor_image']; ?>" alt="<?php echo $row_get_conductor['conductor']; ?>"><?php echo $row_get_conductor['conductor']; ?></td>
      <td nowrap="nowrap"><a href="conductor_delete.php?post_id=<?=$colname_get_extra ?>&id=<?=$row_get_conductor['id'] ?>">Delete</a></td>
    </tr>
    <?php } while ($row_get_conductor = mysql_fetch_assoc($get_conductor)); ?>
  <tr>
      <td colspan="3" nowrap="nowrap"><hr size="1" noshade></td>
  </tr>
  <tr>
    <td nowrap="nowrap">Conductor Detail</td>
    <td><a href="conductor_detail_add.php?post_id=<?=$colname_get_extra ?>">Add Conductor Detail</a></td>
    <td nowrap="nowrap">&nbsp;</td>
  </tr>
  <?php do { ?>
    <tr>
      <td nowrap="nowrap">&nbsp;</td>
      <td><?php echo $row_get_conductor_detail['conductor']; ?></td>
      <td nowrap="nowrap"><a href="conductor_edit.php?post_id=<?=$colname_get_extra ?>&id=<?php echo $row_get_conductor_detail['id']; ?>">Edit</a> | <a href="conductor_detail_delete.php?post_id=<?=$colname_get_extra ?>&id=<?php echo $row_get_conductor_detail['id']; ?>">Delete</a></td>
    </tr>
    <?php } while ($row_get_conductor_detail = mysql_fetch_assoc($get_conductor_detail)); ?>
  <tr>
      <td colspan="3" nowrap="nowrap"><hr size="1" noshade></td>
  </tr>
  <tr>
    <td nowrap="nowrap">Dates</td>
    <td><a href="date_add.php?post_id=<?=$colname_get_extra ?>">Add Date</a></td>
    <td nowrap="nowrap">&nbsp;</td>
  </tr>
  <?php do { ?>
    <tr>
      <td nowrap="nowrap">&nbsp;</td>
      <td><?php echo $row_get_dates['date_info']; ?></td>
      <td nowrap="nowrap"><a href="date_edit.php?post_id=<?=$colname_get_extra ?>&id=<?php echo $row_get_dates['id']; ?>">Edit</a> | <a href="date_delete.php?post_id=<?=$colname_get_extra ?>&id=<?php echo $row_get_dates['id']; ?>">Delete</a></td>
    </tr>
    <?php } while ($row_get_dates = mysql_fetch_assoc($get_dates)); ?>
  <tr>
      <td colspan="3" nowrap="nowrap"><hr size="1" noshade></td>
  </tr>
  <tr>
    <td nowrap="nowrap">Sponsors</td>
    <td><a href="sponsor_add.php?post_id=<?=$colname_get_extra ?>">Add Sponsor</a></td>
    <td nowrap="nowrap">&nbsp;</td>
  </tr>
  <?php do { ?>
    <tr>
      <td nowrap="nowrap">&nbsp;</td>
      <td>
      <a href="<?php echo $row_get_sponsor['sponsor_url']; ?>"><img src="../../extra_images/<?php echo $row_get_sponsor['sponsor_image']; ?>" alt="<?php echo $row_get_sponsor['sponsor_name']; ?>"></a>
	  <?php echo $row_get_sponsor['sponsor_name']; ?> <?php echo $row_get_sponsor['sponsor_url']; ?> <?php echo $row_get_sponsor['sponsor_image']; ?></td>
      <td nowrap="nowrap"><a href="sponsor_delete.php?post_id=<?=$colname_get_extra ?>&id=<?=$row_get_sponsor['id'] ?>">Delete</a></td>
    </tr>
    <?php } while ($row_get_sponsor = mysql_fetch_assoc($get_sponsor)); ?>
</table>
<? include('inc/footer.php'); ?>
<?php
mysql_free_result($get_extra);

mysql_free_result($get_conductor);

mysql_free_result($get_conductor_detail);

mysql_free_result($get_dates);

mysql_free_result($get_sponsor);
?>
