<?php echo showSectionHead($spTextSA['Project Summary']); ?>
<form id="projectform">	
	<div id="live-chat">
		<header class="clearfix"><?php echo $spText['label']['Project']?>:</td>
			<select onchange="doDiaryAction('<?php echo PLUGIN_SCRIPT_URL?>', 'content', 'action=projectSummery', 'project_id','project_id')" name="project_id" id="project_id">
					<?php foreach($projectList as $prjInfo){?>
						<?php if($prjInfo['id'] == $post['project_id']){?>
							<option value="<?php echo $prjInfo['id']?>" selected><?php echo $prjInfo['name']?></option>
						<?php }else{?>
							<option value="<?php echo $prjInfo['id']?>"><?php echo $prjInfo['name']?></option>
						<?php }?>						
					<?php }?>
			</select>
		</header>
	</div>
    <div class="chat">      
        <div class="chat-history">
        
            <div class="chat-message clearfix">        
    	        <div class="chat-message-content clearfix">	
    			 	<p class="chat-desc"><?php echo $projectInfo['description']?></p>
    			</div>
    		</div>
    		
    		<?php if(count($diaryList) > 0) {?>
        		<div class="chat-message clearfix">        
        	        <div class="chat-message-content clearfix">  
        			 	<?php
        				foreach($diaryList as $listInfo){
        					?>
        					<div class="chat-container" id="diary_id" >
    							<h5><?php echo $listInfo['title']?></h5>
        					  	<p class='chat-desc-small'><?php echo $listInfo['description']?></p>
        					  	<span class="chat-name">
            					  	<a onclick="<?php echo pluginGETMethod('action=newComment&diary_id='.$listInfo['id'], 'content')?>" href="javascript:void(0);">
        								<span class="time-right"><br><?php echo $listInfo['comment_count']?> <?php echo 'comments'?></span>
            					  	</a>
            					</span>
        					</div>
        					<?php
        				}
        				?>
        			</div>
        		</div>
    		<?php }?>
    	</div>
    </div>	
</form>