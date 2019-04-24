<html>
<body>
<?php echo "Notification Mail Form Seo Panel"?><br>
<?php echo $mailContent; ?>
<br>
<?php
$custSiteInfo = getCustomizerDetails();
$mailLink = SP_WEBPATH."/admin-panel.php?menu_selected=report-manager&start_script=archive&website_id=0";
echo str_replace('[LOGIN_LINK]', "<a href='$reportLink'>{$loginTexts['Login']}</a>", $pluginText['Diary Manager']); 
?>
<br>
</body>
</html>