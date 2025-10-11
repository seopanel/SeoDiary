<?php echo showSectionHead($spTextPanel['Settings']); ?>
<?php if(!empty($saved)) showSuccessMsg($spSettingsText['allsettingssaved'], false); ?>
<form id="updateSettings">
<input type="hidden" value="update" name="sec">
<table id="cust_tab">
	<tr class="form_head">
		<th width='30%'><?php echo $spTextPanel['Settings']?></th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	foreach( $settingsList as $i => $listInfo){ 
		switch($listInfo['set_type']){
			
			case "small":
				$width = 40;
				break;

			case "bool":
				if(empty($listInfo['set_val'])){
					$selectYes = "";					
					$selectNo = "selected";
				}else{					
					$selectYes = "selected";					
					$selectNo = "";
				}
				break;
				
			case "medium":
				$width = 200;
				break;

			case "large":
			case "text":
				$width = 500;
				break;
		}

		?>
     	<tr class="form_data">
			<td><?php echo $pluginText[$listInfo['set_name']]?>:</td>
		 	<?php if($listInfo['set_type'] != 'text'){?>
				<?php if($listInfo['set_type'] == 'bool'){?>
				<td class="td_right_col">
					<select name="<?php echo $listInfo['set_name']?>" class="custom-select">
						<option value="1" <?php echo $selectYes?>><?php echo $spText['common']['Yes']?></option>
						<option value="0" <?php echo $selectNo?>><?php echo $spText['common']['No']?></option>
					</select>
					<?php } ?>
				</td>
				<?php } ?>
	    </tr>
	<?php }?>

</table>
<table class="actionSec float-right mt-2">
	<tr>
    	<td>
    		<a onclick="<?php echo pluginGETMethod('action=settings', 'content')?>" href="javascript:void(0);" class="btn btn-warning">
         		<?php echo $spText['button']['Cancel']?>
         	</a>&nbsp;
         	<?php $actFun = SP_DEMO ? "alertDemoMsg()" : pluginConfirmPOSTMethod('updateSettings', 'content', 'action=updateSettings');?>
         	<a onclick="<?php echo $actFun?>" href="javascript:void(0);" class="btn btn-primary">
         		<?php echo $spText['button']['Proceed']?>
         	</a>
    	</td>
	</tr>
</table>
</form>