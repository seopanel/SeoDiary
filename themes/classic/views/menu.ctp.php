<?php
$pluginCtrler = new SeoPluginsController();
$pluginText = $pluginCtrler->getPluginLanguageTexts('seodiary', $_SESSION['lang_code'], 'sd_texts');
?>
<ul id='subui'>
    <?php if(isAdmin() || SD_ALLOW_USER_PROJECTS) {?>
        <li><a href="javascript:void(0);" onclick="<?php echo pluginMenu('action=projectManager'); ?>"><?php echo $pluginText['Projects Manager']?></a></li>
        <li><a href="javascript:void(0);" onclick="<?php echo pluginMenu('action=projectSummery'); ?>"><?php echo $pluginText['Project Summary']?></a></li>
        <li><a href="javascript:void(0);" onclick="<?php echo pluginMenu('action=diaryManager'); ?>"><?php echo $pluginText['Diary Manager']?></a></li>
    <?php } ?>
    
    <li><a href="javascript:void(0);" onclick="<?php echo pluginMenu('action=newComment'); ?>"><?php echo $pluginText['Diary Comments']?></a></li>
    
    <?php if(isAdmin()) {?>
        <li><a href="javascript:void(0);" onclick="<?php echo pluginMenu('action=settings'); ?>"><?php echo $pluginText['Plugin Settings']?></a></li>
    <?php } ?>
    
    <li><a href="javascript:void(0);" onclick="<?php echo pluginMenu('action=myTasks'); ?>"><?php echo $pluginText['My Tasks']?></a></li>
    <li><a href="javascript:void(0);" onclick="<?php echo pluginMenu('action=cronJob'); ?>"><?php echo 'Cron Job'?></a></li>
</ul>