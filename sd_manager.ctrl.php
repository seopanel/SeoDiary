<?php
/**
 * Copyright(C) 2009-2019 www.seopanel.org. All rights reserved.
 * @author Geo Varghese 
 * 
 */
class SD_Manager extends SeoDiary {
	
	var $cronJob = false;
	var $statusList;

	function __construct() {
		parent::__construct();
		$this->setPluginTextsForRender ( $this->textCategory, $this->textTable );
		$this->statusList = array(
			'new' => $this->pluginText['New'],
			'closed' => $this->pluginText['Closed'],
			'cancelled' => $this->pluginText['Cancelled'],
			'inprogress' => $this->pluginText['Inprogress'],
			'blocked' => $this->pluginText['Blocked'],
			'feedback' => $this->pluginText['Feedback'],
		);
	}
	
	function showSDList($info=[]) {
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
		        $cond .= " and (d.assigned_user_id=$userId or d.created_user_id=$userId)";
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
	function newDiary($info = []) {
		$userId = isLoggedIn ();
		// also restores the user's own entries on a validation-failure
		// retry (createDiary() re-calls this with $listInfo), and lets an
		// external deep link (e.g. "Add to SEO Diary" from the AI
		// Visibility recommendations dashboard) prefill title/description
		$this->set ( 'post', $info );
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

		include_once(SP_CTRLPATH . '/settings.ctrl.php');
		$this->set('localAiAvailable', SettingsController::isLocalAIEnabled());

		$this->pluginRender ( 'new_diary' );
	}

	/*
	 * AJAX action: Local AI draft of a diary task's description from just
	 * its title - unlike generateProjectAISummary() below, this generates
	 * NEW content (same "the user reviews before using it" discipline as
	 * LocalAIController::suggestMetaTags()), not a restate-only-the-facts
	 * summary. Never auto-fired.
	 */
	function suggestDiaryDescription($info) {
		include_once(SP_CTRLPATH . '/settings.ctrl.php');
		if (!SettingsController::isLocalAIEnabled()) {
			return ['ok' => false, 'description' => '', 'error' => 'Local AI is not enabled'];
		}
		if (empty($info['title'])) {
			return ['ok' => false, 'description' => '', 'error' => 'Please enter a title first'];
		}

		$categoryLabel = '';
		if (!empty($info['category_id'])) {
			$catRow = $this->dbHelper->getRow('sd_category', 'id=' . intval($info['category_id']));
			$categoryLabel = !empty($catRow['label']) ? $catRow['label'] : '';
		}

		$systemPrompt = 'You draft concise, actionable task descriptions for an SEO project task tracker. '
			. 'Respond with ONLY the description text (a short paragraph or a few concrete steps), no preamble, no quotes.';
		$prompt = 'Task title: ' . $info['title'] . "\n" . (!empty($categoryLabel) ? "Category: $categoryLabel\n" : '') . "\nDraft a description for this task.";

		include_once(SP_CTRLPATH . '/localai.ctrl.php');
		$userId = isLoggedIn();
		$result = (new LocalAIController())->__callOllama($prompt, $systemPrompt, 20, $userId);
		return ['ok' => $result['ok'], 'description' => $result['text'], 'error' => $result['error']];
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
	function newDiaryComments($info = []) {
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
	function showProjectSummery($info = []) {
		$this->set ( 'post', $info );
		$userId = isLoggedIn ();
		$projectCtrler = $this->createHelper ( 'Project' );
		$projectList = $projectCtrler->__getAllProjects ( $userId, true );
		$this->set ( 'projectList', $projectList );

		if (empty($info['project_id'] )) {
			$projectId = $projectList[0]['id'];
		} else {
			$projectId = intval($info['project_id']);
			// ownership check (IDOR fix): __getProjectInfo()/__getDiaryList()
			// below have no ownership filtering of their own at all - a
			// caller-supplied project_id was previously used as-is, letting
			// a non-admin view (and, via getDiarytCommentCount(), the
			// comment activity of) ANY project regardless of which
			// website/user it actually belongs to. $projectList above is
			// already correctly scoped to this user's own projects
			// (__getAllProjects()'s own $isAdminCheck logic) - just needed
			// to actually cross-check project_id against it.
			if (!isAdmin()) {
				$ownsProject = false;
				foreach ($projectList as $p) { if ($p['id'] == $projectId) { $ownsProject = true; break; } }
				if (!$ownsProject) $projectId = 0;
			}
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

		include_once(SP_CTRLPATH . '/settings.ctrl.php');
		$this->set('localAiAvailable', SettingsController::isLocalAIEnabled());

		$this->pluginRender ( 'project_summery' );
	}

	/*
	 * AJAX action: Local AI plain-language status summary of a project's
	 * current diary entries (counts/overdue items) - restates ONLY the
	 * given facts, same discipline as RecommendationsController::
	 * generateInsightsSummary(). Ownership is enforced the same way
	 * showProjectSummery() above now is - a non-admin can only summarize
	 * their own project.
	 */
	function generateProjectAISummary($info) {
		include_once(SP_CTRLPATH . '/settings.ctrl.php');
		if (!SettingsController::isLocalAIEnabled()) {
			return ['ok' => false, 'summary' => '', 'error' => 'Local AI is not enabled'];
		}

		$userId = isLoggedIn();
		$projectId = intval($info['project_id']);
		$projectCtrler = $this->createHelper('Project');
		$projectList = $projectCtrler->__getAllProjects($userId, true);
		if (!isAdmin()) {
			$ownsProject = false;
			foreach ($projectList as $p) { if ($p['id'] == $projectId) { $ownsProject = true; break; } }
			if (!$ownsProject) {
				return ['ok' => false, 'summary' => '', 'error' => 'Not authorized'];
			}
		}

		$projectInfo = $projectCtrler->__getProjectInfo($projectId);
		if (empty($projectInfo)) {
			return ['ok' => false, 'summary' => '', 'error' => 'Project not found'];
		}

		$diaryList = $this->__getDiaryList(" project_id = $projectId");
		if (empty($diaryList)) {
			return ['ok' => true, 'summary' => 'No diary entries for this project yet.', 'error' => null];
		}

		$today = date('Y-m-d');
		$lines = [];
		foreach ($diaryList as $d) {
			$overdue = ($d['due_date'] < $today && !in_array($d['status'], ['closed', 'cancelled'])) ? ' (OVERDUE)' : '';
			$lines[] = '- [' . $d['status'] . ']' . $overdue . ' ' . $d['title'] . ' (due ' . $d['due_date'] . ')';
		}
		$listText = implode("\n", $lines);

		$systemPrompt = 'You summarize a project\'s task list for a project manager in plain language. '
			. 'You must ONLY restate and group the tasks given to you - never invent, assume, or add any '
			. 'task, statistic, or recommendation not explicitly present in the list. Keep it to one short paragraph.';
		$prompt = 'Project: ' . $projectInfo['name'] . "\n\nTasks:\n$listText\n\nWrite a one-paragraph plain-language status summary of exactly these tasks.";

		include_once(SP_CTRLPATH . '/localai.ctrl.php');
		$result = (new LocalAIController())->__callOllama($prompt, $systemPrompt, 25, $userId);
		return ['ok' => $result['ok'], 'summary' => $result['text'], 'error' => $result['error']];
	}
	
	/*
	 * show tasks assigned users
	 */
	function showTaskList($info = []) {
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

	/*
	 * func to send due-date reminder emails for open (not closed/
	 * cancelled) diary entries that are overdue, due today, or due
	 * tomorrow. Sends at most once per calendar day per diary entry
	 * (tracked via last_reminder_date), so this is safe to invoke
	 * repeatedly from a frequent system cron (see diarycron.php) without
	 * spamming the assignee. Entries with no assignee are skipped - there
	 * is nobody to remind.
	 */
	function startCronJob() {
		$this->cronJob = true;

		if (!defined('SD_ENABLE_DUE_REMINDERS') || !SD_ENABLE_DUE_REMINDERS) {
			echo "Due-date reminders are disabled (SD_ENABLE_DUE_REMINDERS).";
			return;
		}

		$today = date('Y-m-d');
		$tomorrow = date('Y-m-d', strtotime('+1 day'));

		$sql = "SELECT * FROM sd_seo_diary
				WHERE status NOT IN ('closed','cancelled')
				AND assigned_user_id > 0
				AND due_date <= '" . addslashes($tomorrow) . "'
				AND (last_reminder_date IS NULL OR last_reminder_date != '" . addslashes($today) . "')";
		$diaryList = $this->db->select($sql);

		if (empty($diaryList)) {
			echo "No due-date reminders to send.";
			return;
		}

		$sentCount = 0;
		foreach ($diaryList as $diaryInfo) {
			if ($this->sendDueDateReminderMail($diaryInfo, $today)) {
				$sentCount++;
			}
			$this->db->query("UPDATE sd_seo_diary SET last_reminder_date='" . addslashes($today) . "' WHERE id=" . intval($diaryInfo['id']));
		}

		echo "$sentCount of " . count($diaryList) . " due-date reminder(s) sent.";
	}

	/*
	 * func to send a single due-date reminder mail, worded as overdue/
	 * due-today/due-tomorrow depending on the diary entry's due_date
	 * relative to $today. Returns sendMail()'s result (falsy on failure -
	 * e.g. the assignee has no email, or sendMail() itself fails), same
	 * shape as sendNotificationMail().
	 */
	function sendDueDateReminderMail($diaryInfo, $today) {
		if ($diaryInfo['due_date'] < $today) {
			$reminderLabel = $this->pluginText['Task Overdue'];
		} else if ($diaryInfo['due_date'] == $today) {
			$reminderLabel = $this->pluginText['Task Due Today'];
		} else {
			$reminderLabel = $this->pluginText['Task Due Tomorrow'];
		}

		$userController = new UserController();
		$userInfo = $userController->__getUserInfo($diaryInfo['assigned_user_id']);

		if (empty($userInfo['email'])) {
			return false;
		}

		$subject = $reminderLabel . ": " . $diaryInfo['title'];
		$userName = $userInfo['first_name'] . " " . $userInfo['last_name'];
		$adminInfo = $userController->__getAdminInfo();
		$adminName = $adminInfo['first_name'] . " " . $adminInfo['last_name'];
		$projectInfo = $this->__getDiaryInfo($diaryInfo['id']);

		$this->set('userName', $userName);
		$this->set('listInfo', $diaryInfo);
		$this->set('reminderLabel', $reminderLabel);
		$this->set('projectName', $projectInfo['project_name']);
		$content = $this->getPluginViewContent('diary_reminder_mail');

		return sendMail($adminInfo['email'], $adminName, $userInfo['email'], $subject, $content);
	}

}
	    