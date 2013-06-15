<?php /* @var $options BelaOptions */ ?>
<?php $this->renderPartial('common/nav-tab'); ?>
<p><?php _e('Control the cut off of ELA.'); ?></p>
<form method="post" actions="<?php echo BelaAdmin::URL('howToCut'); ?>">
    <table class="form-table">
        <tbody>
            <?php
            BelaHtml::optionTextInput($options, BelaKey::MAX_ENTRY_TITLE_LENGTH);
            BelaHtml::optionTextInput($options, BelaKey::MAX_CATEGORY_NAME_LENGTH);
            BelaHtml::optionTextInput($options, BelaKey::TRUNCATED_TEXT);
            BelaHtml::optionCheckbox($options, BelaKey::TRUNCATE_BREAK_WORD);
            BelaHtml::optionCheckbox($options, BelaKey::ABBREVIATE_MONTH_NAME);
            BelaHtml::optionRadioGroup($options, BelaKey::TAGS_PICK_STRATEGY, array(
                BelaKey::TAG_STRATEGY_SHOW_ALL,
                BelaKey::TAG_STRATEGY_FIRST_X_MOST_USED,
                BelaKey::TAG_STRATEGY_TAG_AT_LEAST_X_POST,
            ));
            BelaHtml::optionTextInput($options, BelaKey::TAG_STRATEGY_THRESHOLD);
            ?>
        </tbody>
    </table>
    <p class="submit">
        <input type="submit" value="<?php _e("Save Changes", 'bela'); ?>" class="button button-primary" id="submit" name="submit">
    </p>
</form>