<?php echo $spText['common']['Hello']?> <?php echo $userName?>,<br><br>

<b><?php echo $reminderLabel?>:</b><br><br>

<h2><?php echo $listInfo['title']?></h2><br>
<b><?php echo $pluginText['Project']?>:</b> <?php echo $projectName?><br>
<b><?php echo $pluginText['Due Date']?>:</b> <?php echo $listInfo['due_date']?><br>
<b><?php echo $spText['common']['Status']?>:</b> <?php echo $listInfo['status']?><br><br>

<pre><?php echo $listInfo['description']?></pre><br><br>

<a href='<?php echo SP_WEBPATH?>/login.php'><?php echo $spText['label']['Click Here']?></a> <?php echo $spText['login']['to login to your account']?><br><br>

<?php echo $spText['common']['Thank you']; ?>,<br>
<?php echo SP_COMPANY_NAME?><br>
<a href="<?php echo SP_WEBPATH?>"><?php echo SP_WEBPATH?></a>
