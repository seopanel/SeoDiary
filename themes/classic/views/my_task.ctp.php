<?php echo showSectionHead($pluginText["My Tasks"]); ?>
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
				<?php foreach($statusList as $statVal => $statLabel){?>
					<?php if($statVal == $post['status']){?>
						<option value="<?php echo $statVal?>" selected><?php echo $statLabel?></option>
					<?php }else{?>
						<option value="<?php echo $statVal?>"><?php echo $statLabel?></option>
					<?php }?>						
				<?php }?>
			</select>
		</td>
		<th><?php echo $pluginText['Sorting']?>:</th>
		<td>
			<?php
			$statusSel = ($post['sort_col'] == 'status') ? "selected" : "";
			$descSel = ($post['sort_val'] == 'DESC') ? "selected" : "";
			?>
			<select name="sort_col">
				<option value="due_date"><?php echo $pluginText['Due Date']?></option>
				<option value="status" <?php echo $statusSel?>><?php echo $spText['common']['Status']?></option>
	        </select>
			<select name="sort_val">
				<option value="ASC"><?php echo $pluginText['Ascending']?></option>
				<option value="DESC" <?php echo $descSel?>><?php echo $pluginText['Descending']?></option>
			</select>
         	<?php $actFun = pluginPOSTMethod('searchform', 'content', 'action=myTasks'); ?>
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
				<td><?php echo $statusList[$listInfo['status']]?></td>
				<td width="100px">
					<select name="action" id="action<?php echo $listInfo['id']?>" onchange="doPluginAction('<?php echo PLUGIN_SCRIPT_URL?>', 'content', 'project_id=<?php echo $listInfo['id']?>&pageno=<?php echo $pageNo?>', 'action<?php echo $listInfo['id']?>')">
						<option value="">-- <?php echo $spText['common']['Select']?> --</option>
						<option value="newComment"><?php echo $spText['label']['Comments']?></option>
						<option value="editDiary"><?php echo $spText['common']['Edit']?></option>
						<option value="deleteDiary"><?php echo $spText['common']['Delete']?></option>
					</select>
				</td>
			</tr>
			<?php
		}
	}else{
		?>
		<tr><td colspan="7"><b><?php echo $_SESSION['text']['common']['No Records Found']?></b></tr>
		<?php
	} 
	?>
</table>