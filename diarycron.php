<?php
/**
 * Copyright (C) 2009-2019 www.seopanel.in. All rights reserved.
 * @author Geo Varghese
 *
 */

$dirpath = realpath(dirname(__FILE__));
$spLoadFile = "$dirpath/../../includes/sp-load.php";
$spLoadFileExist = true;
if (!file_exists($spLoadFile)) {
    $dirpath = str_ireplace('/plugins/SeoDiary/diarycron.php', '', $_SERVER['SCRIPT_FILENAME']);
    $spLoadFile = $dirpath."/includes/sp-load.php";
    $spLoadFileExist = !file_exists($spLoadFile) ? false : true;    
}

// check wheteher load file exists
if ($spLoadFileExist) {
    include_once($spLoadFile);
    /*if (empty($_SERVER['REQUEST_METHOD'])) {*/
    if (empty($_SERVER['REQUEST_METHOD'])) {
        
        include_once(SP_CTRLPATH."/seoplugins.ctrl.php");
        $controller = New SeoPluginsController();
        $pluginInfo = $controller->__getSeoPluginInfo('SeoDiary', 'name');
        $info['pid'] = $pluginInfo['id'];
        $info['action'] = "cronJob";
        $_GET['doc_type'] = 'export';     
        $controller->manageSeoPlugins($info, "get", true);
        
    /*} else {
    } else {
        showErrorMsg("<p style='color:red'>You don't have permission to access this page!</p>");
    }*/        
    }        
} else {
    echo "Seo Panel Bootstrap loader file not accessible!";
}
?>
