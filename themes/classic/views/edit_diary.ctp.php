<?php echo showSectionHead('Edit Diary'); ?>
<form id="projectform">
<input type="hidden" name="id" value="<?php echo $post['id']?>"/>
<table id="cust_tab">
	<tr class="form_head">
		<th width='30%'><?php echo 'Edit Diary'?></th>
		<th>&nbsp;</th>
	</tr>
	<?php if(!empty($isAdmin)){ ?>
	<tr class="form_data">
		<td><?php echo 'project'?>:</td>
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
			<?php echo $errMsg['project_id']?>
		</td>
	</tr>
	<?php }?>
	<?php if(!empty($isAdmin)){ ?>	
		<tr class="blue_row">
			<td class="td_left_col"><?php echo 'Category'?>:</td>
			<td class="td_right_col">
				<select name="category_id" style="width:150px;">
					<?php foreach($categoryList as $categoryInfo){?>
						<?php if($categoryInfo['id'] == $categorySelected){?>
							<option value="<?php echo $categoryInfo['id']?>" selected><?php echo $categoryInfo['label']?></option>
						<?php }else{?>
							<option value="<?php echo $categoryInfo['id']?>"><?php echo $categoryInfo['label']?></option>
						<?php }?>						
					<?php }?>
				</select>
			</td>
		</tr>
	<?php }?>
	<tr class="form_data">
		<td><?php echo 'Title'?>:</td>
		<td><input type="text" name="title" value="<?php echo $post['title']?>"><?php echo $errMsg['title']?></td>
	</tr>
	<tr class="form_data">
		<td><?php echo 'Description'?>:</td>
		<td>
			<textarea name="description"><?php echo $post['description']?></textarea>
			<br>
			<?php echo $errMsg['description']?>
		</td>
	</tr>
	<?php if(!empty($isAdmin)){ ?>	
		<tr class="blue_row">
			<td class="td_left_col"><?php echo 'Assignee'?>:</td>
			<td class="td_right_col">
				<select name="assigned_user_id" style="width:150px;">
					<?php foreach($userList as $userInfo){?>
						<?php if($userInfo['id'] == $userSelected){?>
							<option value="<?php echo $userInfo['id']?>" selected><?php echo $userInfo['username']?></option>
						<?php }else{?>
							<option value="<?php echo $userInfo['id']?>"><?php echo $userInfo['username']?></option>
						<?php }?>						
					<?php }?>
				</select>
			</td>
		</tr>
	<?php }?>
	<tr class="white_row">
		<td class="td_left_col"><?php echo 'Due-Date'?>:</td>
		<td class="td_right_col"><input type="date" name="due_date" value="<?php echo $post['due_date']?>"><?php echo $errMsg['due_date']?></td>
	</tr>
	<?php if(!empty($isAdmin)){ ?>	
		<tr class="blue_row">
			<td class="td_left_col"><?php echo 'Status'?>:</td>
			<td class="td_right_col">
				<select name="status" id="action<?php echo $listInfo['status']?>" >
						<option value="select">-- <?php echo $spText['common']['Select']?> --</option>
                        <option value="new"><?php echo 'New'?></option>
                        <option value="closed"><?php echo 'Closed'?></option>
                        <option value="cancelled"><?php echo 'Cancelled'?></option>
                        <option value="inprogress"><?php echo 'In Progress'?></option>
                        <option value="blocked"><?php echo 'Blocked'?></option>
                        <option value="feedback"><?php echo 'Feedback'?></option>
                        
					</select>
			</td>
		</tr>
	<?php }?>
		<tr class="white_row">
			<td class="td_left_col"><?php echo 'Email Notification:'?>:</td>
			<td class="td_right_col"><input type="checkbox" name="notification" value="<?php echo $post['notification']?>"><?php echo $errMsg['notification']?></td>
		</tr>	
</table>
<br>
<table width="100%" class="actionSec">
	<tr>
    	<td style="padding-top: 6px;text-align:right;">
    		<a onclick="<?php echo pluginGETMethod('', 'content')?>" href="javascript:void(0);" class="actionbut">
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