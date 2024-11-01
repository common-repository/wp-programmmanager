<?php

class PManager_Settings {

    function PManager_Settings() {
        wp_enqueue_script('common');
	wp_enqueue_script('wp-lists');
	wp_enqueue_script('postbox');
        
        //add filter for WordPress 2.8 changed backend box system !
        add_filter('screen_layout_columns', array(&$this, 'on_screen_layout_columns'), 10, 2);
        //add several metaboxes now, all metaboxes registered during load page can be switched off/on at "Screen Options" automatically, nothing special to do therefore
        add_meta_box('wp-pmanager-sidebox-1', 'Deinstall Plugin', array(&$this, 'on_sidebox_1_content'), $this->pagehook, 'side', 'core');
        add_meta_box('wp-pmanager-contentbox-1', 'Content Settings', array(&$this, 'on_contentbox_1_content'), $this->pagehook, 'normal', 'core');
        //add_meta_box('wp-pmanager-contentbox-2', 'Tabs Settings', array(&$this, 'on_contentbox_2_content'), $this->pagehook, 'normal', 'core');
        //add_meta_box('wp-pmanager-contentbox-3', 'TabControl Settings', array(&$this, 'on_contentbox_3_content'), $this->pagehook, 'normal', 'core');
    }

    function on_screen_layout_columns($columns, $screen) {
        if ($screen == $this->pagehook) {
            $columns[$this->pagehook] = 2;
        }
        return $columns;
    }

    function controller() {
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
        $options = get_option(self::PMANAGER_PLUGIN_ID);
        ?>
<table class="widefat">
    <tr>
        <td><label><b>Border-Color:</b></label></td>
        <td>
            <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
            <input class="color {hash:true}" value="<?php echo $options['content_bordercolor']; ?>" name="content_bordercolor" size="8" /><br />
            <input type="checkbox" name="content_bordercolor_checkbox" value="check" <?php if($options['content_bordercolor_checkbox']) {
                        echo 'checked="checked"';
                           }?>><span id="pmanager-small-font">Ignore this.</span>
        </td>
    </tr>
    <tr>
        <td><label><b>Border:</b></label></td>
        <td>
            <input value="<?php echo $options['content_border']; ?>" name="content_border">
        </td>
    </tr>
    <tr>
        <td><label><b>Background-Color:</b></label></td>
        <td>
            <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
            <input class="color {hash:true}" value="<?php echo $options['content_bgcolor']; ?>" name="content_bgcolor" size="8" /><br />
            <input type="checkbox" name="content_bgcolor_checkbox" value="check" <?php if($options['content_bgcolor_checkbox']) {
                        echo 'checked="checked"';
                           }?>><span id="pmanager-small-font">Ignore this.</span>
        </td>
    </tr>
</table>
        <?php
    }
    function on_contentbox_2_content($data) {
		$options = get_option(self::PMANAGER_PLUGIN_ID);
		?>
		<h4>Tab</h4>
                    <table class="widefat">
                        <tr>
                            <td><label><b>Border-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $options['tabs_bordercolor']; ?>" name="tabs_bordercolor" size="8" /><br />
                                <input type="checkbox" name="tabs_bordercolor_checkbox" value="check" <?php if($options['tabs_bordercolor_checkbox']) { echo 'checked="checked"'; }?>><span id="pmanager-small-font">Ignore this.</span>
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Border:</b></label></td>
                            <td>
                                <input value="<?php echo $options['tabs_border']; ?>" name="tabs_border" />
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Background-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $options['tabs_bgcolor']; ?>" name="tabs_bgcolor" size="8" /><br />
                                <input type="checkbox" name="tabs_bgcolor_checkbox" value="check" <?php if($options['tabs_bgcolor_checkbox']) { echo 'checked="checked"'; }?>><span id="pmanager-small-font">Ignore this.</span>
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Font-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $options['tabs_fontcolor']; ?>" name="tabs_fontcolor" size="8" /><br />
                            </td>
                        </tr>
                    </table>
                    <h4>Tab:Hover</h4>
                    <table class="widefat">
                        <tr>
                            <td><label><b>Border-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $options['tabs_h_bordercolor']; ?>" name="tabs_h_bordercolor" size="8" /><br />
                                <input type="checkbox" name="tabs_h_bordercolor_checkbox" value="check" <?php if($options['tabs_h_bordercolor_checkbox']) { echo 'checked="checked"'; }?>><span id="pmanager-small-font">Ignore this.</span>
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Background-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $options['tabs_h_bgcolor']; ?>" name="tabs_h_bgcolor" size="8" /><br />
                                <input type="checkbox" name="tabs_h_bgcolor_checkbox" value="check" <?php if($options['tabs_h_bgcolor_checkbox']) { echo 'checked="checked"'; }?>><span id="pmanager-small-font">Ignore this.</span>
                            </td>
                        </tr>
                    </table>
                    <h4>Tab:Active</h4>
                    <table class="widefat">
                        <tr>
                            <td><label><b>Border-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $options['tabs_a_bordercolor']; ?>" name="tabs_a_bordercolor" size="8" /><br />
                                <input type="checkbox" name="tabs_a_bordercolor_checkbox" value="check" <?php if($options['tabs_a_bordercolor_checkbox']) { echo 'checked="checked"'; }?>><span id="pmanager-small-font">Ignore this.</span>
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Border:</b></label></td>
                            <td>
                                <input value="<?php echo $options['tabs_a_border']; ?>" name="tabs_a_border" />
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Background-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $options['tabs_a_bgcolor']; ?>" name="tabs_a_bgcolor" size="8" /><br />
                                <input type="checkbox" name="tabs_a_bgcolor_checkbox" value="check" <?php if($options['tabs_a_bgcolor_checkbox']) { echo 'checked="checked"'; }?>><span id="pmanager-small-font">Ignore this.</span>
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Font-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $options['tabs_a_fontcolor']; ?>" name="tabs_a_fontcolor" size="8" /><br />
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
                                <input value="<?=$options['tabcontrol_width']; ?>" name="tabcontrol_width">
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>BorderColor:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $options['tabcontrol_bordercolor']; ?>" name="tabcontrol_bordercolor" size="8" /><br />
                                <input type="checkbox" name="tabcontrol_bordercolor_checkbox" value="check" <?php if($options['tabcontrol_bordercolor_checkbox']) { echo 'checked="checked"'; }?>><span id="pmanager-small-font">Ignore this.</span>
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Border:</b></label></td>
                            <td>
                                <input value="<?php echo $options['tabcontrol_border']; ?>" name="tabs_border" />
                            </td>
                        </tr>
                        <tr>
                            <td><label><b>Background-Color:</b></label></td>
                            <td>
                                <script type="text/javascript" src="<?php echo get_bloginfo('url') . '/' . PLUGINDIR . '/wp-programmmanager'; ?>/jscolor/jscolor.js"></script>
                                <input class="color {hash:true}" value="<?php echo $options['tabcontrol_bgcolor']; ?>" name="tabcontrol_bgcolor" size="8" /><br />
                                <input type="checkbox" name="tabcontrol_bgcolor_checkbox" value="check" <?php if($options['tabcontrol_bgcolor_checkbox']) { echo 'checked="checked"'; }?>><span id="pmanager-small-font">Ignore this.</span>
                            </td>
                        </tr>
                    </table>
		<?php
	}
}
?>
