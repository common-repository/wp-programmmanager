<?php

global $wpdb;
define('pmanagerMainTABLE', $wpdb->prefix.'pmanager_main');

if(isset($_POST['save_programm'])) {
    if($_POST['id'] != "") {
        global $wpdb;
        $sql = "UPDATE ".pmanagerMainTABLE." SET name='".$_POST['name']."', description='".$_POST['description']."',";
        $sql.= " installation='".$_POST['installation']."', faq='".$_POST['faq']."', download='".$_POST['download'];
        $sql.= "', link='".$_POST['link']."', cat_id='".$_POST['cat']."', post_id='".$_POST['post_id'];
        $sql.= "' WHERE id=".$_POST['id'];
        $query = $wpdb->query($sql);
    }
    else {
        //wp_die( __('Neuer Insert') );
        global $wpdb;
        $sql ="INSERT INTO ".pmanagerMainTABLE." (name,description,installation,faq,download,link,cat_id,post_id) ";
        $sql.="VALUES ('".$_POST['name']."', '".$_POST['description']."', '".$_POST['installation']."'";
        $sql.=", '".$_POST['faq']."', '".$_POST['download']."', '".$_POST['link']."', '".$_POST['cat']."', '".$_POST['post_id']."');";
        $query = $wpdb->query($sql);
    }
}

if(isset($_GET['id']))
{
    global $wpdb;
    $sql = "SELECT name FROM ".pmanagerMainTABLE." WHERE id=".$_GET['id'];
    $name = $wpdb->get_var($sql);

    $sql = "SELECT description FROM ".pmanagerMainTABLE." WHERE id=".$_GET['id'];
    $description = $wpdb->get_var($sql);

    $sql = "SELECT installation FROM ".pmanagerMainTABLE." WHERE id=".$_GET['id'];
    $installation = $wpdb->get_var($sql);

    $sql = "SELECT faq FROM ".pmanagerMainTABLE." WHERE id=".$_GET['id'];
    $faq = $wpdb->get_var($sql);

    $sql = "SELECT download FROM ".pmanagerMainTABLE." WHERE id=".$_GET['id'];
    $download = $wpdb->get_var($sql);

    $sql = "SELECT link FROM ".pmanagerMainTABLE." WHERE id=".$_GET['id'];
    $link = $wpdb->get_var($sql);

    $sql = "SELECT post_id FROM ".pmanagerMainTABLE." WHERE id=".$_GET['id'];
    $post_id = $wpdb->get_var($sql);
}

wp_tiny_mce( false , // true makes the editor "teeny"
	array(
		"editor_selector" => "editorContent"
	)
);

function pm_cats() {
    global $wpdb;
    $pmanagerCatTABLE = $wpdb->prefix.'pmanager_cats';
    $cat_id == -1;
    
    if(isset($_GET['id'])) {
        $sql = "SELECT cat_id FROM ".pmanagerMainTABLE." WHERE id=".$_GET['id'];
        $cat_id = $wpdb->get_var($sql);
    }

    $sql = "SELECT * FROM $pmanagerCatTABLE";
    $results = $wpdb->get_results($sql);

    foreach ($results as $resultsset) {
        if($resultsset->id == $cat_id) {
            echo "<option value=\"$resultsset->id\" selected=\"selected\">$resultsset->name</option>";
        } else {
            echo "<option value=\"$resultsset->id\">$resultsset->name</option>";
        }
    }
}

function pm_check_Cats() {
    global $wpdb;
    $pmanagerCatTABLE = $wpdb->prefix.'pmanager_cats';
    $sql = "SELECT COUNT(id) FROM ".$pmanagerCatTABLE;

    $num_rows = $wpdb->get_var($sql);
    if($num_rows == 0) {
        echo "<b>Cannot add a programm without having a category!</b>";
    } else {
        echo "<input class=\"button-primary\" type=\"submit\" value=\"Save\" name=\"save_programm\" />";
    }
}

?>
<div class="wrap">
    <h2>Add Programm</h2>
    <p>Here you can add your programms, you want to display.</p>
    <form method="POST" name="add_programm">
        <table cellpadding="0" cellspacing="20" style="margin:auto;">
            <tr>
                <td>
                    <center>
                        <label><b>Name:</b></label><br />
                        <input type="text" name="name" value="<?php echo $name; ?>" size="20" /><br />
                        <label class="small">The Name of your Programm</label>
                    </center>
                </td>
                <td>
                    <center>
                        <label><b>Filename:</b></label><br />
                        <input type="text" name="link" value="<?php echo $link; ?>" size="50" /><br />
                        <label class="small">Filename and Path within the 'downloads/' folder</label>
                    </center>
                </td>
                <td>
                    <center>
                        <label><b>Category:</b></label><br />
                        <select name="cat" size="1">
                            <?php pm_cats() ?>
                        </select><br />
                        <label class="small">Choose a Category</label>
                    </center>
                </td>
                <td>
                    <center>
                        <label><b>Post/Page ID::</b></label><br />
                        <input type="text" name="post_id" value="<?php echo $post_id; ?>" size="20" /><br />
                        <label class="small">A page/post for allowing comments etc.</label>
                    </center>
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="20" style="margin:auto;">
            <tr>
                <td>
                    <label><b>Description:</b></label><br />
                    <textarea name="description" class="editorContent"><?php echo $description; ?></textarea>
                    <label class="small">Description of your Programm</label>
                </td>
                <td>
                    <label><b>Installation:</b></label><br />
                    <textarea name="installation" class="editorContent"><?php echo $installation; ?></textarea>
                    <label class="small">Description and additional notes for the Installation</label>
                </td>
            </tr>
            <tr>
                <td>
                    <label><b>FAQ:</b></label><br />
                    <textarea name="faq"  class="editorContent"><?php echo $faq; ?></textarea>
                    <label class="small">Here you can place your FAQ</label>
                </td>
                <td>
                    <label><b>Download:</b></label><br />
                    <textarea name="download" class="editorContent"><?php echo $download; ?></textarea>
                    <label class="small">Additional Infos for the Download, rendered before the Download Link</label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php pm_check_Cats() ?>
                </td>
            </tr>
            <tr>
                <td colspan="2"><input type="hidden" name="id" value="<?php echo $_GET['id'] ?>"/></td>
            </tr>
        </table>
    </form>
</div>