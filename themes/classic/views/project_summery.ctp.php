<?php echo showSectionHead($spTextSA['Project Summary']); ?>
<form id="projectform">	
	<div id="live-chat">
		<header class="clearfix"><?php echo $spText['label']['Project']?>:</td>
			<select onchange="doDiaryAction('<?php echo PLUGIN_SCRIPT_URL?>', 'content', 'action=projectSummery', 'project_id','project_id')" name="project_id" style="width:150px;" id="project_id">
					<?php foreach($projectList as $projectInfo){?>
						<?php if($projectInfo['id'] == $post['project_id']){?>
							<option value="<?php echo $projectInfo['id']?>" selected><?php echo $projectInfo['name']?></option>
						<?php }else{?>
							<option value="<?php echo $projectInfo['id']?>"><?php echo $projectInfo['name']?></option>
						<?php }?>						
					<?php }?>
			</select>
		</header>
	</div>
<div class="chat">      
    <div class="chat-history">        
        <div class="chat-message clearfix">        
	        <div class="chat-message-content clearfix">	        	
			 	<?php
				if(count($selectProjectDesc) > 0) {
					foreach($selectProjectDesc as $i => $listInfo){
						?>
					  <h5><?php echo $listInfo['description']?></h5>
					
					<?php
					}
				}
				?>
			</div>
		</div>
		
		<div class="chat-message clearfix">        
	        <div class="chat-message-content clearfix">  
			 	<?php
				if(count($diaryNameList) > 0) {
					foreach($diaryNameList as $i => $listInfo){
						?>
					<div class="container " name="diary_id" id="diary_id" >
					  <h5><?php echo $listInfo['title']?></h5>
					  <p><?php echo $listInfo['description']?></p>
					  <a onclick="<?php echo pluginGETMethod('action=newComment&user_id='.$project_id, 'content')?>" href="javascript:void(0);">
					  	<span class="time-right"><br><?php echo $listInfo['comment_count']?> <?php echo 'comments'?></span>
					  </a>
					 </div>
					
					<?php
					}
				}
				?>
			</div>
		</div>	
	</div>
</div>	
</form>