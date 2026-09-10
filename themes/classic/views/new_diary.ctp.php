<?php echo showSectionHead($pluginText['New Diary']); ?>
<form id="projectform">
<table id="cust_tab">
	<tr class="form_head">
		<th width='30%'><?php echo $pluginText['New Diary']?></th>
		<th>&nbsp;</th>
	</tr>	
	<tr class="form_data">
		<td><?php echo $spText['label']['Project']?>:</td>
		<td>
			<select name="project_id" class="custom-select">
				<?php foreach($projectList as $projectInfo){?>
					<?php if($projectInfo['id'] == $post['project_id']){?>
						<option value="<?php echo $projectInfo['id']?>" selected><?php echo $projectInfo['name']?></option>
					<?php }else{?>
						<option value="<?php echo $projectInfo['id']?>"><?php echo $projectInfo['name']?></option>
					<?php }?>
				<?php }?>
			</select>
		</td>
	</tr>
	<tr class="form_data">
		<td><?php echo $spText['common']['Category']?>:</td>
		<td>
			<select name="category_id" class="custom-select">
				<?php foreach($categoryList as $categoryInfo){?>
					<?php if($categoryInfo['id'] == $post['category_id']){?>
						<option value="<?php echo $categoryInfo['id']?>" selected><?php echo $categoryInfo['label']?></option>
					<?php }else{?>
						<option value="<?php echo $categoryInfo['id']?>"><?php echo $categoryInfo['label']?></option>
					<?php }?>
				<?php }?>
			</select>
		</td>
	</tr>
	<tr class="form_data">
		<td><?php echo $spText['label']['Title']?>:</td>
		<td><input type="text" id="sdTitleInput" name="title" value="<?php echo htmlspecialchars($post['title'] ?? '')?>" class="form-control"><?php echo $errMsg['title']?></td>
	</tr>
	<?php if (!empty($localAiAvailable)) { ?>
	<tr class="form_data">
		<td></td>
		<td>
			<button type="button" id="sdAiDraftBtn" class="btn btn-sm btn-outline-primary" onclick="sdSuggestDiaryDescription()">
				<i class="fas fa-magic"></i> Draft Description with AI
			</button>
			<p>Drafts a description below from the Title (and Category, if selected) using your on-premise Local AI (Ollama) - review before saving.</p>
		</td>
	</tr>
	<script type="text/javascript">
	function sdSuggestDiaryDescription() {
		var title = document.getElementById('sdTitleInput').value;
		if (!title) {
			alert('Please enter a title first.');
			return;
		}
		var categorySelect = document.querySelector('#projectform select[name="category_id"]');
		var btn = document.getElementById('sdAiDraftBtn');
		var originalHtml = btn.innerHTML;
		btn.disabled = true;
		btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Drafting...';
		$.ajax({
			url: '<?php echo PLUGIN_SCRIPT_URL; ?>&action=suggestDiaryDescription',
			type: 'GET',
			data: { title: title, category_id: categorySelect ? categorySelect.value : '' },
			dataType: 'json',
			success: function(response) {
				if (response.ok) {
					document.querySelector('#projectform textarea[name="description"]').value = response.description;
				} else {
					alert(response.error || 'Could not generate a description.');
				}
			},
			error: function() {
				alert('Could not generate a description.');
			},
			complete: function() {
				btn.disabled = false;
				btn.innerHTML = originalHtml;
			}
		});
	}
	</script>
	<?php } ?>
	<tr class="form_data">
		<td><?php echo $spText['label']['Description']?>:</td>
		<td><textarea name="description" class="form-control"><?php echo htmlspecialchars($post['description'] ?? '')?></textarea><br><?php echo $errMsg['description']?></td>
	</tr>
	<tr class="form_data">
		<td><?php echo $pluginText['Assignee']?>:</td>
		<td>
			<select name="assigned_user_id" class="custom-select">
				<option value="">-- <?php echo $spText['common']['Select']?> --</option>
				<?php foreach($userList as $userInfo){?>
					<?php if($userInfo['id'] == $post['assigned_user_id']){?>
						<option value="<?php echo $userInfo['id']?>" selected><?php echo $userInfo['username']?></option>
					<?php }else{?>
						<option value="<?php echo $userInfo['id']?>"><?php echo $userInfo['username']?></option>
					<?php }?>
				<?php }?>
			</select>
		</td>
	</tr>
	<tr class="form_data">
		<td><?php echo $pluginText['Due Date']?>:</td>
		<td>
			<?php $dueDate = !empty($post['due_date']) ? $post['due_date'] : date('Y-m-d', strtotime('+5 days')); ?>
			<input type="text" name="due_date" value="<?php echo $dueDate ?>" class="form-control"><?php echo $errMsg['due_date']?>
    		<script type="text/javascript">
    		$(function() {
    			$( "input[name='due_date']").datepicker({dateFormat: "yy-mm-dd"});
    		});
    		</script>
		</td>
	</tr>
	<tr class="form_data">
		<td><?php echo $spText['common']['Status']?>:</td>
		<td>
			<select name="status" class="custom-select">
				<?php foreach($statusList as $statVal => $statLabel){?>
					<?php if($statVal == $post['status']){?>
						<option value="<?php echo $statVal?>" selected><?php echo $statLabel?></option>
					<?php }else{?>
						<option value="<?php echo $statVal?>"><?php echo $statLabel?></option>
					<?php }?>
				<?php }?>
			</select>
		</td>
	</tr>
	<tr class="form_data">
		<td><?php echo $spTextReport['Email notification']?>:</td>
		<td><input type="checkbox" name="email_notification" value="1" <?php echo !empty($post['email_notification']) ? "checked='checked'" : ""?> ></td>
	</tr>
</table>
<table class="actionSec float-right mt-2">
	<tr>
    	<td>
    		<a onclick="<?php echo pluginGETMethod('action=diaryManager', 'content')?>" href="javascript:void(0);" class="btn btn-warning">
         		<?php echo $spText['button']['Cancel']?>
         	</a>&nbsp;
         	<?php $actFun = SP_DEMO ? "alertDemoMsg()" : pluginPOSTMethod('projectform', 'content', 'action=createDiary'); ?>
         	<a onclick="<?php echo $actFun?>" href="javascript:void(0);" class="btn btn-primary">
         		<?php echo $spText['button']['Proceed']?>
         	</a>
    	</td>
	</tr>
</table>
</form>