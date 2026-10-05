<style>
/* Scoped to .sd-form only - the old #cust_tab/.form_head/.form_data
   table classes this replaces are shared globals used across dozens of
   other admin pages, so this is a from-scratch look for this dialog
   rather than a retheme of those classes (which would ripple out to
   every other page still using them). This view renders in two places:
   inside the app's jQuery UI popup (scriptDoLoadDialog(), card chrome
   already supplied by .ui-dialog) and as a plain full-page route (Plugins
   > Seo Diary > Diary Manager > New Diary, no surrounding chrome at all -
   see diary_manager.ctp.php's link). Without its own card frame it just
   floats as bare fields against a huge empty page in that second context,
   so .sd-form carries its own border/radius/shadow rather than relying on
   a wrapper that isn't always there. */
.sd-form {
    max-width: 640px;
    margin: 24px auto;
    background: #fff;
    border: 1px solid #e3e8f0;
    border-radius: 14px;
    box-shadow: 0 1px 3px rgba(20, 30, 60, 0.06);
    padding: 28px 32px;
}
.sd-form-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 1px solid #e8edf5;
}
.sd-form-header i {
    font-size: 20px;
    color: #1a73e8;
}
.sd-form-header h3 {
    margin: 0;
    font-size: 19px;
    font-weight: 700;
    color: #1a1a2e;
}
.sd-form-row {
    margin-bottom: 16px;
}
.sd-form-row-pair {
    display: flex;
    gap: 16px;
}
.sd-form-row-pair .sd-form-row {
    flex: 1;
    min-width: 0;
}
.sd-form label {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: #5a6372;
    margin-bottom: 6px;
}
.sd-form input[type="text"],
.sd-form select,
.sd-form textarea {
    width: 100%;
    box-sizing: border-box;
    padding: 9px 12px;
    font-size: 14px;
    color: #1a1a2e;
    background: #fafbfd;
    border: 1px solid #d8dee8;
    border-radius: 8px;
    transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
}
.sd-form input[type="text"]:focus,
.sd-form select:focus,
.sd-form textarea:focus {
    outline: none;
    background: #fff;
    border-color: #1a73e8;
    box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.12);
}
.sd-form textarea {
    min-height: 90px;
    resize: vertical;
    font-family: inherit;
}
.sd-form-check {
    display: flex;
    align-items: center;
    gap: 8px;
}
.sd-form-check input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #1a73e8;
}
.sd-form-check label {
    margin: 0;
    text-transform: none;
    font-weight: 500;
    font-size: 13.5px;
    letter-spacing: normal;
    color: #333;
}
.sd-ai-draft {
    background: #eef2fb;
    border: 1px solid #d3def5;
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 16px;
}
.sd-ai-draft p {
    margin: 6px 0 0;
    font-size: 12px;
    color: #64748b;
}
.sd-ai-draft .btn {
    border-radius: 7px;
}
.sd-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 22px;
    padding-top: 16px;
    border-top: 1px solid #e8edf5;
}
.sd-form-actions .btn {
    border-radius: 8px;
    padding: 8px 18px;
    font-weight: 600;
}
</style>

<div class="sd-form">
    <div class="sd-form-header">
        <i class="fas fa-book"></i>
        <h3><?php echo $pluginText['New Diary']?></h3>
    </div>

    <form id="projectform">
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
            </div>
        </div>

        <div class="sd-form-row">
            <label><?php echo $spText['label']['Title']?></label>
            <input type="text" id="sdTitleInput" name="title" value="<?php echo htmlspecialchars($post['title'] ?? '')?>">
            <?php echo $errMsg['title']?>
        </div>

        <?php if (!empty($localAiAvailable)) { ?>
        <div class="sd-ai-draft">
            <button type="button" id="sdAiDraftBtn" class="btn btn-sm btn-outline-primary" onclick="sdSuggestDiaryDescription()">
                <i class="fas fa-magic"></i> Draft Description with AI
            </button>
            <p>Drafts a description below from the Title (and Category, if selected) using your on-premise Local AI (Ollama) - review before saving.</p>
        </div>
        <script type="text/javascript">
        function sdSuggestDiaryDescription() {
            var title = document.getElementById('sdTitleInput').value;
            if (!title) {
                alert('Please enter a title first.');
                return;
            }
            var categorySelect = document.querySelector('#projectform select[name="category_id"]');
            var btn = document.getElementById('sdAiDraftBtn');
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
                <?php $dueDate = !empty($post['due_date']) ? $post['due_date'] : date('Y-m-d', strtotime('+5 days')); ?>
                <input type="text" name="due_date" value="<?php echo $dueDate ?>">
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
            </div>
            <div class="sd-form-row">
                <label>&nbsp;</label>
                <div class="sd-form-check">
                    <input type="checkbox" id="sdEmailNotif" name="email_notification" value="1" <?php echo !empty($post['email_notification']) ? "checked='checked'" : ""?>>
                    <label for="sdEmailNotif"><?php echo $spTextReport['Email notification']?></label>
                </div>
            </div>
        </div>

        <div class="sd-form-actions">
            <a onclick="<?php echo pluginGETMethod('action=diaryManager', 'content')?>" href="javascript:void(0);" class="btn btn-warning">
                <?php echo $spText['button']['Cancel']?>
            </a>
            <?php $actFun = SP_DEMO ? "alertDemoMsg()" : pluginPOSTMethod('projectform', 'content', 'action=createDiary'); ?>
            <a onclick="<?php echo $actFun?>" href="javascript:void(0);" class="btn btn-primary">
                <?php echo $spText['button']['Proceed']?>
            </a>
        </div>
    </form>
</div>
