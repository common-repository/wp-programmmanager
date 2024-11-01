<?php

class PManager_Install {

    const pmanagerVersion = '1.2';
    const PMANAGER_PLUGIN_ID = "wp-pmanager-plugin";

    function PManager_Install() {
        $this->start();
    }

    function start() {
        if(!$this->pmanager_create_MainTable()) {
            $this->alterMainTable();
        }
        $this->pmanager_create_CatTable();
        $options = get_option(self::PMANAGER_PLUGIN_ID);
        if(!is_array($options)) {
            $this->pmanager_create_options();
        } else {
            $this->editOptions();
        }
    }

    function editOptions() {
        $options = get_option(self::PMANAGER_PLUGIN_ID);
        $options['version'] = self::pmanagerVersion;
        $options['show_cats'] = true;
        $options['show_requst_link'] = false;
        $options['use_post_page'] = "post";
        update_option(self::PMANAGER_PLUGIN_ID,$options);
    }

    function alterMainTable() {
        global $wpdb;
        $pmanagerMainTABLE = $wpdb->prefix.'pmanager_main';
        $pmanagerCatTABLE = $wpdb->prefix.'pmanager_cats';

        $sql = "ALTER TABLE $pmanagerMainTABLE ADD post_id int NOT NULL DEFAULT '0';";
        $wpdb->query($sql);

        $sql = "ALTER TABLE $pmanagerMainTABLE ADD cat_id int NOT NULL DEFAULT '0';";
        $wpdb->query($sql);

        $sql = "ALTER TABLE $pmanagerMainTABLE ADD FOREIGN KEY (cat_id) REFERENCES $pmanagerCatTABLE(id);";
        $wpdb->query($sql);
    }

    function pmanager_create_options() {
        $options = array();
        $options['version'] = self::pmanagerVersion;
        $options['tabcontrol_width'] = "98%";
        $options['tabcontrol_bordercolor'] = "#494e52";
        $options['tabcontrol_bordercolor_checkbox'] = false;
        $options['tabcontrol_bgcolor'] = "#636d76;";
        $options['tabcontrol_bgcolor_checkbox'] = false;
        $options['tabcontrol_border'] = "1px solid";

        $options['tabs_a_bordercolor'] = "#FFFFFF";
        $options['tabs_a_bordercolor_checkbox'] = false;
        $options['tabs_a_border'] = "1px solid";
        $options['tabs_a_bgcolor'] = "#464c54";
        $options['tabs_a_bgcolor_checkbox'] = false;
        $options['tabs_a_fontcolor'] = "#282e32";

        $options['tabs_h_bordercolor'] = "#2f343a";
        $options['tabs_h_bordercolor_checkbox'] = false;
        $options['tabs_h_bgcolor'] = "#2f343a";
        $options['tabs_h_bgcolor_checkbox'] = false;

        $options['tabs_bordercolor'] = "#464c54";
        $options['tabs_bordercolor_checkbox'] = false;
        $options['tabs_border'] = "1px solid";
        $options['tabs_bgcolor'] = "#464c54";
        $options['tabs_bgcolor_checkbox'] = false;
        $options['tabs_fontcolor'] = "#ffebb5";

        $options['content_bordercolor'] = "#464c54";
        $options['content_bordercolor_checkbox'] = false;
        $options['content_border'] = "1px solid";
        $options['content_bgcolor'] = "#ffffff";
        $options['content_bgcolor_checkbox'] = false;

        $options['show_cats'] = true;

        $options['show_requst_link'] = false;
        $options['use_post_page'] = "post";

        update_option(self::PMANAGER_PLUGIN_ID,$options);
    }

    // == Functions for creating the necessary Database Tables =====================
    function pmanager_create_MainTable() {
        global $wpdb;
        $pmanagerMainTABLE = $wpdb->prefix.'pmanager_main';
        $pmanagerCatTABLE = $wpdb->prefix.'pmanager_cats';

        $sql = "
		CREATE TABLE IF NOT EXISTS ".$pmanagerMainTABLE." (
			id bigint(20) NOT NULL auto_increment,
			name varchar(50) NOT NULL,
			description text NOT NULL,
                        installation text NOT NULL,
                        faq text NOT NULL,
                        download text NOT NULL,
                        link varchar(50) NOT NULL,
                        cat_id int NOT NULL DEFAULT '0',
                        post_id int NOT NULL DEFAULT '0',
                        count int NOT NULL DEFAULT '0',
			PRIMARY KEY (id),
                        FOREIGN KEY (cat_id) REFERENCES ".$pmanagerCatTABLE."(id)
                );";

        $ergebnis = $wpdb->query($sql);
        return $ergebnis;
    }

    function pmanager_create_CatTable() {
        global $wpdb;
        $pmanagerCatTABLE = $wpdb->prefix.'pmanager_cats';

        $status = $pmanagerCatTABLE.' erfolgreich erstellt.';

        $sql = "
		CREATE TABLE IF NOT EXISTS ".$pmanagerCatTABLE." (
			id bigint(20) NOT NULL auto_increment,
			name varchar(20) NOT NULL,
			PRIMARY KEY (id)
		);";

        $ergebis = $wpdb->query($sql);
        return $ergebis;
    }
}
?>
