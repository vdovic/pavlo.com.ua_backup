<?php
/*
Plugin Name: DB-Toolkit Extentions Manager
Plugin URI: http://dbtoolkit.digilab.co.za
Description: Allows you to install and manage custom and add-on fieldtypes and form processors for DB-Toolkit.
Author: David Cramer
Version: 0.1.0.4
Author URI: http://dbtoolkit.digilab.co.za
*/

//validate DBT existance
if(!in_array( 'db-toolkit/plugincore.php', apply_filters( 'active_plugins', get_option( 'active_plugins' )))){
    add_action('admin_init', 'adminInitdbtExistance');
    function adminInitdbtExistance(){
        add_action('admin_notices', 'dependancyNoticeAlert');
    }
    function dependancyNoticeAlert() {

        if ( !current_user_can( 'manage_options' ) || !empty($_GET['tab']))
                return;
        ?>
        <div id="message" class="error">
            <h3>DB-Toolkit Extensions Manager requires DB-Toolkit to be installed and Activated. <a href="plugin-install.php?tab=search&type=term&s=DB-Toolkit" class="button" id="wpcom-connect">Learn More</a></h3>
        </div>
        <?php
        
    }

}else{

//initilize plugin and 
define('dbte', plugin_dir_path(__FILE__));
define('dbtep', plugin_dir_url(__FILE__));
require_once dbte.'functions.php';

//Apply Menus
add_action('admin_menu', 'dbt_menus');
// Apply Headers
if(is_admin ()){
    if(!empty($_GET['page'])){
        add_action('admin_head', 'dbte_headers');
    }
}

}
?>