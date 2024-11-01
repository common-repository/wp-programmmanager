<?php
$id = $_GET['id'];
if(empty($id) || preg_match("/\D/",$id))
{
	die("Invalid ID, numbers (0-9) only!");
}

require_once('./wp-blog-header.php');

global $wpdb;
define('pmanagerMainTABLE', $wpdb->prefix.'pmanager_main');
$dirname = $_SERVER['DOCUMENT_ROOT'] . '/downloads/';

//Fetch current count state and the file-link from Database
$query = "SELECT * FROM ".pmanagerMainTABLE." WHERE id=".$id;
$row = $wpdb->get_row($query) or die("Fehler!!");
if($row)
{
    //Calc new count and update it to the Database
    $count = $row->count;
    $count += 1;
    $query = "UPDATE ".pmanagerMainTABLE." SET count = $count WHERE id=".$id;
    $wpdb->query($query) or die("Fehler bei der abfrage!");
    //Get File-Link
    $link = $row->link;
    $datei  = $dirname;
    $datei .= $link;

    header("Content-Type: application/force-download");
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$datei");
    header("Content-Transfer-Encoding: binary");
    readfile($datei);
}
?>