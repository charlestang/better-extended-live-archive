<?php $this->renderPartial('common/nav-tab'); ?>
<p><?php _e('Control the output text tips of ELA.'); ?></p>
<form method="post" actions="<?php echo BelaAdmin::URL('howToShow'); ?>">
    <table class="form-table">
        <tbody>
            <?php
            BelaHtml::optionTextInput($options, BelaKey::SELECTED_SIGN);
            BelaHtml::optionTextInput($options, BelaKey::SELECTED_CLASS);
            BelaHtml::optionTextInput($options, BelaKey::TEMPLATE_NUMBER_OF_ENTRIES, true);
            BelaHtml::optionTextInput($options, BelaKey::TEMPLATE_NUMBER_OF_ENTRIES_PER_TAG, true);
            BelaHtml::optionTextInput($options, BelaKey::TEMPLATE_NUMBER_OF_COMMENTS, true);
            BelaHtml::optionTextInput($options, BelaKey::COMMENTS_CLOSED_SIGN, true);
            BelaHtml::optionTextInput($options, BelaKey::POST_DATE_FORMAT_STRING);
            ?>
        </tbody>
    </table>
    <p class="submit">
        <input type="submit" value="<?php _e("Save Changes", 'bela'); ?>" class="button button-primary" id="submit" name="submit">
    </p>
</form>