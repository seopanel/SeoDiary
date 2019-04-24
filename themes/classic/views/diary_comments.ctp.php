<?php echo showSectionHead($pluginText["Diary Comments"]); ?>
<form id="projectform">
    <?php if(!empty($isAdmin)){ ?>	
	<div id="live-chat">
		<header class="clearfix"><?php echo $pluginText['Diary Name']?>
			<select onchange="doDiaryAction('<?php echo PLUGIN_SCRIPT_URL?>', 'content', 'action=newComment', 'diary_id','diary_id')" name="diary_id" style="width:150px;" id="diary_id">

					<?php foreach($diaryNameList as $diaryInfo){?>
						<?php if($diaryInfo['id'] == $post['diary_id']){?>
							<option value="<?php echo $diaryInfo['id']?>" selected><?php echo $diaryInfo['title']?></option>
						<?php }else{?>
							<option value="<?php echo $diaryInfo['id']?>"><?php echo $diaryInfo['title']?></option>
						<?php }?>						
					<?php }?>
			</select>
		</header>
	</div>
<?php }?>

<div class="chat">      
    <div class="chat-history">        
        <div class="chat-message clearfix">        
	        <div class="chat-message-content clearfix">  

	        <?php
			if(count($selectDiaryDesc) > 0) {
				foreach($selectDiaryDesc as $i => $listInfo){
					?>
					<h4><?php echo $listInfo['description']?></h4>
					<?php }
			}else{ ?>
				<?php echo $_SESSION['text']['common']['No Records Found']?></b>
			<?php } ?>

	         </div> <!-- end chat-message-content -->
        </div> <!-- end chat-message -->

    	<div class="chat-message clearfix">        
	        <div class="chat-message-content clearfix"> 
			        <?php
					if(count($diaryCommentList) > 0) {
						foreach($diaryCommentList as $i => $listInfo){
							?> 
						<div class="container "> 
				           	  <span class="chat-time"><?php echo $listInfo['time']?></span>
				              <h5><?php echo $userIdList[$listInfo['user_id']]['username']?></h5>
				              <p><?php echo $listInfo['comments']?></p>
				        </div>
				              <?php } ?>      
			    		<?php
					}else{ ?>
							<?php echo $_SESSION['text']['common']['No Records Found']?>
							<?php } ?>

	        </div> <!-- end chat-message-content -->
    	</div> <!-- end chat-message -->
	
		<fieldset> 
	          <textarea name="comments" value="<?php echo $post['comments']?>" class="ui-autocomplete-input" aria-autocomplete="list" aria-haspopup="true" placeholder="Add your comment here"><?php echo $errMsg['comments']?></textarea>
	    </fieldset>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="actionSec">
	<tr>
    	<td style="padding-top: 6px;text-align:right;">
    		</a>&nbsp;

         	<?php $actFun1 = SP_DEMO ? "alertDemoMsg()" : pluginPOSTMethod('projectform', 'content', 'action=diaryManager'); ?>
    		<a onclick="<?php echo $actFun1?>" href="javascript:void(0);" class="actionbut">
         		<?php echo $pluginText['Cancel']?>
         	</a>&nbsp;

         	<?php $actFun = SP_DEMO ? "alertDemoMsg()" : pluginPOSTMethod('projectform', 'content', 'action=createComment'); ?>
         	<a onclick="<?php echo $actFun?>" href="javascript:void(0);" class="actionbut">
         		<?php echo $pluginText['Add Comment']?>
         	</a>

    	</td>
	</tr>
</table>
    </div> <!-- end chat-history -->
</div>
   

	

</form>