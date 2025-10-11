<?php echo showSectionHead($pluginText["Projects Manager"]); ?>
<?php if(!empty($isAdmin)){ ?>
	<table class="search">
		<tr>
			<th><?php echo $spText['common']['User']?>: </th>
			<td>
				<select name="user_id" id="user_id" onchange="doLoad('user_id', '<?php echo PLUGIN_SCRIPT_URL?>', 'content')" class="custom-select">
					<option value="">-- <?php echo $spText['common']['Select']?> --</option>
					<?php foreach($userList as $userInfo){?>
						<?php if($userInfo['id'] == $userId){?>
							<option value="<?php echo $userInfo['id']?>" selected><?php echo $userInfo['username']?></option>
						<?php }else{?>
							<option value="<?php echo $userInfo['id']?>"><?php echo $userInfo['username']?></option>
						<?php }?>
					<?php }?>
				</select>
			</td>
			<td>
				<a onclick="doLoad('user_id', '<?php echo PLUGIN_SCRIPT_URL?>', 'content')" href="javascript:void(0);" class="btn btn-secondary">
					<?php echo $spText['button']['Show Records']?>
				</a>
			</td>
		</tr>
	</table>
<?php } ?>

<?php echo $pagingDiv?>

<table id="cust_tab">
	<tr>
		<th><?php echo $spText['common']['Name']?></th>
    	<th><?php echo $spText['common']['Website']?></th>
		<th><?php echo $spText['common']['Status']?></th>
		<th style="width: 15%"><?php echo $spText['common']['Action']?></th>
	</tr>
	<?php
	if(count($list) > 0) {
		foreach($list as $i => $listInfo){
			$projectLink = scriptAJAXLinkHref(PLUGIN_SCRIPT_URL, 'content', "action=editProject&project_id={$listInfo['id']}", "{$listInfo['name']}");
			?>
			<tr>
				<td><?php echo $projectLink?></td>
				<td><?php echo $listInfo['website_name']?></td>
				<td class="text-center"><?php echo showStatusBadge($listInfo['status']); ?></td>
				<td>
					<?php
						if($listInfo['status']){
							$statAction = "Inactivate";
							$statLabel = $spText['common']["Inactivate"];
						}else{
							$statAction = "Activate";
							$statLabel = $spText['common']["Activate"];
						}
					?>
					<select name="action" id="action<?php echo $listInfo['id']?>" class="custom-select" style="width: 180px;"
						onchange="doSDPluginAction('<?php echo PLUGIN_SCRIPT_URL?>', 'content', 'project_id=<?php echo $listInfo['id']?>&pageno=<?php echo $pageNo?>', 'action<?php echo $listInfo['id']?>')">
						<option value="select">-- <?php echo $spText['common']['Select']?> --</option>
						<option value="<?php echo $statAction?>"><?php echo $statLabel?></option>
                        <option value="editProject"><?php echo $spText['common']['Edit']?></option>
                        <option value="projectSummery"><?php echo $spTextSA['Project Summary']?></option>
                        <option value="diaryManager"><?php echo $pluginText['Diary Manager']?></option>
					    <option value="deleteProject"><?php echo $spText['common']['Delete']?></option>
					</select>
				</td>
			</tr>
			<?php
		}
	}else{
		?>
		<tr><td colspan="4"><b><?php echo $_SESSION['text']['common']['No Records Found']?></b></tr>
		<?php
	}
	?>
</table>
<table class="actionSec mt-2">
	<tr>
    	<td>
         	<a onclick="<?php echo pluginGETMethod('action=newProject&user_id='.$userId, 'content')?>" href="javascript:void(0);" class="btn btn-primary">
         		<?php echo $spTextPanel['New Project']?>
         	</a>
    	</td>
	</tr>
</table>