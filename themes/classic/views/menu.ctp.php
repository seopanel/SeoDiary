<?php
$pluginCtrler = new SeoPluginsController();
$pluginText = $pluginCtrler->getLanguageTexts('seodiary', $_SESSION['lang_code']);
$spTextPanel = $pluginCtrler->getLanguageTexts('panel', $_SESSION['lang_code']);
$spTextSA = $pluginCtrler->getLanguageTexts('siteauditor', $_SESSION['lang_code']);
?>
<ul id='subui'>
    <?php if(isAdmin() || SD_ALLOW_USER_PROJECTS) {?>
        <li><a href="javascript:void(0);" onclick="<?php echo pluginMenu('action=projectManager'); ?>"><?php echo $pluginText['Projects Manager']?></a></li>
        <li><a href="javascript:void(0);" onclick="<?php echo pluginMenu('action=projectSummery'); ?>"><?php echo $spTextSA['Project Summary']?></a></li>
        <li><a href="javascript:void(0);" onclick="<?php echo pluginMenu('action=diaryManager'); ?>"><?php echo $pluginText['Diary Manager']?></a></li>
    <?php } ?>
    
    <li><a href="javascript:void(0);" onclick="<?php echo pluginMenu('action=myTasks'); ?>"><?php echo $pluginText['My Tasks']?></a></li>
    <li><a href="javascript:void(0);" onclick="<?php echo pluginMenu('action=newComment'); ?>"><?php echo $pluginText['Diary Comments']?></a></li>
    
    <?php if(isAdmin()) {?>
        <li><a href="javascript:void(0);" onclick="<?php echo pluginMenu('action=settings'); ?>"><?php echo $spTextPanel['Settings']?></a></li>
    <?php } ?>
    
</ul>