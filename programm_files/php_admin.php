<?php
	include_once('../config.php');
	
function adminer_object() {
    global $DOCUMENR_ROOT;
    // required to run any plugin
    include_once $DOCUMENR_ROOT.'/programm_files/php_admin/plugin.php';
        // autoloader
    foreach (glob($DOCUMENR_ROOT.'/programm_files/php_admin/plugins/*.php') as $filename) {
        include_once $filename;
    }
    
    $plugins = array(
        // specify enabled plugins here
        new AdminerTheme(),
        new AdminerTablesFilter,
        new AdminerDumpDate,
    );
    
    /* It is possible to combine customization and plugins:
    class AdminerCustomization extends AdminerPlugin {
    }
    return new AdminerCustomization($plugins);
    */
    
    return new AdminerPlugin($plugins);
}

// include original Adminer or Adminer Editor
	foreach (glob($DOCUMENR_ROOT.'/programm_files/php_admin/*.php') as $filename) 
	{
		if (mb_strpos($filename,'adminer'))
		{
		$e = explode('/adminer',$filename);
			if (isset($e[1]) AND !empty(preg_replace ('/[^0-9]/','',$e[1])))
			{
			$adminer_arr[preg_replace ('/[^0-9]/','',$e[1])] = $filename;
			}
		}
    }

    if (isset($adminer_arr))
    {
    arsort($adminer_arr);
    include array_shift($adminer_arr);
    }
    else
		{
		include $DOCUMENR_ROOT.'/programm_files/php_admin/adminer.php';
		}

?>