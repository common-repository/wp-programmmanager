<?php
global $wpdb;
define('pmanagerCatTABLE',$wpdb->prefix.'pmanager_cats');


class WP_PMANAGER_CATEGORYS {

    function WP_PMANAGER_CATEGORYS() {
        //add_action('admin_post_save_wp_pmanager_cats', array(&$this, 'on_save_changes'));
        $this->checkForms();
        $this->show_settings();
    }

    function checkForms() {
        if(isset($_POST['action'])) {
            if($_POST['action'] == 'save_wp_pmanager_cats') {
                global $wpdb;
                $sql ="INSERT INTO ".pmanagerCatTABLE." (name) ";
                $sql.="VALUES ('".$_POST['cat_name']."');";
                $query = $wpdb->query($sql);
            }
        } else if(isset($_GET['id'])) {
            global $wpdb;

            $id = $_GET['id'];

            $sql = "DELETE FROM ".pmanagerCatTABLE." WHERE id=".$id;
            $wpdb->query($sql);
        }
    }

    function on_save_changes() {
        wp_die( __('Cheatin&#8217; uh?') );
        //user permission check
        if ( !current_user_can('manage_options') )
            wp_die( __('Cheatin&#8217; uh?') );
        //cross check the given referer
        check_admin_referer('wp-pmanager-cats');

        //process here your on $_POST validation and / or option saving
        if(isset($_POST['action'])) {
            if($_POST['action'] == 'save_wp_pmanager_cats') {
                global $wpdb;
                $sql ="INSERT INTO ".pmanagerCatTABLE." (name) ";
                $sql.="VALUES ('".$_POST['cat_name']."');";
                $query = $wpdb->query($sql);
            }
        }
        //lets redirect the post request into get request (you may add additional params at the url, if you need to show save results
        wp_redirect($_POST['_wp_http_referer']);
    }

    function get_cats() {
        global $wpdb;

        $sql = "SELECT COUNT(id) FROM ".pmanagerCatTABLE;
        $num_rows = $wpdb->get_var($sql);

        // Are there any entries?
        if($num_rows == 0) {
            echo "<tr><td></td><td style='text-align:center;'><b>No entries found.</b></td><td></td></tr>";
        }
        else {
            $sql = "SELECT * FROM ".pmanagerCatTABLE;
            $results = $wpdb->get_results($sql);

            foreach ($results as $resultsset) {
                echo <<< EOT
            <tr>
            <td>$resultsset->id</td>
            <td>$resultsset->name</td>
            <td>
            <a href="../wp-admin/admin.php?page=wp-pmanager-cats&id=$resultsset->id" onclick="return window.confirm('Wirklich l&ouml;schen?');"><span style="color:red;">L&ouml;schen</span></a></td>
            </tr>
EOT;
            }
        }
    }

    function show_settings() {
        ?>
<div id="wp-pmanager-cats" class="wrap">
    <h2>Categorys</h2>
    <p>Add new Category</p>
    <form action="" method="post">
                <?php wp_nonce_field('wp-pmanager-cats'); ?>
        <input type="hidden" name="action" value="save_wp_pmanager_cats" />
        <label>Name:</label><input type="text" name="cat_name" />
        <input type="submit" class="button-primary" name="save-button" value="Save" />
    </form>
    <hr />
    <table class="widefat">
        <thead>
        <th>ID</th>
        <th>Name</th>
        <th>&nbsp;</th>
        </thead>
        <tbody>
          <?php $this->get_cats(); ?>
        </tbody>
    </table>
</div>
        <?php
    }
}
?>