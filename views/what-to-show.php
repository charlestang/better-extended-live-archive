<?php /* @var $options BelaOptions */ ?>
<?php $this->renderPartial('common/nav-tab'); ?>
<p><?php _e('The basic options of <i>Better Extended Live Archive</i>. Control the output information of it.'); ?></p>
<form method="post" actions="<?php echo BelaAdmin::URL('whatToShow');?>">
<table class="form-table">
    <tbody>
        <?php
        BelaHtml::optionCheckbox($options, BelaKey::SHOW_LATEST_FIRST);
        BelaHtml::optionCheckbox($options, BelaKey::SHOW_NUMBER_OF_ENTRIES);
        BelaHtml::optionCheckbox($options, BelaKey::SHOW_NUMBER_OF_ENTRIES_PER_TAG);
        BelaHtml::optionCheckbox($options, BelaKey::SHOW_NUMBER_OF_COMMENTS);
        BelaHtml::optionCheckbox($options, BelaKey::FADE_EVERYTHING);
        BelaHtml::optionCheckbox($options, BelaKey::EXCLUDE_TRACKBACKS);
        BelaHtml::optionCheckbox($options, BelaKey::PAGINATE_THE_LIST);
        ?>
    </tbody>
</table>
<p class="submit">
    <input type="submit" value="<?php _e("Save Changes", 'bela');?>" class="button button-primary" id="submit" name="submit">
</p>
</form>