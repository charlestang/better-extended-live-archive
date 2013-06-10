<?php $this->renderPartial('common/nav-tab'); ?>
<h3 class="title"><?php _e('What to show?'); ?></h3>
<p><?php _e('Control the output infomation of ELA.'); ?></p>
<table class="form-table">
    <tbody>
        <?php
        BelaHtml::optionCheckbox($options, BelaKey::SHOW_NEWEST_FIRST);
        BelaHtml::optionCheckbox($options, BelaKey::SHOW_NUMBER_OF_ENTRIES);
        BelaHtml::optionCheckbox($options, BelaKey::SHOW_NUMBER_OF_ENTRIES_PER_TAG);
        BelaHtml::optionCheckbox($options, BelaKey::SHOW_NUMBER_OF_COMMENTS);
        BelaHtml::optionCheckbox($options, BelaKey::FADE_EVERYTHING);
        BelaHtml::optionCheckbox($options, BelaKey::EXCLUDE_TRACKBACKS);
        BelaHtml::optionCheckbox($options, BelaKey::PAGINATE_THE_LIST);
        //BelaHtml::adminCheckbox(
        //        __('Use the default CSS stylesheet:', 'ela'), 'use_default_style', $settings['use_default_style'], __('If it exists, will link the <strong>ela.css</strong> stylesheet of your theme. If not present, will link the default stylesheet.', 'ela'));
        ?>
    </tbody>
</table>