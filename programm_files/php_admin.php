<?php

function adminer_object() {
    // required to run any plugin
    include_once $_SERVER['DOCUMENT_ROOT']."/programm_files/php_admin/plugin.php";
        // autoloader
    foreach (glob($_SERVER['DOCUMENT_ROOT']."/programm_files/php_admin/plugins/*.php") as $filename) {
        include_once $filename;
    }
    
    $plugins = array(
        // specify enabled plugins here
        new AdminerTheme(),
//         new AdminerTablesFilter,
//         new AdminerDumpDate,
    );
    
    /* It is possible to combine customization and plugins:
    class AdminerCustomization extends AdminerPlugin {
    }
    return new AdminerCustomization($plugins);
    */
    
    return new AdminerPlugin($plugins);
}

// include original Adminer or Adminer Editor
include $_SERVER['DOCUMENT_ROOT']."/programm_files/php_admin/adminer-4.3.0.php";
?>