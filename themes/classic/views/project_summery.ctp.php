<?php echo showSectionHead($spTextSA['Project Summary']); ?>
<form id="projectform">	
	<div id="live-chat">
		<header class="clearfix d-flex align-items-center">
			<span style="margin-right: 10px;"><?php echo $spText['label']['Project']?>:</span>
			<select onchange="doDiaryAction('<?php echo PLUGIN_SCRIPT_URL?>', 'content', 'action=projectSummery', 'project_id','project_id')" name="project_id" id="project_id" class="custom-select" style="flex: 0 0 auto; width: auto;">
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
    			 	<p class="chat-desc"><?php echo nl2br(htmlspecialchars($projectInfo['description']))?></p>
    			</div>
    		</div>

    		<?php if (!empty($localAiAvailable)) { ?>
    		<div class="chat-message clearfix">
    			<div class="chat-message-content clearfix">
    				<button type="button" id="sdAiSummaryBtn" class="btn btn-sm btn-outline-primary" onclick="sdGenerateProjectAISummary(<?php echo intval($projectInfo['id'])?>)">
    					<i class="fas fa-magic"></i> AI Summary
    				</button>
    				<div id="sdAiSummaryRow" style="display:none; margin-top:10px;">
    					<div id="sdAiSummaryText" class="alert alert-info" style="margin-bottom:0;"></div>
    				</div>
    			</div>
    		</div>
    		<script type="text/javascript">
    		function sdGenerateProjectAISummary(projectId) {
    			var btn = document.getElementById('sdAiSummaryBtn');
    			var originalHtml = btn.innerHTML;
    			btn.disabled = true;
    			btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Summarizing...';
    			$.ajax({
    				url: '<?php echo PLUGIN_SCRIPT_URL; ?>&action=generateProjectAISummary',
    				type: 'GET',
    				data: { project_id: projectId },
    				dataType: 'json',
    				success: function(response) {
    					document.getElementById('sdAiSummaryRow').style.display = '';
    					if (response.ok) {
    						document.getElementById('sdAiSummaryText').className = 'alert alert-info';
    						document.getElementById('sdAiSummaryText').innerText = response.summary;
    					} else {
    						document.getElementById('sdAiSummaryText').className = 'alert alert-danger';
    						document.getElementById('sdAiSummaryText').innerText = response.error || 'Could not generate a summary.';
    					}
    				},
    				error: function() {
    					document.getElementById('sdAiSummaryRow').style.display = '';
    					document.getElementById('sdAiSummaryText').className = 'alert alert-danger';
    					document.getElementById('sdAiSummaryText').innerText = 'Could not generate a summary.';
    				},
    				complete: function() {
    					btn.disabled = false;
    					btn.innerHTML = originalHtml;
    				}
    			});
    		}
    		</script>
    		<?php } ?>

    		<?php if(count($diaryList) > 0) {?>
        		<div class="chat-message clearfix">
        	        <div class="chat-message-content clearfix">
        			 	<?php
        				foreach($diaryList as $listInfo){
        					?>
        					<div class="chat-container" id="diary_id" >
    							<h5><?php echo htmlspecialchars($listInfo['title'])?></h5>
        					  	<p class='chat-desc-small'><?php echo nl2br(htmlspecialchars($listInfo['description']))?></p>
        					  	<span class="chat-name">
            					  	<a onclick="<?php echo pluginGETMethod('action=newComment&diary_id='.$listInfo['id'], 'content')?>" href="javascript:void(0);">
        								<span class="time-right" style="font-size: 16px;"><br><?php echo $listInfo['comment_count']?> <?php echo 'comments'?></span>
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