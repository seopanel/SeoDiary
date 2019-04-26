<?php
/**
 * Copyright (C) 2009-2019 www.seopanel.in. All rights reserved.
 * @author Geo Varghese
 *
 */

// include plugins controller if not included
include_once (SP_CTRLPATH . '/seoplugins.ctrl.php');
class SeoDiary extends SeoPluginsController {
	
	// plugin settings controller object
	var $settingsCtrler;
	
	// the plugin text database table
	var $textTable = "texts";
	
	// the plugin text category
	var $textCategory = "seodiary";
	
	// plugin directory name
	var $directoryName = "SeoDiary";
	
	/*
	 * function to init plugin details before each plugin action
	 */
	function initPlugin($data) {
		$this->setPluginTextsForRender ( $this->textCategory, $this->textTable );
		$this->set ( 'pluginText', $this->pluginText );
		$this->set ( 'spTextPanel', $this->getLanguageTexts('panel', $_SESSION['lang_code']));
        $settingsCtrler = $this->createHelper('SDSettings');
        $settingsCtrler->defineAllPluginSystemSettings();
		
		if (! defined ( 'PLUGIN_PATH' )) {
			define ( 'PLUGIN_PATH', $this->pluginPath );
		}
	}
	
	/*
	 * function to show the first page while access plugin
	 */
	function index($data) {
		if (isAdmin() || SD_ALLOW_USER_PROJECTS) {
			$this->projectManager( $data );
		} else {
			$this->myTasks($data);
		}
	}
	
	function projectManager($data) {
		$projectCtrler = $this->createHelper ( 'Project' );
		$projectCtrler->showProjectsManager ( $data );
	}
	
	/*
	 * func to show create new project form
	 */
	function newProject($data) {
		$projectCtrler = $this->createHelper ( 'Project' );
		$projectCtrler->newProject ( $data );
	}
	
	/*
	 * func to create new project
	 */
	function createProject($data) {
		$projectCtrler = $this->createHelper ( 'Project' );
		$projectCtrler->createProject ( $data );
	}
	
	/*
	 * func to show edit project form
	 */
	function editProject($data) {
		$projectCtrler = $this->createHelper ( 'Project' );
		$projectCtrler->editProject ( $data ['project_id'] );
	}
	
	/*
	 * func to update project
	 */
	function updateProject($data) {
		$projectCtrler = $this->createHelper ( 'Project' );
		$projectCtrler->updateProject ( $data );
	}
	
	/*
	 * func to delete project
	 */
	function deleteProject($data) {
		$projectCtrler = $this->createHelper ( 'Project' );
		$projectCtrler->deleteProject ( $data ['project_id'] );
	}
	
	/*
	 * function to activate project
	 */
	function Activate($data) {
		if (! empty ( $data ['project_id'] )) {
			$ctrler = $this->createHelper ( 'Project' );
			$ctrler->__changeStatus ( $data ['project_id'], 1 );
			$ctrler->showProjectsManager ();
		}
	}
	
	/*
	 * function to deactivate project
	 */
	function Inactivate($data) {
		if (! empty ( $data ['project_id'] )) {
			$ctrler = $this->createHelper ( 'Project' );
			$ctrler->__changeStatus ( $data ['project_id'], 0 );
			$ctrler->showProjectsManager ();
		}
	}
	
	/*
	 * function to show the first page while access plugin
	 */
	function diaryManager($data) {
		$sdMgrCtrler = $this->createHelper ( 'SD_Manager' );
		$sdMgrCtrler->showSDList ( $data );
	}
	
	/*
	 * function to show the first page while access plugin
	 */
	function myTasks($data) {
		$sdMgrCtrler = $this->createHelper ( 'SD_Manager' );
		$sdMgrCtrler->showTaskList ( $data );
	}
	
	/*
	 * function to show the first page of the Seo-Diary Manager
	 */
	function newDiary($data) {
		$sdMgrCtrler = $this->createHelper ( 'SD_Manager' );
		$sdMgrCtrler->newDiary ( $data );
	}

	
	/*
	 * func to create new project
	 */
	function createDiary($data) {
		$sdMgrCtrler = $this->createHelper ( 'SD_Manager' );
		$sdMgrCtrler->createDiary ( $data );
	}
	
	/*
	 * func to edit the diary
	 */
	function editDiary($data) {
		$sdMgrCtrler = $this->createHelper ( 'SD_Manager' );
		$sdMgrCtrler->editDiary ( $data ['project_id'] );
	}
	
	/*
	 * func to update project
	 */
	function updateDiary($data) {
		$sdMgrCtrler = $this->createHelper ( 'SD_Manager' );
		$sdMgrCtrler->updateDiary ( $data );
	}
	
	/*
	 * func to delete project
	 */
	function deleteDiary($data) {
		$sdMgrCtrler = $this->createHelper ( 'SD_Manager' );
		$sdMgrCtrler->deleteDiary ( $data ['project_id'] );
	}
	
	/*
	 * func to show create new diary comment form
	 */
	function newComment($data) {
		$sdMgrCtrler = $this->createHelper ( 'SD_Manager' );
		$sdMgrCtrler->newDiaryComments ( $data );
	}
	
	/*
	 * func to create new project
	 */
	function createComment($data) {
		$sdMgrCtrler = $this->createHelper ( 'SD_Manager' );
		$sdMgrCtrler->createDiaryComment ( $data );
	}
	
	/*
	 * function show system settings
	 */
	function settings($data) {
		checkAdminLoggedIn ();
		$settingsCtrler = $this->createHelper ( 'sdsettings' );
		$settingsCtrler->showSDPluginSettings ();
	}
	
	/*
	 * function to save plugin settings
	 */
	function updateSettings($data) {
		checkAdminLoggedIn ();
		$settingsCtrler = $this->createHelper ( 'sdsettings' );
		$settingsCtrler->updatePluginSettings ( $data );
	}
	
	/*
	 * function show system settings
	 */
	function projectSummery($data) {
		$sdMgrCtrler = $this->createHelper ( 'SD_Manager' );
		$sdMgrCtrler->showProjectSummery ( $data );
	}

		
	/*
	 * function for start sending status to social media networks like fb, twitter, linkedin using cron
	 */
	function cronjob() {
		$reportCtrler = $this->createHelper ( 'SD_Manager' );
		$reportCtrler->startCronJob( $data );
	}
}
?>