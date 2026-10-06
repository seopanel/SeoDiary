<?php /* Card twin of new_diary.ctp.php - same fields, same .sd-form design
   system (css/sd.css), same AI-draft affordance. Kept in sync with that
   file's field set deliberately; a divergence here is a regression. */ ?>

<div class="sd-form">
    <div class="sd-form-header">
        <i class="fas fa-edit"></i>
        <h3><?php echo $pluginText['Edit Diary']?></h3>
    </div>

    <form id="projectform">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($post['id'] ?? '')?>"/>

        <div class="sd-form-row-pair">
            <div class="sd-form-row">
                <label><?php echo $spText['label']['Project']?></label>
                <select name="project_id" class="custom-select">
                    <?php foreach($projectList as $projectInfo){?>
                        <?php if($projectInfo['id'] == $post['project_id']){?>
                            <option value="<?php echo $projectInfo['id']?>" selected><?php echo $projectInfo['name']?></option>
                        <?php }else{?>
                            <option value="<?php echo $projectInfo['id']?>"><?php echo $projectInfo['name']?></option>
                        <?php }?>
                    <?php }?>
                </select>
                <?php echo $errMsg['project_id']?>
            </div>
            <div class="sd-form-row">
                <label><?php echo $spText['common']['Category']?></label>
                <select name="category_id" class="custom-select">
                    <?php foreach($categoryList as $categoryInfo){?>
                        <?php if($categoryInfo['id'] == $post['category_id']){?>
                            <option value="<?php echo $categoryInfo['id']?>" selected><?php echo $categoryInfo['label']?></option>
                        <?php }else{?>
                            <option value="<?php echo $categoryInfo['id']?>"><?php echo $categoryInfo['label']?></option>
                        <?php }?>
                    <?php }?>
                </select>
                <?php echo $errMsg['category_id']?>
            </div>
        </div>

        <div class="sd-form-row">
            <label><?php echo $spText['label']['Title']?></label>
            <input type="text" id="sdEditTitleInput" name="title" value="<?php echo htmlspecialchars($post['title'] ?? '')?>">
            <?php echo $errMsg['title']?>
        </div>

        <?php if (!empty($localAiAvailable)) { ?>
        <div class="sd-ai-draft">
            <button type="button" id="sdEditAiDraftBtn" class="btn btn-sm btn-outline-primary" onclick="sdEditSuggestDiaryDescription()">
                <i class="fas fa-magic"></i> Draft Description with AI
            </button>
            <p>Drafts a description below from the Title (and Category, if selected) using your on-premise Local AI (Ollama) - review before saving.</p>
        </div>
        <script type="text/javascript">
        function sdEditSuggestDiaryDescription() {
            var title = document.getElementById('sdEditTitleInput').value;
            if (!title) {
                alert('Please enter a title first.');
                return;
            }
            var categorySelect = document.querySelector('#projectform select[name="category_id"]');
            var btn = document.getElementById('sdEditAiDraftBtn');
            var originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Drafting...';
            $.ajax({
                url: '<?php echo PLUGIN_SCRIPT_URL; ?>&action=suggestDiaryDescription',
                type: 'GET',
                data: { title: title, category_id: categorySelect ? categorySelect.value : '' },
                dataType: 'json',
                success: function(response) {
                    if (response.ok) {
                        document.querySelector('#projectform textarea[name="description"]').value = response.description;
                    } else {
                        alert(response.error || 'Could not generate a description.');
                    }
                },
                error: function() {
                    alert('Could not generate a description.');
                },
                complete: function() {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            });
        }
        </script>
        <?php } ?>

        <div class="sd-form-row">
            <label><?php echo $spText['label']['Description']?></label>
            <textarea name="description"><?php echo htmlspecialchars($post['description'] ?? '')?></textarea>
            <?php echo $errMsg['description']?>
        </div>

        <div class="sd-form-row-pair">
            <div class="sd-form-row">
                <label><?php echo $pluginText['Assignee']?></label>
                <select name="assigned_user_id" class="custom-select">
                    <option value="">-- <?php echo $spText['common']['Select']?> --</option>
                    <?php foreach($userList as $userInfo){?>
                        <?php if($userInfo['id'] == $post['assigned_user_id']){?>
                            <option value="<?php echo $userInfo['id']?>" selected><?php echo $userInfo['username']?></option>
                        <?php }else{?>
                            <option value="<?php echo $userInfo['id']?>"><?php echo $userInfo['username']?></option>
                        <?php }?>
                    <?php }?>
                </select>
            </div>
            <div class="sd-form-row">
                <label><?php echo $pluginText['Due Date']?></label>
                <input type="text" name="due_date" value="<?php echo htmlspecialchars($post['due_date'] ?? '')?>">
                <?php echo $errMsg['due_date']?>
                <script type="text/javascript">
                $(function() {
                    $( "input[name='due_date']").datepicker({dateFormat: "yy-mm-dd"});
                });
                </script>
            </div>
        </div>

        <div class="sd-form-row-pair">
            <div class="sd-form-row">
                <label><?php echo $spText['common']['Status']?></label>
                <select name="status" class="custom-select">
                    <?php foreach($statusList as $statVal => $statLabel){?>
                        <?php if($statVal == $post['status']){?>
                            <option value="<?php echo $statVal?>" selected><?php echo $statLabel?></option>
                        <?php }else{?>
                            <option value="<?php echo $statVal?>"><?php echo $statLabel?></option>
                        <?php }?>
                    <?php }?>
                </select>
                <?php echo $errMsg['status']?>
            </div>
            <div class="sd-form-row">
                <label>&nbsp;</label>
                <div class="sd-form-check">
                    <input type="checkbox" id="sdEditEmailNotif" name="email_notification" value="1" <?php echo !empty($post['email_notification']) ? "checked='checked'" : ""?>>
                    <label for="sdEditEmailNotif"><?php echo $spTextReport['Email notification']?></label>
                </div>
            </div>
        </div>

        <div class="sd-form-actions">
            <a onclick="<?php echo pluginGETMethod('action=diaryManager', 'content')?>" href="javascript:void(0);" class="btn btn-warning">
                <?php echo $spText['button']['Cancel']?>
            </a>
            <?php $actFun = SP_DEMO ? "alertDemoMsg()" : pluginConfirmPOSTMethod('projectform', 'content', 'action=updateDiary'); ?>
            <a onclick="<?php echo $actFun?>" href="javascript:void(0);" class="btn btn-primary">
                <?php echo $spText['button']['Proceed']?>
            </a>
        </div>
    </form>
</div>
