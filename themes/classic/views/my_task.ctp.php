<?php echo showSectionHead("My Tasks"); ?>
<form id="searchform">
<table class="search" width="80%">
    <tr>
		<th><?php echo $spText['label']['Project']?>:</th>
		<td>
			<select name="project_id">
				<option value="">-- <?php echo $spText['common']['Select']?> --</option>
				<?php foreach($projectList as $projectInfo){?>
					<?php if($projectInfo['id'] == $post['project_id']){?>
						<option value="<?php echo $projectInfo['id']?>" selected><?php echo $projectInfo['name']?></option>
					<?php }else{?>
						<option value="<?php echo $projectInfo['id']?>"><?php echo $projectInfo['name']?></option>
					<?php }?>						
				<?php }?>
			</select>
		</td>
		<th><?php echo $spText['common']['Keywords']?>:</th>
		<td><input type="text" class="input" name="keyword" value="<?php echo $post['keyword']?>"><?php echo $errMsg['keyword']?></td>
	</tr>
	<tr>
		<th><?php  echo $spText['common']['Status']?>:</th>
		<td>
			<select name="status">
				<option value="">-- <?php echo $spText['common']['Select']?> --</option>
				<?php foreach($statusList as $statusInfo){?>
					<?php if($statusInfo['id'] == $post['status']){?>
						<option value="<?php echo $statusInfo['id']?>" selected><?php echo $statusInfo['status']?></option>
					<?php }else{?>
						<option value="<?php echo $statusInfo['id']?>"><?php echo $statusInfo['status']?></option>
					<?php }?>						
				<?php }?>
			</select>
		</td>
		<th><?php echo $pluginText['Sorting']?>:</th>
		<td>
			<select name="sort_col">
				<option value="due_date"><?php echo $pluginText['Due Date']?></option>
				<option value="status"><?php echo $spText['common']['Status']?></option>
	        </select>
			<select name="sort_val">
				<option value="ASC"><?php echo $pluginText['Ascending']?></option>
				<option value="DESC"><?php echo $pluginText['Descending']?></option>
			</select>
         	<?php $actFun = SP_DEMO ? "alertDemoMsg()" : pluginPOSTMethod('searchform', 'content', 'action=myTasks'); ?>
         	<a onclick="<?php echo $actFun?>" href="javascript:void(0);" class="actionbut">
         		<?php echo $spText['button']['Search']?>
         	</a>
    	</td>
    </tr>
</table>
</form>
<?php echo $pagingDiv?>

<table id="cust_tab">
	<tr>
		<th><?php echo $spText['common']['Id']?></th>
		<th><?php echo $spText['label']['Title']?></th>
		<th><?php echo $spText['label']['Project']?></th>
		<th><?php echo $spText['common']['Category']?></th>
		<th><?php echo $pluginText['Due Date']?></th>
		<th><?php echo $spText['common']['Status']?></th>
		<th><?php echo $spText['common']['Action']?></th>
	</tr>
	<?php
	if(count($list) > 0) {
		foreach($list as $i => $listInfo){
			$diaryLink = scriptAJAXLinkHref(PLUGIN_SCRIPT_URL, 'content', "action=editDiary&project_id={$listInfo['id']}", "{$listInfo['title']}");
			?>
			<tr>
				<td width="40px"><?php echo $listInfo['id']?></td>
				<td><?php echo $diaryLink?></td>				
				<td><?php echo $listInfo['project_name']?></td>
				<td><?php echo $listInfo['category_label']?></td>
                <td><?php echo $listInfo['due_date']?></td>
				<td><?php echo $listInfo['status']?></td>
				<td width="100px">
					<select name="action" id="action<?php echo $listInfo['id']?>" onchange="doPluginAction('<?php echo PLUGIN_SCRIPT_URL?>', 'content', 'project_id=<?php echo $listInfo['id']?>&pageno=<?php echo $pageNo?>', 'action<?php echo $listInfo['id']?>')">
						<option value="<?php echo $statAction?>"><?php echo $statLabel?>Select Action</option>
						<option value="newComment">Comments</option>
						<option value="editDiary"><?php echo $spText['common']['Edit']?></option>
						<option value="deleteDiary"><?php echo $spText['common']['Delete']?></option>
					</select>
				</td>
			</tr>
			<?php
		}
	}else{
		?>
		<tr><td colspan="8"><b><?php echo $_SESSION['text']['common']['No Records Found']?></b></tr>
		<?php
	} 
	?>
</table>