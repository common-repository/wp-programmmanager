<?php
function pm_check_link($link) {
    $file = pmanager_FILEPATH . $link;
    //echo $file;
    if(file_exists($file)) {
        return '<img src="'.pmanager_BASEPATH.'images/tick.png" alt="" />';
    } else {
        return '<img src="'.pmanager_BASEPATH.'images/cross.png" alt="" />';
    }
}

function pm_list_all()
{
    global $wpdb;

    $sql = "SELECT COUNT(id) FROM ".pmanagerMainTABLE;
    $num_rows = $wpdb->get_var($sql);

    // Are there any entries?
    if($num_rows == 0)
    {
        echo "<tr><td></td><td></td><td style='text-align:center;'><b>No entries found.</b></td><td></td><td></td><td></td></tr>";
    }
    else
    {
        $sql = "SELECT * FROM ".pmanagerMainTABLE;
        $results = $wpdb->get_results($sql);

        foreach ($results as $resultsset)
        {
            $img = pm_check_link($resultsset->link);

            echo <<< EOT
            <tr>
            <td><label>$resultsset->id</label></td>
            <td><label><b>$resultsset->name</b><br />$resultsset->link</label></td>
            <td>$img</td>
            <td><label>$resultsset->count</label></td>
            <td><a href="../wp-admin/admin.php?page=wp-pmanager-new&id=$resultsset->id">Bearbeiten</a>
             |
            <a href="../wp-admin/admin.php?page=wp-pmanager-overview&action=details&id=$resultsset->id">Details</a>
             |
            <a href="../wp-admin/admin.php?page=wp-pmanager-overview&id=$resultsset->id" onclick="return window.confirm('Wirklich l&ouml;schen?');"><span style="color:red;">L&ouml;schen</span></a></td>
            </tr>
EOT;
        }
    }
}

?>
<div class="wrap">
    <h2>Overview</h2>
    <p>Here you can handle your saved programms, edit or delete them.</p>
    <br /><br />
    <table class="widefat" id="pm_overview">
        <thead>
            <tr>
                <th><label>ID</label></th>
                <th><label><b>Name</b><br />File</label></th>
                <th><label>Found</label></th>
                <th><label>Count</label></th>
                <th><label>&nbsp;</label></th>
            </tr>
        </thead>
        <tbody>
            <?php pm_list_all(); ?>
        </tbody>
    </table>
    <script type="text/javascript">
    //<![CDATA[
    jQuery(document).ready( function($) {
        $('#pm_overview').dataTable();
    });
    //]]>
</script>
</div>