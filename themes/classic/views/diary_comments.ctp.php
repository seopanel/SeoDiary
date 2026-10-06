<div class="sd-form">
    <div class="sd-form-header">
        <i class="fas fa-comments"></i>
        <h3><?php echo $pluginText["Diary Comments"]?></h3>
    </div>

    <form id="projectform">
        <div class="sd-selector-bar">
            <span><?php echo $spText['common']['Name']?>:</span>
            <select onchange="doDiaryAction('<?php echo PLUGIN_SCRIPT_URL?>', 'content', 'action=newComment', 'diary_id','diary_id')" name="diary_id" id="diary_id" class="custom-select">
                <?php foreach($diaryList as $drInfo){?>
                    <?php if($drInfo['id'] == $diaryId){?>
                        <option value="<?php echo $drInfo['id']?>" selected><?php echo htmlspecialchars($drInfo['title'])?></option>
                    <?php }else{?>
                        <option value="<?php echo $drInfo['id']?>"><?php echo htmlspecialchars($drInfo['title'])?></option>
                    <?php }?>
                <?php }?>
            </select>
            <?php echo $errMsg['diary_id']?>
        </div>

        <div class="sd-source-desc"><?php echo htmlspecialchars($diaryInfo['description'])?></div>

        <?php if(count($diaryCommentList) > 0) {?>
        <div class="sd-item-list">
            <?php foreach($diaryCommentList as $i => $listInfo){?>
                <div class="sd-comment">
                    <p class="sd-comment-text"><?php echo nl2br(htmlspecialchars($listInfo['comments']))?></p>
                    <div class="sd-comment-meta">
                        <span><?php echo htmlspecialchars($userIdList[$listInfo['user_id']]['username'] ?? '')?></span>
                        <span><?php echo $listInfo['updated_time']?></span>
                    </div>
                </div>
            <?php }?>
        </div>
        <?php } else { ?>
        <p class="sd-no-items"><?php echo $spText['common']['No Records Found']?></p>
        <?php } ?>

        <div class="sd-form-row">
            <label><?php echo $pluginText['Add Comment']?></label>
            <textarea name="comments" placeholder="<?php echo $pluginText['Add your comment here']?>..."><?php echo htmlspecialchars($post['comments'] ?? '')?></textarea>
            <?php echo $errMsg['comments']?>
        </div>

        <div class="sd-form-actions">
            <?php $actFun1 = SP_DEMO ? "alertDemoMsg()" : pluginPOSTMethod('projectform', 'content', 'action=newComment'); ?>
            <a onclick="<?php echo $actFun1?>" href="javascript:void(0);" class="btn btn-warning">
                <?php echo $spText['button']['Cancel']?>
            </a>
            <?php $actFun = SP_DEMO ? "alertDemoMsg()" : pluginPOSTMethod('projectform', 'content', 'action=createComment'); ?>
            <a onclick="<?php echo $actFun?>" href="javascript:void(0);" class="btn btn-primary">
                <?php echo $pluginText['Add Comment']?>
            </a>
        </div>
    </form>
</div>
