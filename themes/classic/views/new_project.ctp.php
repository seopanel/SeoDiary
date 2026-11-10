<div class="sd-form">
    <div class="sd-form-header">
        <i class="fas fa-folder-plus"></i>
        <h3><?php echo $spTextPanel['New Project']?></h3>
    </div>

    <form id="projectform">
        <div class="sd-form-row">
            <label><?php echo $spText['common']['Website']?></label>
            <select name="website_id" class="custom-select">
                <?php foreach($websiteList as $websiteInfo){?>
                    <?php if($websiteInfo['id'] == $post['website_id']){?>
                        <option value="<?php echo $websiteInfo['id']?>" selected><?php echo $websiteInfo['name']?></option>
                    <?php }else{?>
                        <option value="<?php echo $websiteInfo['id']?>"><?php echo $websiteInfo['name']?></option>
                    <?php }?>
                <?php }?>
            </select>
            <?php echo $errMsg['website_id']?>
        </div>

        <div class="sd-form-row">
            <label><?php echo $spText['common']['Name']?></label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($post['name'] ?? '')?>">
            <?php echo $errMsg['name']?>
        </div>

        <div class="sd-form-row">
            <label><?php echo $spText['label']['Description']?></label>
            <textarea name="description"><?php echo htmlspecialchars($post['description'] ?? '')?></textarea>
            <?php echo $errMsg['description']?>
        </div>

        <?php
        // Reached two ways: the classic full-page Projects Manager's own
        // "+New Project" button (targets #content, same page) and the
        // dashboard's "Add to SEO Diary" -> "Create a project first"
        // nested flow (new_diary.ctp.php's empty state), which opens this
        // INSIDE the shared Bootstrap #ajaxModalSP - #content there is
        // the page sitting behind that still-open modal, invisible until
        // it's closed. $inModal (set by Project::newProject()/
        // createProject() from the in_modal request param new_diary.ctp.php
        // appends to its link) tells these two which area to actually
        // target. Cancel also goes back to the diary form instead of the
        // (here, irrelevant) Projects Manager list when nested.
        $area = !empty($inModal) ? 'ajaxModalSPBody' : 'content';
        $cancelArgs = !empty($inModal) ? 'action=newDiary&in_modal=1' : '';
        $createArgs = 'action=createProject' . (!empty($inModal) ? '&in_modal=1' : '');
        ?>
        <div class="sd-form-actions">
            <a onclick="<?php echo pluginGETMethod($cancelArgs, $area)?>" href="javascript:void(0);" class="btn btn-warning">
                <?php echo $spText['button']['Cancel']?>
            </a>
            <?php $actFun = SP_DEMO ? "alertDemoMsg()" : pluginPOSTMethod('projectform', $area, $createArgs); ?>
            <a onclick="<?php echo $actFun?>" href="javascript:void(0);" class="btn btn-primary">
                <?php echo $spText['button']['Proceed']?>
            </a>
        </div>
    </form>
</div>
