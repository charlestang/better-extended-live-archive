<?php $this->renderPartial('common/nav-tab'); ?>
<p><?php _e('The layout of the posts when using a paged list instead of complete list .', 'bela'); ?></p>
<form method="post" actions="<?php echo BelaAdmin::URL('pagination');?>">
<table class="form-table paged-posts-section">
    <tbody>
        <?php
        BelaHtml::optionTextInput($options, BelaKey::PAGE_OPT_NUMBER_PER_PAGE, true);
        BelaHtml::optionTextInput($options, BelaKey::PAGE_OPT_NEXT_PAGE_TEXT, true);
        BelaHtml::optionTextInput($options, BelaKey::PAGE_OPT_PREVIOUS_PAGE_TEXT, true);
        ?>
    </tbody>
</table>
<p class="submit">
    <input type="submit" value="<?php _e("Save Changes", 'bela');?>" class="button button-primary" id="submit" name="submit">
</p>
</form>