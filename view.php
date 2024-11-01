<?php
global $wpdb;
define('pmanagerMainTABLE', $wpdb->prefix.'pmanager_main');
define('pmanager_BASEPATH', get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager/');
define('pmanager_FILEPATH',$_SERVER['DOCUMENT_ROOT'] . '/downloads/');

// == If Page is called with an ID Entrie to delete it =============
if(isset($_GET['id']) && !isset($_GET['action']))
{
 	global $wpdb;

 	$sql = "DELETE FROM ".pmanagerMainTABLE." WHERE id=".$_GET['id'];
	$query = $wpdb->query($sql);
        include("overview.php");
} elseif(isset($_GET['action']) && isset($_GET['id'])) {
    include("details.php");
} else {
    include("overview.php");
}
?>
