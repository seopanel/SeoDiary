<?php echo showSectionHead($pluginText["Diary Comments"]); ?>
<form id="projectform">
	<div id="live-chat">
		<header class="clearfix"><?php echo $spText['common']['Name']?>:
			<select onchange="doDiaryAction('<?php echo PLUGIN_SCRIPT_URL?>', 'content', 'action=newComment', 'diary_id','diary_id')" name="diary_id" id="diary_id">
					<?php foreach($diaryList as $drInfo){?>
						<?php if($drInfo['id'] == $diaryId){?>
							<option value="<?php echo $drInfo['id']?>" selected><?php echo $drInfo['title']?></option>
						<?php }else{?>
							<option value="<?php echo $drInfo['id']?>"><?php echo $drInfo['title']?></option>
						<?php }?>						
					<?php }?>
			</select>
		</header>
	</div>
	<div class="chat">      
    <div class="chat-history"> 
           
        <div class="chat-message clearfix">        
	        <div class="chat-message-content clearfix">
				<pre class="chat-desc"><?php echo $diaryInfo['description']?></pre>
	         </div>
        </div>

		<?php if(count($diaryCommentList) > 0) {?>
        	<div class="chat-message clearfix">        
    	        <div class="chat-message-content clearfix"> 
    		        <?php
    				foreach($diaryCommentList as $i => $listInfo){
						?> 
						<div class="chat-container"> 
				              <p class="chat-desc-small"><?php echo $listInfo['comments']?></p>
				              <div style="clear: both;"></div>
				              <h5 class="chat-name"><?php echo $userIdList[$listInfo['user_id']]['username']?></h5>
				              <div style="clear: both;"></div>
				           	  <span class="chat-time"><?php echo $listInfo['updated_time']?></span>
				        </div>
						<?php
    				}
    				?>
    	        </div>
        	</div>
    	<?php }?>
		
		<textarea name="comments" class="ui-autocomplete-input" aria-autocomplete="list" aria-haspopup="true" placeholder="<?php echo $pluginText['Add your comment here']?>..."><?php echo $post['comments']?></textarea>
	    <?php echo $errMsg['comments']?>
	    
	    <table width="100%" class="actionSec">
        	<tr>
            	<td style="padding-top: 6px;text-align:right;">
                 	<?php $actFun1 = SP_DEMO ? "alertDemoMsg()" : pluginPOSTMethod('projectform', 'content', 'action=newComment'); ?>
            		<a onclick="<?php echo $actFun1?>" href="javascript:void(0);" class="actionbut">
                 		<?php echo $spText['button']['Cancel']?>
                 	</a>&nbsp;
        
                 	<?php $actFun = SP_DEMO ? "alertDemoMsg()" : pluginPOSTMethod('projectform', 'content', 'action=createComment'); ?>
                 	<a onclick="<?php echo $actFun?>" href="javascript:void(0);" class="actionbut">
                 		<?php echo $pluginText['Add Comment']?>
                 	</a>
            	</td>
        	</tr>
        </table>
    </div>
</div>
</form>