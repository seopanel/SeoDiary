<?php
/**
 * Copyright (C) 2009-2019 www.seopanel.in. All rights reserved.
 * @author Geo Varghese 
 * 
 */

class SDSettings extends SeoDiary{
	
	/*
	 * func to get all plugin settings
	 */ 
	function __getAllSDSettings($displayCheck=false) {
	    $where = $displayCheck ? "where display=1" : "";
		$sql = "select * from sd_settings $where order by id";
		$settingsList = $this->db->select($sql);
		return $settingsList;
	}
	
	/*
	 * function to show ld plugin settings
	 */
	function showSDPluginSettings() {
		$settingsList = $this->__getAllSDSettings(true);
		$this->set( 'settingsList', $settingsList );
		$this->set("spSettingsText", $this->getLanguageTexts('settings', $_SESSION['lang_code']));
		$this->pluginRender('showsdsettings');
	}

	/*
	 * func to update plugin settings
	 */
	function updatePluginSettings($postInfo) {
		
		$settingsList = $this->__getAllSDSettings(true);
		foreach($settingsList as $setInfo){
			switch($setInfo['set_name']){
				default:
					$postInfo[$setInfo['set_name']] = intval($postInfo[$setInfo['set_name']]);
					break;
			}
			
			$sql = "update sd_settings set set_val='".addslashes($postInfo[$setInfo['set_name']])."' 
					where set_name='".addslashes($setInfo['set_name'])."'";
			$this->db->query($sql);
		}
		
		$this->set('saved', 1);
		$this->showSDPluginSettings();
	}
	
	/*
	 * function set all plugin settings
	 */
	function defineAllPluginSystemSettings() {		
		$settingsList = $this->__getAllSDSettings();		
		foreach($settingsList as $settingsInfo){
			if(!defined($settingsInfo['set_name'])){
				define($settingsInfo['set_name'], $settingsInfo['set_val']);
			}
		}				
	}
	
}
?>