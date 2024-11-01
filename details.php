<?php
$id = $_GET['id'];
if(empty($id) || preg_match("/\D/",$id))
{
	die("Invalid ID, numbers (0-9) only!");
}

global $wpdb;
define('pmanagerMainTABLE', $wpdb->prefix.'pmanager_main');
define('pmanagerCatTABLE',$wpdb->prefix.'pmanager_cats');
$dirname = $_SERVER['DOCUMENT_ROOT'] . '/downloads/';

if(isset($_POST['delete_count'])) {
    $query = "UPDATE ".pmanagerMainTABLE." SET count = 0 WHERE id=".$id;
    $wpdb->query($query) or die("Fehler bei der abfrage!");
}

//Fetch current count state and the file-link from Database
$query = "SELECT * FROM ".pmanagerMainTABLE." WHERE id=".$id;
$row = $wpdb->get_row($query) or die("Fehler!!");

if ($row) {
    global $wpdb;
    $description = $row->description;
    $install = $row->installation;
    $faq = $row->faq;
    $download = $row->download;
    $post_id = $row->post_id;
    $query2 = "SELECT name FROM ".pmanagerCatTABLE." WHERE id=".$row->cat_id;
    $category = $wpdb->get_var($query2);

echo <<< EOT
<div class="wrap">
    <h2>Details for $row->name</h2>
    <table cellpadding="0" cellspacing="20" style="margin:auto;" class="widefat">
        <thead>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </thead>
        <tr>
            <td colspan="8"><b>Path:</b></td>
            <td>$row->link</td>
        </tr>
        <tr>
            <td colspan="8"><b>Category:</b></td>
            <td>$category</td>
        </tr>
        <tr>
            <td colspan="8"><b>Linked to Post/Page:</b></td>
            <td>$post_id</td>
        </tr>
        <tr>
            <td colspan="8"><b>Download-Count:</b></td>
            <td>$row->count <form method="post"><input class="button-primary" type="submit" name="delete_count" value="Reset Count" /></form></td>
        </tr>
        <tr>
            <td colspan="8"><label><b>Description:</b></label></td>
            <td><label>$description</label></td>
        </tr>
        <tr>
            <td colspan="8"><label><b>Installation:</b></label></td>
            <td><label>$install</label></td>
        </tr>
        <tr>
            <td colspan="8"><label><b>FAQ:</b></label></td>
            <td><label>$faq</label></td>
        </tr>
        <tr>
            <td colspan="8"><label><b>Download:</b></label></td>
            <td><label>$download</label></td>
        </tr>
    </table>
    <p><a href="../wp-admin/admin.php?page=wp-pmanager-overview">Zurück</a></p>
</div>
EOT;
} else {
    echo <<< EOT
   <div class="wrap">
       <p align="center">No Programm found!</p>
   </div>
EOT;
}
?>