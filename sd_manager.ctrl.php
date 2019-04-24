<?php
/**
 * Copyright(C) 2009-2019 www.seopanel.in. All rights reserved.
 * @author Geo Varghese 
 * 
 */
class SD_Manager extends SeoDiary {
	
	/*
	 * show projects list to manage
	 */

	var $cronJob = false;

	function showSDList($info = "") {
		$userId = isLoggedIn ();
		$this->set ( 'post', $info );
		
		if (isAdmin ()) {
			$this->set ( 'isAdmin', 1 );
		} else {
			$cond .= " and d.assigned_user_id=$userId";
			
			$this->set ( 'isAdmin', 0 );
		}
		
		$cond .= ! empty ( $info ['project_id'] ) ? " and d.project_id=" . intval ( $info ['project_id'] ) : "";
		$cond .= ! empty ( $info ['category_id'] ) ? " and d.category_id=" . intval ( $info ['category_id'] ) : "";
		$cond .= ! empty ( $info ['assigned_user_id'] ) ? " and d.assigned_user_id=" . intval ( $info ['assigned_user_id'] ) : "";
		$cond .= ! empty ( $info ['keyword'] ) ? " and (title LIKE '%" . addslashes ( $info ['keyword'] ) . "%' OR d.description LIKE '%" . addslashes ( $info ['keyword'] ) . "%')" : "";
		$cond .= ! empty ( $info ['status'] ) ? " and d.status=" . intval ( $info ['status'] ) : "";
		$cond .= ! empty ( $info ['sort_col'] ) ? " order by " . addslashes ( $info ['sort_col'] ) : "";
		$cond .= ! empty ( $info ['sort_val'] ) ? " " . addslashes ( $info ['sort_val'] ) : "";
		
		$info ['user_id'] = intval ( $info ['assigned_user_id'] );
		$pgScriptPath = PLUGIN_SCRIPT_URL . "&action=diaryManager";
		$sql = "select d.*,p.name project_name, c.label category_label from sd_seo_diary d, sd_category c, sd_projects p where d.project_id=p.id and d.category_id=c.id $cond ";
		
		$userCtrler = new UserController ();
		$userList = $userCtrler->__getAllUsers ();
		$this->set ( 'userList', $userList );
		$userIdList = [ ];
		
		foreach ( $userList as $userInfo ) {
			$userIdList [$userInfo ['id']] = $userInfo;
		}
		
		$this->set ( 'userIdList', $userIdList );
		
		$projectCtrler = $this->createHelper ( 'Project' );
		$projectList = $projectCtrler->__getAllProjects ( $userId, true );
		$this->set ( 'projectList', $projectList );
		
		$categoryList = $this->selectDiaryCategory ();
		$this->set ( 'categoryList', $categoryList );
		
		$statusList = $this->selectDiaryStatus ();
		$this->set ( 'statusList', $statusList );
		
		// pagination setup
		$this->db->query ( $sql, true );
		$this->paging->setDivClass ( 'pagingdiv' );
		$this->paging->loadPaging ( $this->db->noRows, SP_PAGINGNO );
		$pagingDiv = $this->paging->printPages ( $pgScriptPath, '', 'scriptDoLoad', 'content', 'layout=ajax' );
		$this->set ( 'pagingDiv', $pagingDiv );
		$sql .= " limit " . $this->paging->start . "," . $this->paging->per_page;
		
		$projectList = $this->db->select ( $sql );
		$this->set ( 'list', $projectList );
		$this->set ( 'pageNo', $_GET ['pageno'] );
		$this->pluginRender ( 'diary_manager' );
	}
	
	/*
	 * func to create new project
	 */
	function newDiary($info = '') {
		
		// get all users
		$userId = isLoggedIn ();
		$userCtrler = new UserController ();
		$userList = $userCtrler->__getAllUsers ();
		$this->set ( 'userList', $userList );
		
		$projectCtrler = $this->createHelper ( 'Project' );
		$projectList = $projectCtrler->__getAllProjects ( $userId, true );
		$this->set ( 'projectList', $projectList );
		
		$categoryList = $this->selectDiaryCategory ();
		$this->set ( 'categoryList', $categoryList );
		$this->set ( 'isAdmin', 1 );
		
		$this->pluginRender ( 'new_diary' );
	}
	
	/*
	 * func to create project
	 */
	function createDiary($listInfo) {
		$this->set ( 'post', $listInfo );
		$errMsg ['project_id'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['project_id'] ) );
		$errMsg ['category_id'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['category_id'] ) );
		$errMsg ['title'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['title'] ) );
		$errMsg ['description'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['description'] ) );
		$errMsg ['assigned_user_id'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['assigned_user_id'] ) );
		$errMsg ['due_date'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['due_date'] ) );
		$errMsg ['status'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['status'] ) );
		if (isAdmin () || SD_ENABLE_EMAIL_NOTIFICATION) {
			$userId = $listInfo ['assigned_user_id'];
			$subject = "your have one new assingned diary ";
			$content = $this->pluginRender('mailview', 'ajax', false);
			$userController = new UserController ();
			$userInfo = $userController->__getUserInfo ( $userId );
			$adminInfo = $userController->__getAdminInfo ();
			$userName = $userInfo ['first_name'] . "-" . $userInfo ['last_name'];
			$this->set ( 'userName', $userName );
			
			if (! sendMail ( $adminInfo ['email'], $userName, $userInfo ['email'], $subject, $content )) {
				echo "Notifiaction Mail send successfully to " . $userInfo ['email'] . "\n";
				} else {
					echo 'An internal error occured while sending mail!';
			}
		}
		
		if (! $this->validate->flagErr) {
			
			$sql = "INSERT INTO `sd_seo_diary`(`project_id`, `assigned_user_id`, `category_id`, `title`, `description`, `due_date`, `status`) 
					VALUES('" . intval ( $listInfo ['project_id'] ) . "', '" . addslashes ( $listInfo ['assigned_user_id'] ) . "', 
					'" . addslashes ( $listInfo ['category_id'] ) . "',  '" . addslashes ( $listInfo ['title'] ) . "',
					'" . addslashes ( $listInfo ['description'] ) . "', '" . addslashes ( $listInfo ['due_date'] ) . "',
					'" . addslashes ( $listInfo ['status'] ) . "')";
			$this->db->query ( $sql );
			$this->showSDList ();
			exit ();
		}
		
		$this->set ( 'errMsg', $errMsg );
		$this->newDiary ( $listInfo );
	}
	
	/*
	 * func to edit diary
	 */
	function editDiary($diaryId, $listInfo = '') {
		
		if (! empty ( $diaryId )) {
			
			if (empty ( $listInfo )) {
				$listInfo = $this->__getDiaryInfo ( $diaryId );
			}
			
			$this->set ( 'post', $listInfo );
			
			$userCtrler = new UserController ();
			$userList = $userCtrler->__getAllUsers ();
			$this->set ( 'userList', $userList );
			
			$userId = isLoggedIn ();
			$projectCtrler = $this->createHelper ( 'Project' );
			$projectList = $projectCtrler->__getAllProjects ( $userId, true );
			$this->set ( 'projectList', $projectList );
			
			$categoryList = $this->selectDiaryCategory ();
			$this->set ( 'categoryList', $categoryList );
			$this->set ( 'isAdmin', 1 );
			
			$this->pluginRender ( 'edit_diary' );
		}
	}
	
	/*
	 * func to update project
	 */
	function updateDiary($listInfo) {
		
		if (isAdmin ()) {
			$diaryId = empty ( $listInfo ['project_id'] ) ? isLoggedIn () : intval ( $listInfo ['project_id'] );
		} else {
			$diaryId = isLoggedIn ();
		}
		
		$this->set ( 'post', $listInfo );
		
		$errMsg ['project_id'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['project_id'] ) );
		$errMsg ['category_id'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['category_id'] ) );
		$errMsg ['title'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['title'] ) );
		$errMsg ['description'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['description'] ) );
		$errMsg ['assigned_user_id'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['assigned_user_id'] ) );
		$errMsg ['due_date'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['due_date'] ) );
		$errMsg ['status'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['status'] ) );
		if (! $this->validate->flagErr) {
			
			if ($this->__checkTitle ( $listInfo ['tile'], $diaryId )) {
				$errMsg ['title'] = formatErrorMsg ( 'Diary already exist' );
				$this->validate->flagErr = true;
			}
			
			if (isAdmin () || SD_ENABLE_EMAIL_NOTIFICATION) {
				$userId = $listInfo ['assigned_user_id'];
				$subject = "your assingned diary was changed";
				$content = $this->getViewContent('mailview', 'ajax', false);
				$userController = new UserController ();
				$userInfo = $userController->__getUserInfo ( $userId );
				$adminInfo = $userController->__getAdminInfo ();
				$userName = $userInfo ['first_name'] . "-" . $userInfo ['last_name'];
				$this->set ( 'userName', $userName );
				
				if (! sendMail ( $adminInfo ['email'], $userName, $userInfo ['email'], $subject, $content )) {
					echo "Reports send successfully to " . $userInfo ['email'] . "\n";
				} else {
					echo 'An internal error occured while sending mail!';
				}
			}
			
			if (! $this->validate->flagErr) {
				
				$sql = "update sd_seo_diary set project_id = " . intval ( $listInfo ['project_id'] ) . ", category_id = " . intval ( $listInfo ['category_id'] ) . ", title = '" . addslashes ( $listInfo ['title'] ) . "', description = '" . addslashes ( $listInfo ['description'] ) . "', assigned_user_id = '" . addslashes ( $listInfo ['assigned_user_id'] ) . "', due_date = '" . addslashes ( $listInfo ['due_date'] ) . "', update_time = '" . date ( "Y-m-d" ) . "', status = '" . addslashes ( $listInfo ['status'] ) . "' where id=" . intval ( $listInfo ['id'] );
				$this->db->query ( $sql );
				$this->showSDList ();
				exit ();
			}
		}
		$this->set ( 'errMsg', $errMsg );
		$this->editProject ( $listInfo ['id'], $listInfo );
	}
	
	/*
	 * func to delete project
	 */
	function deleteDiary($project_id) {
		$project_id = intval ( $project_id );
		$sql = "delete from sd_seo_diary where id=" . intval ( $project_id );
		$this->db->query ( $sql );
		$this->showSDList ();
	}
	/*
	 * func to create new project
	 */
	function newDiaryComments($info = '') {
		$this->set ( 'post', $info );
		$userId = isLoggedIn ();
		
		$diaryNameList = $this->__selectDiaryName ();
		$this->set ( 'diaryNameList', $diaryNameList );
		
		if (empty ( $info ['diary_id'] )) {
			$diaryId = $diaryNameList [0] ['id'];
		} else {			
			$diaryId = $info ['diary_id'];
		}
		
		$selectDiaryDesc = $this->selectDiaryDesc ( " where id=" . $diaryId );
		$this->set ( 'selectDiaryDesc', $selectDiaryDesc );

		$userCtrler = new UserController ();
		$userList = $userCtrler->__getAllUsers ();
		$this->set ( 'userList', $userList );
		$userIdList = [ ];
		
		foreach ( $userList as $userInfo ) {
			$userIdList [$userInfo ['id']] = $userInfo;
		}
		
		$this->set ( 'userIdList', $userIdList );
		

		$diaryCommentList = $this->showDiaryComments ( "where d.id=c.diary_id and d.id =" . $diaryId . " and c.user_id =" . $userId );
		$this->set ( 'diaryCommentList', $diaryCommentList );
		$this->set ( 'isAdmin', 1 );
	
		$this->pluginRender ( 'diary_comments' );
	}
	
	/*
	 * func to create new project
	 */
	function showProjectSummery($info = '') {
		$this->set ( 'post', $info );
		$userId = isLoggedIn ();
		$projectCtrler = $this->createHelper ( 'Project' );
		$projectList = $projectCtrler->__getAllProjects ( $userId, true );
		$this->set ( 'projectList', $projectList );
		
		if (empty ( $info ['project_id'] )) {
			$projectId = $projectList [0] ['id'];
		} else {
			$projectId = $info ['project_id'];
		}
		
		$diaryNameList = $this->selectSummery ( " WHERE d.project_id = p.id and p.id=" . $projectId );
		
		foreach ( $diaryNameList as $i => $listInfo ) {
			$countList = $this->selectCommentCount ( "where diary_id= d.id and d.id=" . $listInfo ['id'] );
			$listInfo ['comment_count'] = $countList ['comment_count'];
			$diaryNameList [$i] = $listInfo;
		}
		
		$this->set ( 'diaryNameList', $diaryNameList );
		$selectProjectDesc = $this->selectProjectDesc ( " where `id`=" . $projectId );
		$this->set ( 'selectProjectDesc', $selectProjectDesc );
		
		$this->pluginRender ( 'project_summery' );
	}
	
	/*
	 * func to create project
	 */
	function createDiaryComment($listInfo) {
		$userId = isLoggedIn ();
		$this->set ( 'post', $listInfo );
		$errMsg ['diary_id'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['diary_id'] ) );
		$errMsg ['comments'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['comments'] ) );
		
		if (! $this->validate->flagErr) {
			
			$sql = "INSERT INTO `sd_diary_comments`( `diary_id`, `user_id`, `comments`,  `updated_time`) VALUES ('" . intval ( $listInfo ['diary_id'] ) . "','" . addslashes ( $userId ) . "', 
					'" . addslashes ( $listInfo ['comments'] ) . "','". date("Y-m-d H:i:s")."')";
			$this->db->query ( $sql );
			$this->newDiaryComments ();
			exit ();
		}
		
		$this->set ( 'errMsg', $errMsg );
		$this->newDiaryComments ( $listInfo );
	}
	
	/*
	 * show tasks assigned users
	 */
	function showTaskList($info = "") {
		$this->set ( 'post', $info );
		$userId = isLoggedIn ();
		if (isAdmin ()) {
			$this->set ( 'isAdmin', 1 );
		} else {
			$cond .= " and d.assigned_user_id=$userId";
			
			$this->set ( 'isAdmin', 0 );
		}
		
		$cond .= ! empty ( $info ['project_id'] ) ? " and d.project_id=" . intval ( $info ['project_id'] ) : "";
		$cond .= ! empty ( $info ['keyword'] ) ? " and (title LIKE '%" . addslashes ( $info ['keyword'] ) . "%' OR d.description LIKE '%" . addslashes ( $info ['keyword'] ) . "%')" : "";
		$cond .= ! empty ( $info ['status'] ) ? " and d.status=" . intval ( $info ['status'] ) : "";
		$cond .= ! empty ( $info ['sort_col'] ) ? " order by " . addslashes ( $info ['sort_col'] ) : "";
		$cond .= ! empty ( $info ['sort_val'] ) ? " " . addslashes ( $info ['sort_val'] ) : "";
		
		$info ['user_id'] = intval ( $info ['assigned_user_id'] );
		$pgScriptPath = PLUGIN_SCRIPT_URL . "&action=diaryManager";
		$sql = "select d.*,p.name project_name, c.label category_label from sd_seo_diary d, sd_category c, sd_projects p where d.project_id=p.id and d.category_id=c.id $cond ";
		
		$userCtrler = new UserController ();
		$userList = $userCtrler->__getAllUsers ();
		$this->set ( 'userList', $userList );
		$userIdList = [ ];
		
		foreach ( $userList as $userInfo ) {
			$userIdList [$userInfo ['id']] = $userInfo;
		}
		
		$this->set ( 'userIdList', $userIdList );
		
		$projectCtrler = $this->createHelper ( 'Project' );
		$projectList = $projectCtrler->__getAllProjects ( $userId, true );
		$this->set ( 'projectList', $projectList );
		
		$categoryList = $this->selectDiaryCategory ();
		$this->set ( 'categoryList', $categoryList );
		
		$statusList = $this->selectDiaryStatus ();
		$this->set ( 'statusList', $statusList );
		
		// pagination setup
		$this->db->query ( $sql, true );
		$this->paging->setDivClass ( 'pagingdiv' );
		$this->paging->loadPaging ( $this->db->noRows, SP_PAGINGNO );
		$pagingDiv = $this->paging->printPages ( $pgScriptPath, '', 'scriptDoLoad', 'content', 'layout=ajax' );
		$this->set ( 'pagingDiv', $pagingDiv );
		$sql .= " limit " . $this->paging->start . "," . $this->paging->per_page;
		
		$projectList = $this->db->select ( $sql );
		$this->set ( 'list', $projectList );
		$this->set ( 'pageNo', $_GET ['pageno'] );
		$this->pluginRender ( 'my_task' );
	}
	
	/*
	 * func to get all category type
	 */
	function showDiaryComments($condtions = '') {
		$sql = "select c.*, d.id ,c.updated_time time from sd_diary_comments c, sd_seo_diary d ";
		$sql .= empty ( $condtions ) ? "" : $condtions;
		$diaryCommentList = $this->db->select ( $sql );
		return $diaryCommentList;
	}
	/*
	 * func to get all category type
	 */
	function __selectDiaryName($condtions = '') {
		$sql = "select id, title, description from sd_seo_diary";
		if (! $isAdminCheck || ! isAdmin ()) {
			if (! empty ( $userId ))
				$sql .= " and project_id=" . intval ( $userId );
		}
		
		// if search string is not empty
		if (! empty ( $searchName )) {
			$sql .= " and (title like '%" . addslashes ( $searchName ) . "%' or url like '%" . addslashes ( $searchName ) . "%')";
		}
		
		$sql .= " order by title";
		$diaryNameList = $this->db->select ( $sql );
		return $diaryNameList;
	}
	/*
	 * func to get all diary description
	 */
	function selectDiaryDesc($condtions = '') {
		$sql = "select `id`,`description` FROM `sd_seo_diary`";
		$sql .= empty ( $condtions ) ? "" : $condtions;
		$selectDiaryDesc = $this->db->select ( $sql );
		return $selectDiaryDesc;
	}
	/*
	 * func to get all category type
	 */
	function selectDiaryCategory($condtions = '') {
		$sql = "select id, label from sd_category";
		$sql .= empty ( $condtions ) ? "" : $condtions;
		$categoryList = $this->db->select ( $sql );
		return $categoryList;
	}
	
	/*
	 * func to get all category type
	 */
	function selectCommentCount($condtions = '') {
		$sql = "SELECT count(*) as comment_count, d.id from sd_diary_comments, sd_seo_diary d ";
		$sql .= empty ( $condtions ) ? "" : $condtions;
		$countList = $this->db->select ( $sql, true );
		return $countList;
	}
	
	/*
	 * func to get all status
	 */
	function selectDiaryStatus($condtions = '') {
		$sql = "select id, status from sd_seo_diary ";
		$sql .= empty ( $condtions ) ? "" : $condtions;
		$statusList = $this->db->select ( $sql );
		return $statusList;
	}
	
	/*
	 * func to get all category type
	 */
	function selectDiaryName($condtions = '') {
		$sql = "select id, title, description from sd_seo_diary";
		$sql .= empty ( $condtions ) ? "" : $condtions;
		$diaryNameList = $this->db->select ( $sql );
		return $diaryNameList;
	}
	/*
	 * func to get all category type
	 */
	function selectSummery($condtions = '') {
		$sql = "select d.*,p.id project_id, d.title, d.description from sd_projects p, sd_seo_diary d ";
		$sql .= empty ( $condtions ) ? "" : $condtions;
		$diaryNameList = $this->db->select ( $sql );
		return $diaryNameList;
	}
	
	/*
	 * func to get all category type
	 */
	function selectProjectDesc($condtions = '') {
		$sql = "SELECT `id`, `name`, `description` FROM `sd_projects`";
		$sql .= empty ( $condtions ) ? "" : $condtions;
		$selectProjectDesc = $this->db->select ( $sql );
		return $selectProjectDesc;
	}
	/*
	 * function to check name of project already existing
	 */
	function __checkTitle($title, $diaryId = 0) {
		$diaryId = intval ( $diaryId );
		$sql = "select id from sd_seo_diary where title='" . addslashes ( $title ) . "'";
		$sql .= empty ( $diaryId ) ? " and id=$diaryId" : "";
		$listInfo = $this->db->select ( $sql, true );
		return empty ( $listInfo ['id'] ) ? false : $listInfo ['id'];
	}
	
	/*
	 * func to get project info
	 */
	function __getDiaryInfo($diaryId) {
		$sql = "select d.*,p.name project_name from sd_seo_diary d,sd_projects p where d.project_id=p.id and d.id=" . intval ( $diaryId );
		$info = $this->db->select ( $sql, true );
		return $info;
	}


	/**
	 * function to execute cron job
	 */
	function startCronJob() {
		$this->cronJob = true;
		$sql = "SELECT `id`,`assigned_user_id`, `due_date`, `status` FROM `sd_seo_diary` WHERE `status`= 'new' or `sd_seo_diary`.`status`='inprogress'";
		$diaryList = $this->db->select($sql);

		if (count($diaryList) > 0) {
			foreach ($diaryList as $diaryListInfo) {
				$diaryId =$diaryListInfo['id'];
				$this->generateDairyList($diaryId);
				
			}
		} else {
			echo "Diary List generated for all the projects!";
		}

	}

	/*
	 * function to generate Report
	 */
	function generateDairyList($diaryId) {	

		$datetime = new DateTime(date('Y-m-d'));
		$datetime->modify('+1 day');
		$datetime->format('Y-m-d');
		$diaryInfo = $this->__getDiaryInfo($diaryId);


            if (($diaryInfo['status'] == "new" || "inprogress") || ($diaryInfo['due_date'] > $datetime)) {
                	$userId = $diaryInfo ['assigned_user_id'];
                    $subject = "your assingned diary was changed";
                    $content = $this->getViewContent('mailview', 'ajax', false);
                    $userController = new UserController ();
                    $userInfo = $userController->__getUserInfo ( $userId );
                    $adminInfo = $userController->__getAdminInfo ();
                    $userName = $userInfo ['first_name'] . "-" . $userInfo ['last_name'];
                    $this->set ( 'userName', $userName );
                    
            	if (! sendMail ( $adminInfo ['email'], $userName, $userInfo ['email'], $subject, $content )) {
                echo "Reports send successfully to " . $userInfo ['email'] . "\n";
             	} else {
                        echo 'An internal error occured while sending mail!';
                       }
             ?><br><?php print $adminInfo ['email']; 
			             print  $userName;
			             print  $userInfo ['email'];
			             print  $subject;
			             print  $content;
         }
		}
}
	    