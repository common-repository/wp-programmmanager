<?php
/*
Plugin Name: WP-PManager
Plugin URI: http://www.angelofagony.de.vu
Description: Programm Database Manager
Version: 1.2
Author: David Brendel
Author URI: http://www.angelofagony.de.vu
*/

/*  Copyright 2008  David Brendel http://www.angelofagony.de.vu

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    For a copy of the GNU General Public License, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

global $wpdb;
define('pmanagerMainTABLE', $wpdb->prefix.'pmanager_main');
define('pmanagerCatTABLE',$wpdb->prefix.'pmanager_cats');

//Load file with class for installation
require_once 'wp-pmanager-install.php';
//Load file with class for categorys
require_once 'category.php';

//class that reperesent the complete plugin
class WP_PManager_Plugin {

    //global $wpdb;
    const pmanagerVersion = '1.2';
    const PMANAGER_PLUGIN_ID = "wp-pmanager-plugin";
    private $options = array();

    //constructor of class, PHP4 compatible construction for backward compatibility
    function WP_PManager_Plugin() {
	//add filter for WordPress 2.8 changed backend box system !
	add_filter('screen_layout_columns', array(&$this, 'on_screen_layout_columns'), 10, 2);
	//register callback for admin menu  setup
	add_action('admin_menu', array(&$this, 'on_admin_menu'));
	//register the callback been used if options of page been submitted and needs to be processed
	add_action('admin_post_save_wp_pmanager_general', array(&$this, 'on_save_changes'));
        //register callback for activating and deactivation the plugin
        add_action('activate_'.dirname(plugin_basename(__FILE__)).'/wp-pmanager.php', array(&$this, 'pmanager_install'));
        //add_action('deactivate_'.dirname(plugin_basename(__FILE__)).'/wp-pmanager.php', array(&$this, 'pmanager_deinstall'));
        //register shortcode
        add_shortcode('wp-pmanager', array(&$this,'pmanager_shortcode_func'));
        //register scripts for page header
        add_action('wp_head', array(&$this,'pmanager_header_func'));
        //register scripts for admin header
        add_action('admin_head', array(&$this,'pmanager_adminheader_func'));
    }

    // == for WordPress 2.8 we have to tell, that we support 2 columns ! ==========
    function on_screen_layout_columns($columns, $screen) {
		if ($screen == $this->pagehook) {
			$columns[$this->pagehook] = 2;
		}
		return $columns;
	}

    // == create new object of instalation-class and start installation ===========
    function pmanager_install() {
        $WPPManagerInstall = new PManager_Install();
    }

    // == Action when Plugin is deinstalled ========================================
    function pmanager_deinstall() {
        delete_option(self::PMANAGER_PLUGIN_ID);
    }

    // == Write link to stylesheet file, other css and javascript files ============
    function pmanager_header_func() {
        $options = get_option(self::PMANAGER_PLUGIN_ID);
        echo '<link rel="stylesheet" type="text/css" id="WP-PManager-Plugin" media="screen" href="'. get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager/css/stylesheet.css" />';
        $this->writeOptionsCSS();
        wp_enqueue_script( 'jquery' );
        echo '<script language="JavaScript" src="'. get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager/js/jquery.idTabs.min.js"></script>';
    }

    // == Write links to Stylesheets and Javascript ================================
    function pmanager_adminheader_func() {
        echo '<link rel="stylesheet" type="text/css" id="WP-PManager-Plugin" media="screen" href="'. get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager/css/stylesheet.css" />';
        echo '<link rel="stylesheet" type="text/css" id="WP-PManager-Plugin" media="screen" href="'. get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager/css/demo_table.css" />';
        echo '<link rel="stylesheet" type="text/css" id="WP-PManager-Plugin" media="screen" href="'. get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager/css/demo_page.css" />';
        echo '<script language="JavaScript" src="'. get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager/js/jquery.dataTables.js"></script>';
    }

    // == Write additional CSS which is saved in the options ======================
    function writeOptionsCSS() {
    $this->options = get_option(self::PMANAGER_PLUGIN_ID);

    echo '<style type="text/css">';
    //TabControl
    echo '.usual {';
    echo 'width: '.$this->options['tabcontrol_width'].';';
    if($this->options['tabcontrol_bordercolor_checkbox'] == false) {
        echo 'border: '.$this->options['tabcontrol_border'].' '.$this->options['tabcontrol_bordercolor'].';';
    }
    else { echo 'border: none;'; }
    if($this->options['tabcontrol_bgcolor_checkbox'] == false) {
        echo 'background-color: '.$this->options['tabcontrol_bgcolor'].';';
    }
    else { echo 'background-color: none;'; }
    echo '}';
    //Content
    if($this->options['content_bordercolor_checkbox'] == false || $this->options['content_bgcolor_checkbox'] == false) {
        echo '.usual div {';
        if($this->options['content_bordercolor_checkbox'] == false) {
            echo "border: ".$this->options['content_border']." ".$this->options['content_bordercolor'].";";
        }
        else {
            echo "border:none;";
        }
        if($this->options['content_bgcolor_checkbox'] == false) {
            echo "background-color: ".$this->options['content_bgcolor'].";";
        }
        else {
            echo "backgroundcolor: none;";
        }
        echo '}';
    }
    //Tabs normal
    echo '.usual ul a {';
    if($this->options['tabs_bgcolor_checkbox'] == false) {
        echo 'background-color: '.$this->options['tabs_bgcolor'].';';
    }
    else {
        echo 'background-color:none;';
    }
    if($this->options['tabs_bordercolor_checkbox'] == false) {
        echo 'border: '.$this->options['tabs_border'].' '.$this->options['tabs_bordercolor'].';';
    }
    else { echo 'border: none;'; }
    echo '}';
    //Tabs hover
    echo '.usual ul a:hover {';
    if($this->options['tabs_h_bordercolor_checkbox'] == false) {
        echo 'border: 1px solid '.$this->options['tabs_h_bordercolor'].';';
    }
    else { echo 'border: none;'; }
    if($this->options['tabs_h_bgcolor_checkbox'] == false) {
        echo 'background-color: '.$this->options['tabs_h_bgcolor'].';';
    }
    else { echo 'background-color: none;'; }
    echo '}';
    //Tabs selected
    echo '.usual ul a.selected {';
    echo 'font-color: '.$this->options['tabs_a_fontcolor'].';';
    if($this->options['tabs_a_bordercolor_checkbox'] == false) {
        echo 'border: '.$this->options['tabs_a_border'].' '.$this->options['tabs_a_bordercolor'].';';
    }
    else { echo 'border: none;'; }
    if($this->options['tabs_a_bgcolor_checkbox'] == false) {
    echo 'background-color: '.$this->options['tabs_a_bgcolor'].';';
    }
    else { echo 'background-color:none;'; }
    echo '}';
    echo '</style>';
}

    // == extend the admin menu ===================================================
    function on_admin_menu() {
            //add MenuPage
            if(function_exists('add_menu_page')) {
                $this->pagehook = add_menu_page('WP-PManager','WP-PManager',10,__FILE__,array(&$this,'on_show_page'));
            }
            if(function_exists('add_submenu_page')) {
                add_submenu_page(__FILE__,'PManager Plugin Settings','Settings',10,'wp-programmmanager/wp-pmanager.php',array(&$this,'on_show_page'));
                add_submenu_page(__FILE__,'PManager Plugin Catagorys','Categorys',10,'wp-pmanager-cats',array(&$this,'on_show_page'));
                add_submenu_page(__FILE__,'PManager Plugin Add Programm','Add Programm',10,'wp-pmanager-new',array(&$this,'on_show_page'));
                add_submenu_page(__FILE__,'PManager Plugin Programm Overview','Overview',10,'wp-pmanager-overview',array(&$this,'on_show_page'));
            }
            //register  callback gets call prior your own page gets rendered
            add_action('load-'.$this->pagehook, array(&$this, 'on_load_page'));
        }

    // == will be executed if wordpress core detects this page has to be rendered ==
    function on_load_page() {
	//ensure, that the needed javascripts been loaded to allow drag/drop, expand/collapse and hide/show of boxes
	wp_enqueue_script('common');
	wp_enqueue_script('wp-lists');
	wp_enqueue_script('postbox');

        //add several metaboxes now, all metaboxes registered during load page can be switched off/on at "Screen Options" automatically, nothing special to do therefore
	add_meta_box('wp-pmanager-sidebox-1', 'Deinstall Plugin', array(&$this, 'on_sidebox_1_content'), $this->pagehook, 'side', 'core');
	add_meta_box('wp-pmanager-contentbox-1', 'Content Settings', array(&$this, 'on_contentbox_1_content'), $this->pagehook, 'normal', 'core');
	add_meta_box('wp-pmanager-contentbox-2', 'Tabs Settings', array(&$this, 'on_contentbox_2_content'), $this->pagehook, 'normal', 'core');
        add_meta_box('wp-pmanager-contentbox-3', 'TabControl Settings', array(&$this, 'on_contentbox_3_content'), $this->pagehook, 'normal', 'core');
        add_meta_box('wp-pmanager-contentbox-4', 'Category Settings', array(&$this, 'on_contentbox_4_content'), $this->pagehook, 'normal', 'core');
        add_meta_box('wp-pmanager-contentbox-5', 'Category Settings', array(&$this, 'on_contentbox_5_content'), $this->pagehook, 'normal', 'core');

        //load PLugin Options
        $this->options = get_option(self::PMANAGER_PLUGIN_ID);
    }

    // == executed to show the plugins complete admin page ========================
    function on_show_page() {
                switch($_GET['page']) {
                    case "wp-pmanager-new" :
                        include("add_programm.php");
                        break;
                    case "wp-pmanager-overview" :
                        include("view.php");
                        break;
                    case "wp-pmanager-cats" :
                        $WPPMANAGERCats = new WP_PMANAGER_CATEGORYS();
                        break;
                    default :
                        $this->showSettingsPage();
                        break;
                }
	}

    // == executed to print html on the settings page of the plugin ===============
    function showSettingsPage() {
            //we need the global screen column value to beable to have a sidebar in WordPress 2.8
            global $screen_layout_columns;
            ?>
<div id="wp-pmanager-general" class="wrap">
            <?php screen_icon('options-general'); ?>
    <h2>WP-ProgrammManager</h2>
    <form action="admin-post.php" method="post">
                <?php wp_nonce_field('wp-pmanager-general'); ?>
                <?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
                <?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
        <input type="hidden" name="action" value="save_wp_pmanager_general" />

        <div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
            <div id="side-info-column" class="inner-sidebar">
                        <?php do_meta_boxes($this->pagehook, 'side', $data); ?>
            </div>
            <div id="post-body" class="has-sidebar">
                <div id="post-body-content" class="has-sidebar-content">
                            <?php do_meta_boxes($this->pagehook, 'normal', $data); ?>
                    <p>
                        <input type="submit" value="Save Changes" class="button-primary" name="Submit"/>
                    </p>
                </div>
            </div>
            <br class="clear"/>

        </div>
    </form>
</div>
<script type="text/javascript">
    //<![CDATA[
    jQuery(document).ready( function($) {
        // close postboxes that should be closed
        $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
        // postboxes setup
        postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
    });
    //]]>
</script>
        <?php
        }

    // == executed if the post arrives initiated by pressing the submit button of form ==
    function on_save_changes() {
		//user permission check
		if ( !current_user_can('manage_options') )
			wp_die( __('Cheatin&#8217; uh?') );
		//cross check the given referer
		check_admin_referer('wp-pmanager-general');

		//process here your on $_POST validation and / or option saving
                if(isset($_POST['action'])) {
                    if($_POST['action'] == 'save_wp_pmanager_general') {
                        if(isset($_POST['drop']) && isset($_POST['delete'])) {
                            global $wpdb;

                            $sql = "DROP TABLE IF EXISTS ".pmanagerMainTABLE;
                            $wpdb->query($sql);

                            $sql = "DROP TABLE IF EXISTS ".pmanagerCatTABLE;
                            $wpdb->query($sql);
                        } else {
                            $this->options = get_option(self::PMANAGER_PLUGIN_ID);
                            $this->save_content_css();
                            $this->save_tabcontrol_css();
                            $this->save_tabs_css();
                            $this->options['show_cats'] = $_POST['show_cats'];
                            $this->options['show_requst_link'] = $_POST['show_requst_link'];
                            $this->options['use_post_page'] = $_POST['use_post_page'];
                            update_option(self::PMANAGER_PLUGIN_ID,$this->options);
                        }
                    } 
                }

		//lets redirect the post request into get request (you may add additional params at the url, if you need to show save results
		wp_redirect($_POST['_wp_http_referer']);
	}

    function save_content_css() {
        //Save the Border, BorderColor and if it should be displayed
        if(isset ($_POST['content_bordercolor_checkbox'])) {
            $this->options['content_bordercolor_checkbox'] = true;
        } else {
            $this->options['content_bordercolor_checkbox'] = false;
        }
        $this->options['content_bordercolor'] = $_POST['content_bordercolor'];
        $this->options['content_border'] = $_POST['content_border'];
        //Save Background-Color and if it should be displayed
        $this->options['content_bgcolor'] = $_POST['content_bgcolor'];
        if(isset ($_POST['content_bgcolor_checkbox']))
        {
            $this->options['content_bgcolor_checkbox'] = true;
        }
        else
        {
            $this->options['content_bgcolor_checkbox'] = false;
        }
    }

    function save_tabcontrol_css() {

        //Save the Width
        $this->options['tabcontrol_width'] = $_POST['tabcontrol_width'];
        //Save the Border, BorderColor and if it should be displayed
        $this->options['tabcontrol_border'] = $_POST['tabcontrol_border'];
        $this->options['tabcontrol_bordercolor'] = $_POST['tabcontrol_bordercolor'];
        if(isset ($_POST['tabcontrol_bordercolor_checkbox']))
        {
            $this->options['tabcontrol_bordercolor_checkbox'] = true;
        }
        else
        {
            $this->options['tabcontrol_bordercolor_checkbox'] = false;
        }
        //Save Background-Color and if it should be displayed
        $this->options['tabcontrol_bgcolor'] = $_POST['tabcontrol_bgcolor'];
        if(isset ($_POST['tabcontrol_bgcolor_checkbox']))
        {
            $this->options['tabcontrol_bgcolor_checkbox'] = true;
        }
        else
        {
            $this->options['tabcontrol_bgcolor_checkbox'] = false;
        }
    }

    function save_tabs_css() {

        //Save Border, BorderColor and if it should be displayed - Tab
        $this->options['tabs_fontcolor'] = $_POST['tabs_fontcolor'];
        if(isset ($_POST['tabs_bordercolor_checkbox']))
        {
            $this->options['tabs_bordercolor_checkbox'] = true;
        }
        else
        {
            $this->options['tabs_bordercolor_checkbox'] = false;
        }
        $this->options['tabs_bordercolor'] = $_POST['tabs_bordercolor'];
        $this->options['tabs_border'] = $_POST['tabs_border'];
        $this->options['tabs_bgcolor'] = $_POST['tabs_bgcolor'];
        if(isset ($_POST['tabs_bgcolor_checkbox']))
        {
            $this->options['tabs_bgcolor_checkbox'] = true;
        }
        else
        {
            $this->options['tabs_bgcolor_checkbox'] = false;
        }

        //Save Border, BorderColor and if it should be displayed - Tab:Hover
        $this->options['tabs_h_bordercolor'] = $_POST['tabs_h_bordercolor'];
        if(isset ($_POST['tabs_h_bordercolor_checkbox']))
        {
            $this->options['tabs_h_bordercolor_checkbox'] = true;
        }
        else
        {
            $this->options['tabs_h_bordercolor_checkbox'] = false;
        }
        $this->options['tabs_h_bgcolor'] = $_POST['tabs_h_bgcolor'];
        if(isset ($_POST['tabs_h_bgcolor_checkbox']))
        {
            $this->options['tabs_h_bgcolor_checkbox'] = true;
        }
        else
        {
            $this->options['tabs_h_bgcolor_checkbox'] = false;
        }

        //Save Font-Color, Border, BorderColor and if it should be displayed - Tab:Active
        $this->options['tabs_a_fontcolor'] = $_POST['tabs_a_fontcolor'];
        if(isset ($_POST['tabs_a_bordercolor_checkbox']))
        {
            $this->options['tabs_a_bordercolor_checkbox'] = true;
        }
        else
        {
            $this->options['tabs_a_bordercolor_checkbox'] = false;
        }
        $this->options['tabs_a_bordercolor'] = $_POST['tabs_a_bordercolor'];
        $this->options['tabs_a_border'] = $_POST['tabs_a_border'];
        $this->options['tabs_a_bgcolor'] = $_POST['tabs_a_bgcolor'];
        if(isset ($_POST['tabs_a_bgcolor_checkbox']))
        {
            $this->options['tabs_a_bgcolor_checkbox'] = true;
        }
        else
        {
            $this->options['tabs_a_bgcolor_checkbox'] = false;
        }

        //update_option(self::PMANAGER_PLUGIN_ID,$options);
    }


    //below you will find for each registered metabox the callback method, that produces the content inside the boxes
    //i did not describe each callback dedicated, what they do can be easily inspected and compare with the admin page displayed
    function on_sidebox_1_content($data) {
		?>
		<label>Really delete the Database Tables?</label>
                <input type="checkbox" name="drop" value="drop" />
                <input class="button-primary" name="delete" value="<?php echo htmlentities('Delete') ?>" type="submit" />
                <br />
                <label><span id="pmanager-small-font" style="color:red;"><?php echo htmlentities('Deleting the Tables cannot be revoked!!') ?></span></label>
		<?php
	}
        
    function on_contentbox_1_content($data) {
	?>
                <table class="widefat">
                    <tr>
                        <td><label><b>Border-Color:</b></label></td>
                        <td>
                            <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                            <input class="color {hash:true}" value="<?php echo $this->options['content_bordercolor']; ?>" name="content_bordercolor" size="8" /><br />
                            <input type="checkbox" name="content_bordercolor_checkbox" value="check" <?php if($this->options['content_bordercolor_checkbox']) {
                            echo 'checked="checked"';
                        }?>><span id="pmanager-small-font">Ignore this.</span>
                        </td>
                    </tr>
                    <tr>
                        <td><label><b>Border:</b></label></td>
                        <td>
                            <input value="<?php echo $this->options['content_border']; ?>" name="content_border">
                        </td>
                    </tr>
                    <tr>
                        <td><label><b>Background-Color:</b></label></td>
                        <td>
                            <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                            <input class="color {hash:true}" value="<?php echo $this->options['content_bgcolor']; ?>" name="content_bgcolor" size="8" /><br />
                            <input type="checkbox" name="content_bgcolor_checkbox" value="check" <?php if($this->options['content_bgcolor_checkbox']) {
                            echo 'checked="checked"';
                        }?>><span id="pmanager-small-font">Ignore this.</span>
                        </td>
                    </tr>
                </table>
	<?php
}
    function on_contentbox_2_content($data) {
		?>
		<h4>Tab</h4>
                    <table class="widefat">
                        <tr>
                            <td><label><b>Border-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $this->options['tabs_bordercolor']; ?>" name="tabs_bordercolor" size="8" /><br />
                                <input type="checkbox" name="tabs_bordercolor_checkbox" value="check" <?php if($this->options['tabs_bordercolor_checkbox']) { echo 'checked="checked"'; }?>><span id="pmanager-small-font">Ignore this.</span>
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Border:</b></label></td>
                            <td>
                                <input value="<?php echo $this->options['tabs_border']; ?>" name="tabs_border" />
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Background-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $this->options['tabs_bgcolor']; ?>" name="tabs_bgcolor" size="8" /><br />
                                <input type="checkbox" name="tabs_bgcolor_checkbox" value="check" <?php if($this->options['tabs_bgcolor_checkbox']) { echo 'checked="checked"'; }?>><span id="pmanager-small-font">Ignore this.</span>
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Font-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $this->options['tabs_fontcolor']; ?>" name="tabs_fontcolor" size="8" /><br />
                            </td>
                        </tr>
                    </table>
                    <h4>Tab:Hover</h4>
                    <table class="widefat">
                        <tr>
                            <td><label><b>Border-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $this->options['tabs_h_bordercolor']; ?>" name="tabs_h_bordercolor" size="8" /><br />
                                <input type="checkbox" name="tabs_h_bordercolor_checkbox" value="check" <?php if($this->options['tabs_h_bordercolor_checkbox']) { echo 'checked="checked"'; }?>><span id="pmanager-small-font">Ignore this.</span>
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Background-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $this->options['tabs_h_bgcolor']; ?>" name="tabs_h_bgcolor" size="8" /><br />
                                <input type="checkbox" name="tabs_h_bgcolor_checkbox" value="check" <?php if($this->options['tabs_h_bgcolor_checkbox']) { echo 'checked="checked"'; }?>><span id="pmanager-small-font">Ignore this.</span>
                            </td>
                        </tr>
                    </table>
                    <h4>Tab:Active</h4>
                    <table class="widefat">
                        <tr>
                            <td><label><b>Border-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $this->options['tabs_a_bordercolor']; ?>" name="tabs_a_bordercolor" size="8" /><br />
                                <input type="checkbox" name="tabs_a_bordercolor_checkbox" value="check" <?php if($this->options['tabs_a_bordercolor_checkbox']) { echo 'checked="checked"'; }?>><span id="pmanager-small-font">Ignore this.</span>
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Border:</b></label></td>
                            <td>
                                <input value="<?php echo $this->options['tabs_a_border']; ?>" name="tabs_a_border" />
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Background-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $this->options['tabs_a_bgcolor']; ?>" name="tabs_a_bgcolor" size="8" /><br />
                                <input type="checkbox" name="tabs_a_bgcolor_checkbox" value="check" <?php if($this->options['tabs_a_bgcolor_checkbox']) { echo 'checked="checked"'; }?>><span id="pmanager-small-font">Ignore this.</span>
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Font-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $this->options['tabs_a_fontcolor']; ?>" name="tabs_a_fontcolor" size="8" /><br />
                            </td>
                        </tr>
                    </table>
		<?php
	}
    function on_contentbox_3_content($data) {
		?>
                    <table class="widefat">
                        <tr>
                            <td><label><b>Width:</b></label></td>
                            <td>
                                <input value="<?=$this->options['tabcontrol_width']; ?>" name="tabcontrol_width">
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>BorderColor:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $this->options['tabcontrol_bordercolor']; ?>" name="tabcontrol_bordercolor" size="8" /><br />
                                <input type="checkbox" name="tabcontrol_bordercolor_checkbox" value="check" <?php if($this->options['tabcontrol_bordercolor_checkbox']) { echo 'checked="checked"'; }?>><span id="pmanager-small-font">Ignore this.</span>
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Border:</b></label></td>
                            <td>
                                <input value="<?php echo $this->options['tabcontrol_border']; ?>" name="tabcontrol_border" />
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Background-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $this->options['tabcontrol_bgcolor']; ?>" name="tabcontrol_bgcolor" size="8" /><br />
                                <input type="checkbox" name="tabcontrol_bgcolor_checkbox" value="check" <?php if($this->options['tabcontrol_bgcolor_checkbox']) { echo 'checked="checked"'; }?>><span id="pmanager-small-font">Ignore this.</span>
                            </td>
                        </tr>
                    </table>
		<?php
	}
    function on_contentbox_4_content($data) {
        ?>
        <table class="widefat">
            <tr>
                <td><label><b>Show Categorys:</b></label></td>
                <td><input type="checkbox" name="show_cats" value="check" <?php if($this->options['show_cats']) { echo 'checked="checked"'; }?> /></td>
           </tr>
      </table>
       <?php
    }
    function on_contentbox_5_content($data) {
        if('post' == $this->options['use_post_page']) {
            $options = '<option value="post" selected="selected">POST</option>';
            $options .= '<option value="page">PAGE</option>';
        } else if('page' == $this->options['use_post_page']) {
            $options = '<option value="post">POST</option>';
            $options .= '<option value="page"  selected="selected">PAGE</option>';
        } else {
             $options = '<option value="post">POST</option>';
            $options .= '<option value="page">PAGE</option>';
        }
        ?>
        <table class="widefat">
            <tr>
                <td><label><b>Show Request Link</b></label></td>
                <td><input type="checkbox" name="show_requst_link" value="check" <?php if($this->options['show_requst_link']) { echo 'checked="checked"'; }?> /></td>
            </tr>
            <tr>
                <td><label><b>Use Post/Page:</b></label></td>
                <td><select name="use_post_page"><?php echo $options; ?></select></td>
           </tr>
      </table>
       <?php
    }

    //below you will find all functions for the shortcode
    // == executed when shortcode is called =======================================
    function pmanager_shortcode_func($atts) {
        extract($atts);

        if(isset($cat_id)) {
            $this->pmanager_shortcode_by_cat($cat_id);
        } else {

            $options = get_option(self::PMANAGER_PLUGIN_ID);
            if($options['show_cats']) {
                $this->pmanager_shortcode_by_cats();
            } else {
                $this->pmanager_shortcode_all();
            }
        }
    }
    // == executed when categorys should be dislplayed =============================
    function pmanager_shortcode_by_cats() {
        global $wpdb;
        $dirname = get_bloginfo('url');
        $tabview_ids = array();

        $sql = "SELECT ".pmanagerMainTABLE.".id,".pmanagerMainTABLE.".name,".pmanagerMainTABLE.".description,".pmanagerMainTABLE.".installation,".pmanagerMainTABLE.".faq,".pmanagerMainTABLE.".download,".pmanagerMainTABLE.".count,".pmanagerMainTABLE.".post_id,".pmanagerCatTABLE.".name AS CAT_NAME,".pmanagerCatTABLE.".id AS CAT_ID FROM ".pmanagerMainTABLE." INNER JOIN ".pmanagerCatTABLE." ON ".pmanagerMainTABLE.".cat_id = ".pmanagerCatTABLE.".id ORDER BY ".pmanagerCatTABLE.".name";
        $results = $wpdb->get_results($sql);

        $cat = '';
        foreach ($results as $resultsset) {
            if ($cat != $resultsset->CAT_NAME) {
                $cat = $resultsset->CAT_NAME;
                echo "<h1>$resultsset->CAT_NAME</h1>";
            }
            $this->shortcode_show_programm($resultsset);
        }
    }
    // == executed when categorys shouldn't be displayed ===========================
    function pmanager_shortcode_all() {
        global $wpdb;
        $dirname = get_bloginfo('url');
        $tabview_ids = array();

        $sql = "SELECT * FROM ".pmanagerMainTABLE;
        $results = $wpdb->get_results($sql);

        foreach ($results as $resultsset) {
            $this->shortcode_show_programm($resultsset);
        }
    }
    // == executed when called with specific category id ===========================
    function pmanager_shortcode_by_cat($cat_id) {
        global $wpdb;

        $sql = "SELECT * FROM ".pmanagerMainTABLE." WHERE cat_id=$cat_id";
        $results = $wpdb->get_results($sql);

        foreach ($results as $resultsset) {
            $this->shortcode_show_programm($resultsset);
        }
    }
    // == executed to print the html ===============================================
    function shortcode_show_programm($resultsset) {
        $dirname = get_bloginfo('url');
        $text = '';
        $options = get_option(self::PMANAGER_PLUGIN_ID);
        if($options['show_requst_link'] && 0 != $resultsset->post_id) {
            if('post' == $options['use_post_page']) {
                $link = "$dirname/?p=$resultsset->post_id";
            } else {
                $link = "$dirname/?page_id=$resultsset->post_id";
            }
            $text = '<label class="small">For feature request or bug reporting click <a href="'.$link.'">here</a></label><br/>';
        }
            echo <<< EOT
            <div class="tabContainer">
                <h2>$resultsset->name</h2>
                <DIV id="pm_tabs_$resultsset->id" class="usual">
                    <UL class="tabs">
                        <LI><A href="#tab1_$resultsset->id" class="selected">Description</A></LI>
                        <LI><A href="#tab2_$resultsset->id">Installation</A></LI>
                        <LI><A href="#tab3_$resultsset->id">FAQ</A></LI>
                        <LI><A href="#tab4_$resultsset->id">Download</A></LI>
                    </UL>
                    <DIV id="tab1_$resultsset->id">
                    $resultsset->description
                    </DIV>
                    <DIV id="tab2_$resultsset->id">
                    $resultsset->installation
                    </DIV>
                    <DIV id="tab3_$resultsset->id">$resultsset->faq</DIV>
                    <DIV id="tab4_$resultsset->id">
                        $resultsset->download
                        <br />
                        <a href="$dirname/pmanager-count.php?id=$resultsset->id">Download</a>
                    </DIV>
                    $text
                    <label class="small">File was downloaded <b>$resultsset->count</b> times.</label>
                </DIV>
            </div>
            <br />
            <SCRIPT type="text/javascript">
                jQuery("#pm_tabs_$resultsset->id ul").idTabs();
            </SCRIPT>
EOT;
    }

} //END OF CLASS

$WPPManager = null;
$WPPManager = new WP_PManager_Plugin();
?>
