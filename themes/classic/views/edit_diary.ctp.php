<?php echo showSectionHead($pluginText['Edit Diary']); ?>
<form id="projectform">
<input type="hidden" name="id" value="<?php echo $post['id']?>"/>
<table id="cust_tab">
	<tr class="form_head">
		<th width='30%'><?php echo $pluginText['Edit Diary']?></th>
		<th>&nbsp;</th>
	</tr>	
	<tr class="form_data">
		<td><?php echo $spText['label']['Project']?>:</td>
		<td>
			<select name="project_id">
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
			<select name="category_id">
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
		<td><input type="text" name="title" value="<?php echo $post['title']?>"><?php echo $errMsg['title']?></td>
	</tr>
	<tr class="form_data">
		<td><?php echo $spText['label']['Description']?>:</td>
		<td><textarea name="description"><?php echo $post['description']?></textarea><br><?php echo $errMsg['description']?></td>
	</tr>
	<tr class="form_data">
		<td><?php echo $pluginText['Assignee']?>:</td>
		<td>
			<select name="assigned_user_id">
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
		<td><input type="date" name="due_date" value="<?php echo $post['due_date']?>"><?php echo $errMsg['due_date']?></td>
	</tr>
	<tr class="form_data">
		<td><?php echo $spText['common']['Status']?>:</td>
		<td>
			<select name="status">
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
		<td><input type="checkbox" name="email_notification" value="1" <?php echo !empty($post['email_notification']) ? "checked='checked'" : ""?>"></td>
	</tr>
</table>
<br>
<table width="100%" class="actionSec">
	<tr>
    	<td style="padding-top: 6px;text-align:right;">
    		<a onclick="<?php echo pluginGETMethod('action=diaryManager', 'content')?>" href="javascript:void(0);" class="actionbut">
         		<?php echo $spText['button']['Cancel']?>
         	</a>&nbsp;
         	<?php $actFun = SP_DEMO ? "alertDemoMsg()" : pluginConfirmPOSTMethod('projectform', 'content', 'action=updateDiary'); ?>
         	<a onclick="<?php echo $actFun?>" href="javascript:void(0);" class="actionbut">
         		<?php echo $spText['button']['Proceed']?>
         	</a>
    	</td>
	</tr>	
</table>
</form>