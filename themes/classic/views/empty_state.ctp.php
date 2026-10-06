<?php /* Reusable "nothing here yet" page - used where a controller previously
   called showErrorMsg($_SESSION['text']['common']['No Records Found']), which
   prints a bare red error banner and exit()s the whole page, leaving no way
   forward. Expects: $emptyIcon (a Font Awesome class, no "fa-" prefix check -
   pass it whole, e.g. 'fa-folder-open'), $emptyHeading, $emptyMessage, and
   optionally $emptyCtaAction (a pluginGETMethod() action string) +
   $emptyCtaLabel for a CTA button. */ ?>
<div class="sd-empty-page">
    <div class="sd-empty-page-icon">
        <i class="fas <?php echo htmlspecialchars($emptyIcon)?>"></i>
    </div>
    <h3><?php echo htmlspecialchars($emptyHeading)?></h3>
    <p><?php echo htmlspecialchars($emptyMessage)?></p>
    <?php if (!empty($emptyCtaAction)) { ?>
    <a href="javascript:void(0);" onclick="<?php echo pluginGETMethod($emptyCtaAction, 'content')?>" class="btn btn-primary btn-lg">
        <i class="fas fa-plus-circle"></i> <?php echo htmlspecialchars($emptyCtaLabel)?>
    </a>
    <?php } ?>
</div>
