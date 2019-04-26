<?php echo showSectionHead($spTextPanel['Edit Project']); ?>
<form id="projectform">
<input type="hidden" name="id" value="<?php echo $post['id']?>"/>
<table id="cust_tab">
	<tr class="form_head">
		<th width='30%'><?php echo $spTextPanel['Edit Project']?></th>
		<th>&nbsp;</th>
	</tr>	
	<tr class="form_data">
		<td><?php echo $spText['common']['Website']?>:</td>
		<td>
			<select name="website_id">
				<?php foreach($websiteList as $websiteInfo){?>
					<?php if($websiteInfo['id'] == $post['website_id']){?>
						<option value="<?php echo $websiteInfo['id']?>" selected><?php echo $websiteInfo['name']?></option>
					<?php }else{?>
						<option value="<?php echo $websiteInfo['id']?>"><?php echo $websiteInfo['name']?></option>
					<?php }?>						
				<?php }?>
			</select>
			<?php echo $errMsg['website_id']?>
		</td>
	</tr>
	<tr class="form_data">
		<td><?php echo $spText['common']['Name']?>:</td>
		<td><input type="text" name="name" value="<?php echo $post['name']?>"><?php echo $errMsg['name']?></td>
	</tr>
	<tr class="form_data">
		<td><?php echo $spText['label']['Description']?>:</td>
		<td>
			<textarea name="description"><?php echo $post['description']?></textarea>
			<br>
			<?php echo $errMsg['description']?>
		</td>
	</tr>
</table>
<br>
<table width="100%" class="actionSec">
	<tr>
    	<td style="padding-top: 6px;text-align:right;">
    		<a onclick="<?php echo pluginGETMethod('', 'content')?>" href="javascript:void(0);" class="actionbut">
         		<?php echo $spText['button']['Cancel']?>
         	</a>&nbsp;
         	<?php $actFun = SP_DEMO ? "alertDemoMsg()" : pluginConfirmPOSTMethod('projectform', 'content', 'action=updateProject'); ?>
         	<a onclick="<?php echo $actFun?>" href="javascript:void(0);" class="actionbut">
         		<?php echo $spText['button']['Proceed']?>
         	</a>
    	</td>
	</tr>
</table>
</form>