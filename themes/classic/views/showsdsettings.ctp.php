<div class="sd-form">
    <div class="sd-form-header">
        <i class="fas fa-sliders-h"></i>
        <h3><?php echo $spTextPanel['Settings']?></h3>
    </div>

    <?php if(!empty($saved)) showSuccessMsg($spSettingsText['allsettingssaved'], false); ?>

    <form id="updateSettings">
        <input type="hidden" value="update" name="sec">

        <?php foreach($settingsList as $i => $listInfo){ ?>
        <div class="sd-form-row">
            <label><?php echo $pluginText[$listInfo['set_name']]?></label>
            <?php if ($listInfo['set_type'] == 'bool') { ?>
                <select name="<?php echo $listInfo['set_name']?>" class="custom-select">
                    <option value="1" <?php echo !empty($listInfo['set_val']) ? 'selected' : ''?>><?php echo $spText['common']['Yes']?></option>
                    <option value="0" <?php echo empty($listInfo['set_val']) ? 'selected' : ''?>><?php echo $spText['common']['No']?></option>
                </select>
            <?php } else if ($listInfo['set_type'] == 'large' || $listInfo['set_type'] == 'text') { ?>
                <textarea name="<?php echo $listInfo['set_name']?>"><?php echo htmlspecialchars($listInfo['set_val'] ?? '')?></textarea>
            <?php } else { ?>
                <input type="text" name="<?php echo $listInfo['set_name']?>" value="<?php echo htmlspecialchars($listInfo['set_val'] ?? '')?>"
                    <?php echo $listInfo['set_type'] == 'small' ? 'style="max-width: 200px;"' : ''?>>
            <?php } ?>
        </div>
        <?php } ?>

        <div class="sd-form-actions">
            <a onclick="<?php echo pluginGETMethod('action=settings', 'content')?>" href="javascript:void(0);" class="btn btn-warning">
                <?php echo $spText['button']['Cancel']?>
            </a>
            <?php $actFun = SP_DEMO ? "alertDemoMsg()" : pluginConfirmPOSTMethod('updateSettings', 'content', 'action=updateSettings');?>
            <a onclick="<?php echo $actFun?>" href="javascript:void(0);" class="btn btn-primary">
                <?php echo $spText['button']['Proceed']?>
            </a>
        </div>
    </form>
</div>
