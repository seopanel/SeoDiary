<?php
/**
 * Copyright(C) 2009-2019 www.seopanel.in. All rights reserved.
 * @author Geo Varghese 
 * 
 */
class Project extends SeoDiary {
	
	var $spTextSA;
	var $spTextPanel;
	
	function __construct() {
		parent::__construct();
		$this->spTextSA = $this->getLanguageTexts('siteauditor', $_SESSION['lang_code']);
	}
	
	/*
	 * show projects list to manage
	 */
	function showProjectsManager($info = '') {
		$userId = isLoggedIn();
		$info ['user_id'] = intval( $info ['user_id'] );
		$pgScriptPath = PLUGIN_SCRIPT_URL;
		$sql = "select sdp.*, w.name as website_name from sd_projects sdp, websites w where sdp.website_id=w.id";
		
		if(isAdmin()) {
			$userCtrler = new UserController();
			$userList = $userCtrler->__getAllUsers();
			$this->set( 'userList', $userList );
			
			$webSiteCtrler = new WebsiteController();
			$websiteList = $webSiteCtrler->__getAllWebsites();
			$this->set( 'websiteList', $websiteList );
			
			if(!empty( $info ['user_id'] )) {
				$pgScriptPath .= "&user_id=" . $info ['user_id'];
				$sql .= " and w.user_id=" . $info ['user_id'];
				$this->set( 'userId', $info ['user_id'] );
			}
			
			$this->set( 'isAdmin', 1 );
		} else {
			$sql .= " and w.user_id=$userId";
			$this->set( 'isAdmin', 0 );
		}
		
		// pagination setup
		$this->db->query( $sql, true );
		$this->paging->setDivClass( 'pagingdiv' );
		$this->paging->loadPaging( $this->db->noRows, SP_PAGINGNO );
		$pagingDiv = $this->paging->printPages( $pgScriptPath, '', 'scriptDoLoad', 'content', 'layout=ajax' );
		$this->set( 'pagingDiv', $pagingDiv );
		$sql .= " limit " . $this->paging->start . "," . $this->paging->per_page;
		
		$projectList = $this->db->select( $sql );
		$this->set( 'list', $projectList );
		$this->set( 'pageNo', $_GET ['pageno'] );
		$this->set('spTextSA', $this->spTextSA);
		$this->pluginRender( 'show_projects_manager' );
	}
	
	/*
	 * func to create new project
	 */
	function newProject($info = '') {
		$userId = isLoggedIn();
		$webSiteCtrler = new WebsiteController();
		$websiteList = $webSiteCtrler->__getAllWebsites( $userId, true );
		$this->set( 'websiteList', $websiteList );
		$this->pluginRender( 'new_project' );
	}
	
	/*
	 * func to create project
	 */
	function createProject($listInfo) {

		$this->set( 'post', $listInfo );
		$errMsg ['website_id'] = formatErrorMsg( $this->validate->checkBlank( $listInfo ['website_id'] ) );
		$errMsg ['name'] = formatErrorMsg( $this->validate->checkBlank( $listInfo ['name'] ) );
		$errMsg ['description'] = formatErrorMsg( $this->validate->checkBlank( $listInfo ['description'] ) );
		
		if(! $this->validate->flagErr) {			
			if(!$this->__checkProjectExists( $listInfo ['website_id'])) {
				$sql = "insert into sd_projects(website_id, name,description,status)
					values(" . intval( $listInfo ['website_id'] ) . ", '" . addslashes( $listInfo ['name'] ) . "','" 
					. addslashes( $listInfo ['description'] ) . "',1)";
				$this->db->query( $sql );
				$this->showProjectsManager();
				exit();
			} else {
				$errMsg ['name'] = formatErrorMsg( $this->spTextSA['projectalreadyexist'] );
			}
		}
		
		$this->set( 'errMsg', $errMsg );
		$this->newProject( $listInfo );
	}
	
	/*
	 * func to edit project
	 */
	function editProject($projectId, $listInfo = '') {
		$userId = isLoggedIn();
		
		if(!empty( $projectId )) {
			
			if(empty( $listInfo )) {
				$listInfo = $this->__getProjectInfo( $projectId );
			}
			
			$this->set( 'post', $listInfo );
			$webSiteCtrler = new WebsiteController();
			$websiteList = $webSiteCtrler->__getAllWebsites( $userId, true );
			$this->set( 'websiteList', $websiteList );
			$this->pluginRender( 'edit_project' );
		}
	}
	
	/*
	 * func to update project
	 */
	function updateProject($listInfo) {

		$this->set( 'post', $listInfo );
		$errMsg ['website_id'] = formatErrorMsg( $this->validate->checkBlank( $listInfo ['website_id'] ) );
		$errMsg ['name'] = formatErrorMsg( $this->validate->checkBlank( $listInfo ['name'] ) );
		$errMsg ['description'] = formatErrorMsg( $this->validate->checkBlank( $listInfo ['description'] ) );
		
		if(! $this->validate->flagErr) {			
			
			if($this->__checkProjectExists($listInfo['website_id'], $listInfo ['id'] )) {
				$this->validate->flagErr = true;
				$errMsg ['name'] = formatErrorMsg( $this->spTextSA['projectalreadyexist'] );
			}
			
			if(! $this->validate->flagErr) {				
				$sql = "update sd_projects set
								website_id = " . intval( $listInfo ['website_id'] ) . ",
								name = '" . addslashes( $listInfo ['name'] ) . "',
								description = '" . addslashes( $listInfo ['description'] ) . "'
								where id=" . intval( $listInfo ['id'] );
				$this->db->query( $sql );
				$this->showProjectsManager();
				exit();
			}
		}
		
		$this->set( 'errMsg', $errMsg );
		$this->editProject( $listInfo ['id'], $listInfo );
	}
	
	/*
	 * func to delete project
	 */
	function deleteProject($projectId) {
		$projectId = intval( $projectId );
		$sql = "delete from sd_projects where id=" . intval( $projectId );
		$this->db->query( $sql );
		$this->showProjectsManager();
	}
	
	/*
	 * func to change status
	 */
	function __changeStatus($projectId, $status) {
		$projectId = intval( $projectId );
		$status = intval( $status );
		$sql = "update sd_projects set status=$status where id=$projectId";
		$this->db->query( $sql );
	}

	/*
	 * function to check name of project already existing
	 */
	function __checkProjectExists($websiteId, $projectId = 0) {
		$websiteId = intval( $websiteId );
		$projectId = intval($projectId);
		$sql = "select id from sd_projects where website_id=$websiteId";
		$sql .= !empty( $projectId ) ? " and id!=$projectId" : "";
		$listInfo = $this->db->select( $sql, true );
		return !empty( $listInfo ['id'] ) ? $listInfo ['id'] : false;
	}

	function __getAllProjects($userId = '', $isAdminCheck = false, $searchName = '') {
		$sql = "select p.*,w.name as website_name from sd_projects p,websites w where p.website_id=w.id ";
		if(!$isAdminCheck || !isAdmin() ){
			if(!empty($userId)) $sql .= " and user_id=" . intval($userId);
		} 
		
		// if search string is not empty
		if (!empty($searchName)) {
			$sql .= " and (name like '%".addslashes($searchName)."%' or url like '%".addslashes($searchName)."%')";
		}
		
		$sql .= " order by name";
		$websiteList = $this->db->select($sql);
		return $websiteList;
	}
	
	/*
	 * func to get project info
	 */
	function __getProjectInfo($projectId) {
		$sql = "select p.*,w.name as website_name from sd_projects p,websites w where p.website_id=w.id and p.id=" . intval( $projectId );
		$info = $this->db->select( $sql, true );
		return $info;
	}
}