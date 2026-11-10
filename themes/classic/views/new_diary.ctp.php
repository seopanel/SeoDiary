<?php /* Shared .sd-form/.sd-* design system lives in css/sd.css (plugin-wide,
   auto-loaded on every page - see SeoPluginsController::loadAllPluginCss()),
   not duplicated here. This view renders in three places: inside the app's
   jQuery UI popup (scriptDoLoadDialog(), card chrome already supplied by
   .ui-dialog), as a plain full-page route (Plugins > Seo Diary > Diary
   Manager > New Diary, no surrounding chrome at all - see
   diary_manager.ctp.php's link), and inside the newer Bootstrap
   #ajaxModalSP (js/popup.js's openAjaxModalSP(), used by the dashboard's
   own "Add to SEO Diary" quick action - see recommendations_main.ctp.php/
   dashboard/main.ctp.php) - .sd-form carries its own card frame since a
   wrapper isn't always there in the other two contexts. */ ?>

<div class="sd-form">
    <div class="sd-form-header">
        <i class="fas fa-book"></i>
        <h3><?php echo $pluginText['New Diary']?></h3>
    </div>

    <?php if (empty($projectList)) {
        // openAjaxModalSP(), not pluginGETMethod('action=newProject',
        // 'content') - that one always targets the page's own #content,
        // which is correct for the first two contexts above but wrong for
        // the third: from inside #ajaxModalSPBody, it loaded the New
        // Project form into #content instead, which sits BEHIND this
        // still-open modal - invisible until the modal was closed.
        // openAjaxModalSP() reloads this same modal's body in place
        // regardless of which of the three contexts opened it, so it
        // works everywhere this view does.
        $newProjectUrl = SP_WEBPATH . '/seo-plugins.php?pid=' . PLUGIN_ID . '&action=newProject&in_modal=1';
        $newProjectTitle = '<i class="fas fa-folder-plus"></i> ' . ($spTextPanel['New Project'] ?? 'New Project');
        $newProjectOnclick = "openAjaxModalSP('" . addslashes($newProjectUrl) . "', '" . addslashes($newProjectTitle) . "')";
    ?>
    <div class="sd-empty-state">
        <i class="fas fa-exclamation-triangle"></i>
        <p>
            You don't have any projects yet - a diary entry has to belong to one.
            <a href="javascript:void(0);" onclick="<?php echo htmlspecialchars($newProjectOnclick, ENT_QUOTES)?>">Create a project first</a>.
        </p>
    </div>
    <?php } ?>

    <form id="projectform">
        <div class="sd-form-row-pair">
            <div class="sd-form-row">
                <label><?php echo $spText['label']['Project']?></label>
                <select name="project_id" class="custom-select">
                    <option value="">-- <?php echo $spText['common']['Select']?> --</option>
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
                    <option value="">-- <?php echo $spText['common']['Select']?> --</option>
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
                <?php echo $errMsg['status']?>
            </div>
            <div class="sd-form-row">
                <label>&nbsp;</label>
                <div class="sd-form-check">
                    <input type="checkbox" id="sdEmailNotif" name="email_notification" value="1" <?php echo !empty($post['email_notification']) ? "checked='checked'" : ""?>>
                    <label for="sdEmailNotif"><?php echo $spTextReport['Email notification']?></label>
                </div>
            </div>
        </div>

        <?php
        // Same $inModal plumbing as new_project.ctp.php (set by
        // SD_Manager::newDiary() from the in_modal request param) - this
        // form is reached both as a plain full-page route (#content is
        // correct) and inside the dashboard's "Add to SEO Diary" Bootstrap
        // modal (#content is the page behind it, invisible until closed -
        // confirmed live via screenshot).
        $area = !empty($inModal) ? 'ajaxModalSPBody' : 'content';
        $createArgs = 'action=createDiary' . (!empty($inModal) ? '&in_modal=1' : '');
        ?>
        <div class="sd-form-actions">
            <a onclick="<?php echo pluginGETMethod('action=diaryManager', $area)?>" href="javascript:void(0);" class="btn btn-warning">
                <?php echo $spText['button']['Cancel']?>
            </a>
            <?php $actFun = SP_DEMO ? "alertDemoMsg()" : pluginPOSTMethod('projectform', $area, $createArgs); ?>
            <a onclick="<?php echo $actFun?>" href="javascript:void(0);" class="btn btn-primary">
                <?php echo $spText['button']['Proceed']?>
            </a>
        </div>
    </form>
</div>
