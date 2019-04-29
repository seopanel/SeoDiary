<?php
/**
 * Copyright(C) 2009-2019 www.seopanel.in. All rights reserved.
 * @author Geo Varghese 
 * 
 */
class SD_Manager extends SeoDiary {
	
	var $cronJob = false;
	var $statusList;

	function __construct() {
		parent::__construct();
		parent::initPlugin(true);
		$this->statusList = array(
			'new' => $this->pluginText['New'],
			'closed' => $this->pluginText['Closed'],
			'cancelled' => $this->pluginText['Cancelled'],
			'inprogress' => $this->pluginText['Inprogress'],
			'blocked' => $this->pluginText['Blocked'],
			'feedback' => $this->pluginText['Feedback'],
		);
	}
	
	function showSDList($info = "") {
		$userId = isLoggedIn ();
		$this->set ( 'post', $info );
		$cond = "";
		
		$projectCtrler = $this->createHelper ( 'Project' );
		$projectList = $projectCtrler->__getAllProjects ( $userId, true );
		$this->set ( 'projectList', $projectList );
		
		if (!isAdmin ()) {
		    if (SD_ALLOW_USER_PROJECTS) {
		        $prjIdList = [0];
		        foreach ($projectList as $projectInfo) $prjIdList[] = $projectInfo['id'];
		        $cond .= " and d.project_id in (".implode(',', $prjIdList).")";
		    } else {
		        $cond .= " and d.assigned_user_id=$userId";
		    }
		}
		
		$cond .= !empty( $info ['project_id'] ) ? " and d.project_id=" . intval ( $info ['project_id'] ) : "";
		$cond .= !empty( $info ['category_id'] ) ? " and d.category_id=" . intval ( $info ['category_id'] ) : "";
		$cond .= !empty( $info ['assigned_user_id'] ) ? " and d.assigned_user_id=" . intval ( $info ['assigned_user_id'] ) : "";
		$cond .= !empty( $info ['keyword'] ) ? " and (title LIKE '%" . addslashes ( $info ['keyword'] ) . "%' OR d.description LIKE '%" . addslashes ( $info ['keyword'] ) . "%')" : "";
		$cond .= !empty( $info ['status'] ) ? " and d.status='" . addslashes( $info ['status'] ) ."'" : "";
		$cond .= !empty( $info ['sort_col'] ) ? " order by " . addslashes ( $info ['sort_col'] ) : "";
		$cond .= !empty( $info ['sort_val'] ) ? " " . addslashes ( $info ['sort_val'] ) : "";
		
		$info ['user_id'] = intval ( $info ['assigned_user_id'] );
		$pgScriptPath = PLUGIN_SCRIPT_URL . "&action=diaryManager";
		$sql = "select d.*,p.name project_name, c.label category_label from sd_seo_diary d, sd_category c, sd_projects p 
			where d.project_id=p.id and d.category_id=c.id $cond ";
		
		$userCtrler = new UserController ();
		$userList = $userCtrler->__getAllUsers ();
		$this->set( 'userList', $userList );
		$userIdList = [];
		
		foreach ( $userList as $userInfo ) {
			$userIdList [$userInfo ['id']] = $userInfo;
		}
		
		$this->set ( 'userIdList', $userIdList );
		$categoryList = $this->selectDiaryCategory ();
		$this->set ( 'categoryList', $categoryList );
		$this->set( 'statusList', $this->statusList);
		
		// pagination setup
		$this->db->query ( $sql, true );
		$this->paging->setDivClass ( 'pagingdiv' );
		$this->paging->loadPaging ( $this->db->noRows, SP_PAGINGNO );
		$pagingDiv = $this->paging->printPages ( $pgScriptPath, 'searchform', 'scriptDoLoadPost', 'content', '');
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
		$userId = isLoggedIn ();
		$userCtrler = new UserController ();
		$userList = $userCtrler->__getAllUsers ();
		$this->set ( 'userList', $userList );
		
		$projectCtrler = $this->createHelper ( 'Project' );
		$projectList = $projectCtrler->__getAllProjects ( $userId, true );
		$this->set ( 'projectList', $projectList );
		
		$categoryList = $this->selectDiaryCategory ();
		$this->set ( 'categoryList', $categoryList );
		$this->set( 'statusList', $this->statusList);
		$this->set ( 'spTextReport', $this->getLanguageTexts('report', $_SESSION['lang_code']));
		$this->pluginRender ( 'new_diary' );
	}
	
	/*
	 * func to create diary
	 */
	function createDiary($listInfo) {
	    $this->set ( 'post', $listInfo );
	    $now = date('Y-m-d H:i:s');
		$errMsg ['project_id'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['project_id'] ) );
		$errMsg ['category_id'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['category_id'] ) );
		$errMsg ['title'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['title'] ) );
		$errMsg ['description'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['description'] ) );
		$errMsg ['status'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['status'] ) );
		
		if (! $this->validate->flagErr) {
		    
		    if ($this->__checkTitle ($listInfo ['title'], $listInfo ['project_id'] )) {
		        $errMsg ['title'] = formatErrorMsg ($this->pluginText['Diary already exist']);
		        $this->validate->flagErr = true;
		    }
		    
		    if (!$this->validate->flagErr) {
    		    $listInfo['created_user_id'] = isLoggedIn();
    		    $listInfo['update_time'] = $now;
    		    $listInfo['creation_time'] = $now;
    			$this->insertDiary($listInfo);
    			$this->showSDList(['keyword' => $listInfo ['title']]);
    			exit();
		    }
		}
		
		$this->set('errMsg', $errMsg );
		$this->newDiary( $listInfo );
	}	
	
	function insertDiary($listInfo) {
	    $sql = "INSERT INTO `sd_seo_diary`(`project_id`, `assigned_user_id`, `category_id`, `title`, `description`, `due_date`, `status`, 
                    `email_notification`, `creation_time`, `update_time`, `created_user_id`)
					VALUES('" . intval ( $listInfo ['project_id'] ) . "', '" . intval ( $listInfo ['assigned_user_id'] ) . "',
					'" . intval ( $listInfo ['category_id'] ) . "',  '" . addslashes ( $listInfo ['title'] ) . "',
					'" . addslashes ( $listInfo ['description'] ) . "', '" . addslashes ( $listInfo ['due_date'] ) . "',
					'" . addslashes ( $listInfo ['status'] ) . "', ".intval($listInfo['email_notification']).", 
                    '" . addslashes ( $listInfo ['creation_time'] ) . "', '" . addslashes ( $listInfo ['creation_time'] ) . "', ".intval($listInfo['created_user_id']).")";
	    $this->db->query( $sql );
	    
	    // email notification enabled, send mail
	    if (!empty($listInfo['email_notification']) && !empty($listInfo ['assigned_user_id'])) {
            $this->sendNotificationMail($listInfo);        
	    }
	    
	}
	
	function sendNotificationMail($listInfo) {
	    $userId = $listInfo ['assigned_user_id'];
	    $subject = $this->pluginText['Assigned to You'] . ": " . $listInfo['title'];
	    $userController = new UserController ();
	    $userInfo = $userController->__getUserInfo ( $userId );
	    $userName = $userInfo ['first_name'] . "-" . $userInfo ['last_name'];
	    $adminInfo = $userController->__getAdminInfo();
	    $adminName = $adminInfo['first_name']."-".$adminInfo['last_name'];
	    $this->set ( 'userName', $userName );
	    $this->set ( 'listInfo', $listInfo);
	    $content = $this->getPluginViewContent('notification_mail');
	    
	    if (sendMail( $adminInfo ['email'], $adminName, $userInfo['email'], $subject, $content )) {
	        showSuccessMsg("Notifiaction Mail send successfully to " . $userInfo ['email'], FALSE);
	    } else {
	        showErrorMsg('An internal error occured while sending mail!', FALSE);
	    }
	}
	
	/*
	 * func to edit diary
	 */
	function editDiary($diaryId, $listInfo = '') {
		
		if (!empty( $diaryId )) {
			
			if (empty($listInfo )) {
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
			$this->set( 'statusList', $this->statusList);
			$this->set ( 'spTextReport', $this->getLanguageTexts('report', $_SESSION['lang_code']));
			$this->pluginRender ( 'edit_diary' );
		}
	}
	
	/*
	 * func to update project
	 */
	function updateDiary($listInfo) {		
		$this->set ( 'post', $listInfo );
		$errMsg = [];
		$errMsg ['project_id'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['project_id'] ) );
		$errMsg ['category_id'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['category_id'] ) );
		$errMsg ['title'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['title'] ) );
		$errMsg ['description'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['description'] ) );
		$errMsg ['status'] = formatErrorMsg ( $this->validate->checkBlank ( $listInfo ['status'] ) );
		
		if (! $this->validate->flagErr) {
			
		    if ($this->__checkTitle ( $listInfo ['title'], $listInfo ['project_id'], $listInfo ['id'] )) {
		        $errMsg ['title'] = formatErrorMsg ($this->pluginText['Diary already exist']);
				$this->validate->flagErr = true;
			}
			
			if (! $this->validate->flagErr) {
			    $oldDiaryInfo = $this->__getDiaryInfo($listInfo ['id']);
				$sql = "update sd_seo_diary set project_id = " . intval ( $listInfo ['project_id'] ) . ", category_id = " . intval ( $listInfo ['category_id'] ) . 
				    ", title = '" . addslashes ( $listInfo ['title'] ) . "', description = '" . addslashes ( $listInfo ['description'] ) . 
				    "', assigned_user_id = '" . intval ( $listInfo ['assigned_user_id'] ) . "', due_date = '" . addslashes ( $listInfo ['due_date'] ) . 
				    "', update_time = '" . date ( "Y-m-d H:i:s" ) . "', status = '" . addslashes ( $listInfo ['status'] ) . "' where id=" . intval ( $listInfo ['id'] );
				$this->db->query ( $sql );
				
				// email notification enabled, send mail
				if (!empty($listInfo['email_notification']) && !empty($listInfo ['assigned_user_id'])) {
				    if ($oldDiaryInfo['assigned_user_id'] != $listInfo ['assigned_user_id']) {
				        $this->sendNotificationMail($listInfo);
				    }
				}
				
				$this->showSDList(['keyword' => $listInfo ['title']]);
				exit();
			}
			
		}
		
		$this->set ( 'errMsg', $errMsg );
		$this->editDiary( $listInfo ['id'], $listInfo );
	}
	
	/*
	 * func to delete project
	 */
	function deleteDiary($diaryId) {
		$diaryId = intval ( $diaryId );
		$sql = "delete from sd_seo_diary where id=" . intval ( $diaryId );
		$this->db->query ( $sql );
		$this->showSDList ();
	}
	
	function getUserDiaryList($userId) {
	    $cond = "";
	    $userId = intval($userId);
	    
	    if (!isAdmin()) {
	        if (SD_ALLOW_USER_PROJECTS) {
	            $projectCtrler = $this->createHelper ( 'Project' );
	            $projectList = $projectCtrler->__getAllProjects ( $userId, true );
	            $prjIdList = [0];
	            foreach ($projectList as $projectInfo) $prjIdList[] = $projectInfo['id'];
	            $cond .= " project_id in (".implode(',', $prjIdList).")";
	        } else {
	            $cond .= " assigned_user_id=$userId";
	        }
	    }
	    
	    $diaryList = $this->dbHelper->getAllRows('sd_seo_diary', $cond);
	    return $diaryList;
	}	
	
	/*
	 * func to create new comments
	 */
	function newDiaryComments($info = '') {
		$this->set ( 'post', $info );
		$userId = isLoggedIn();
		
		$diaryList = $this->getUserDiaryList($userId);
		$this->set ( 'diaryList', $diaryList );
		
		if (empty($info['diary_id'] )) {
		    $diaryId = $diaryList[0]['id'];
		} else {
			$diaryId = intval($info['diary_id']);
		}
		
		if (empty($diaryId)) {
		    showErrorMsg($_SESSION['text']['common']['No Records Found']);
		}

		$diaryInfo = $this->__getDiaryInfo($diaryId);
		$this->set ('diaryInfo', $diaryInfo );
		$this->set ('diaryId', $diaryId );
		
		$userCtrler = new UserController ();
		$userList = $userCtrler->__getAllUsers();
		$userIdList = [];
		foreach ( $userList as $userInfo ) $userIdList [$userInfo ['id']] = $userInfo;
		$this->set ( 'userIdList', $userIdList );
		
		$diaryCommentList = $this->getDiaryComments( " and diary_id=" . intval($diaryId));
		$this->set ( 'diaryCommentList', $diaryCommentList );	
		$this->pluginRender ( 'diary_comments' );
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
	        $sql = "INSERT INTO `sd_diary_comments`( `diary_id`, `user_id`, `comments`,  `updated_time`) 
                    VALUES ('" . intval ( $listInfo ['diary_id'] ) . "','" . intval ( $userId ) . "',
					'" . addslashes ( $listInfo ['comments'] ) . "','". date("Y-m-d H:i:s")."')";
	        $this->db->query ( $sql );
	        $this->newDiaryComments(['diary_id' =>  $listInfo ['diary_id']]);
	        exit ();
	    }
	    
	    $this->set ('errMsg', $errMsg );
	    $this->newDiaryComments ( $listInfo );
	}
	
	/*
	 * func to project shummary
	 */
	function showProjectSummery($info = '') {
		$this->set ( 'post', $info );
		$userId = isLoggedIn ();
		$projectCtrler = $this->createHelper ( 'Project' );
		$projectList = $projectCtrler->__getAllProjects ( $userId, true );
		$this->set ( 'projectList', $projectList );
		
		if (empty($info['project_id'] )) {
			$projectId = $projectList[0]['id'];
		} else {
			$projectId = intval($info['project_id']);
		}
		
		if (empty($projectId)) {
		    showErrorMsg($_SESSION['text']['common']['No Records Found']);
		}
		
		$projectInfo = $projectCtrler->__getProjectInfo($projectId);
		$this->set('projectInfo', $projectInfo);
		
		$diaryList = $this->__getDiaryList(" project_id = " . intval($projectId));
		foreach ( $diaryList as $i => $listInfo ) {
			$diaryList[$i]['comment_count'] = $this->getDiarytCommentCount($listInfo['id']);
		}
		
		$this->set ( 'diaryList', $diaryList );
		$this->set ( 'spTextSA', $this->getLanguageTexts('siteauditor', $_SESSION['lang_code']));		
		$this->pluginRender ( 'project_summery' );
	}
	
	/*
	 * show tasks assigned users
	 */
	function showTaskList($info = "") {
		$this->set ( 'post', $info );
		$userId = isLoggedIn ();
		$cond .= " and d.assigned_user_id=$userId";
		
		$cond .= !empty( $info ['project_id'] ) ? " and d.project_id=" . intval ( $info ['project_id'] ) : "";
		$cond .= !empty( $info ['keyword'] ) ? " and (title LIKE '%" . addslashes ( $info ['keyword'] ) . "%' OR d.description LIKE '%" . addslashes ( $info ['keyword'] ) . "%')" : "";
		$cond .= !empty( $info ['status'] ) ? " and d.status='" . addslashes( $info ['status'] ) ."'" : "";
		$cond .= !empty( $info ['sort_col'] ) ? " order by " . addslashes ( $info ['sort_col'] ) : "";
		$cond .= !empty( $info ['sort_val'] ) ? " " . addslashes ( $info ['sort_val'] ) : "";
		
		$info ['user_id'] = intval ( $info ['assigned_user_id'] );
		$pgScriptPath = PLUGIN_SCRIPT_URL . "&action=myTasks";
		$sql = "select d.*,p.name project_name, c.label category_label from sd_seo_diary d, sd_category c, sd_projects p 
			where d.project_id=p.id and d.category_id=c.id $cond ";
		
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
		$this->set ( 'statusList', $this->statusList );
		
		// pagination setup
		$this->db->query ( $sql, true );
		$this->paging->setDivClass ( 'pagingdiv' );
		$this->paging->loadPaging ( $this->db->noRows, SP_PAGINGNO );
		$pagingDiv = $this->paging->printPages ( $pgScriptPath, 'searchform', 'scriptDoLoadPost', 'content', '');
		$this->set ( 'pagingDiv', $pagingDiv );
		$sql .= " limit " . $this->paging->start . "," . $this->paging->per_page;
		
		$taskList = $this->db->select ( $sql );
		$this->set ( 'list', $taskList );
		$this->set ( 'pageNo', $_GET ['pageno'] );
		$this->pluginRender ( 'my_task' );
	}
	
	/*
	 * func to get all category type
	 */
	function getDiaryComments($condtions = '') {
		$sql = "select * from sd_diary_comments where 1=1";
		$sql .= empty( $condtions ) ? "" : $condtions;
		$diaryCommentList = $this->db->select( $sql );
		return $diaryCommentList;
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
	function getDiarytCommentCount($diaryId) {
	    $diaryCountInfo = $this->dbHelper->getRow('sd_diary_comments', "diary_id=".intval($diaryId), "count(*) count");
	    return !empty($diaryCountInfo['count']) ? $diaryCountInfo['count'] : 0;
	}
	
	/*
	 * function to check name of project already existing
	 */
	function __checkTitle($title, $projectId, $diaryId = 0) {
		$diaryId = intval ( $diaryId );
		$sql = "select id from sd_seo_diary where title='" . addslashes ( $title ) . "' and project_id=".intval($projectId);
		$sql .= !empty( $diaryId ) ? " and id!=$diaryId" : "";
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
	
	function __getDiaryList($cond = '') {
	    $diaryList = $this->dbHelper->getAllRows('sd_seo_diary', $cond);
	    return $diaryList;
	}

	/*function startCronJob() {
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
		}*/
		
}
	    