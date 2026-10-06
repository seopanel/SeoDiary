<div class="sd-form">
    <div class="sd-form-header">
        <i class="fas fa-chart-pie"></i>
        <h3><?php echo $spTextSA['Project Summary']?></h3>
    </div>

    <form id="projectform">
        <div class="sd-selector-bar">
            <span><?php echo $spText['label']['Project']?>:</span>
            <select onchange="doDiaryAction('<?php echo PLUGIN_SCRIPT_URL?>', 'content', 'action=projectSummery', 'project_id','project_id')" name="project_id" id="project_id" class="custom-select">
                <?php foreach($projectList as $prjInfo){?>
                    <?php if($prjInfo['id'] == $post['project_id']){?>
                        <option value="<?php echo $prjInfo['id']?>" selected><?php echo $prjInfo['name']?></option>
                    <?php }else{?>
                        <option value="<?php echo $prjInfo['id']?>"><?php echo $prjInfo['name']?></option>
                    <?php }?>
                <?php }?>
            </select>
        </div>

        <div class="sd-source-desc"><?php echo nl2br(htmlspecialchars($projectInfo['description']))?></div>

        <?php if (!empty($localAiAvailable)) { ?>
        <div class="sd-ai-draft">
            <button type="button" id="sdAiSummaryBtn" class="btn btn-sm btn-outline-primary" onclick="sdGenerateProjectAISummary(<?php echo intval($projectInfo['id'])?>)">
                <i class="fas fa-magic"></i> AI Summary
            </button>
            <div id="sdAiSummaryRow" style="display:none; margin-top:10px;">
                <div id="sdAiSummaryText" class="alert alert-info" style="margin-bottom:0;"></div>
            </div>
        </div>
        <script type="text/javascript">
        function sdGenerateProjectAISummary(projectId) {
            var btn = document.getElementById('sdAiSummaryBtn');
            var originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Summarizing...';
            $.ajax({
                url: '<?php echo PLUGIN_SCRIPT_URL; ?>&action=generateProjectAISummary',
                type: 'GET',
                data: { project_id: projectId },
                dataType: 'json',
                success: function(response) {
                    document.getElementById('sdAiSummaryRow').style.display = '';
                    if (response.ok) {
                        document.getElementById('sdAiSummaryText').className = 'alert alert-info';
                        document.getElementById('sdAiSummaryText').innerText = response.summary;
                    } else {
                        document.getElementById('sdAiSummaryText').className = 'alert alert-danger';
                        document.getElementById('sdAiSummaryText').innerText = response.error || 'Could not generate a summary.';
                    }
                },
                error: function() {
                    document.getElementById('sdAiSummaryRow').style.display = '';
                    document.getElementById('sdAiSummaryText').className = 'alert alert-danger';
                    document.getElementById('sdAiSummaryText').innerText = 'Could not generate a summary.';
                },
                complete: function() {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            });
        }
        </script>
        <?php } ?>

        <?php if(count($diaryList) > 0) {?>
        <div class="sd-item-list">
            <?php foreach($diaryList as $listInfo){?>
                <div class="sd-item-card">
                    <h5><?php echo htmlspecialchars($listInfo['title'])?></h5>
                    <p><?php echo nl2br(htmlspecialchars($listInfo['description']))?></p>
                    <a class="sd-item-link" href="javascript:void(0);" onclick="<?php echo pluginGETMethod('action=newComment&diary_id='.$listInfo['id'], 'content')?>">
                        <i class="fas fa-comment-dots"></i> <?php echo $listInfo['comment_count']?> comments
                    </a>
                </div>
            <?php }?>
        </div>
        <?php } else { ?>
        <p class="sd-no-items"><?php echo $spText['common']['No Records Found']?></p>
        <?php } ?>
    </form>
</div>
