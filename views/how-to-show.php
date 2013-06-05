<?php $this->render('common/nav-tab');?>
<h3 class="title"><?php _e('How to show?'); ?></h3>
<p><?php _e('Control the output text tips of ELA.'); ?></p>
<table class="form-table">
    <tbody>
        <?php
        BelaHtml::adminTextInput(
                __('Selected Text:', 'ela'), 'selected_text', $settings['selected_text'], __('The text that is shown after the currently selected year, month or category.', 'ela'));
        BelaHtml::adminTextInput(
                __('Selected Class:', 'ela'), 'selected_class', $settings['selected_class'], __('The CSS class for the currently selected year, month or category.', 'ela'));
        BelaHtml::adminTextInput(
                __('# of Entries Text:', 'ela'), 'number_text', $settings['number_text'], __('The string to show for number of entries per year, month or category. Can contain HTML. % is replaced with number of entries.', 'ela'), true);
        BelaHtml::adminTextInput(
                __('# of Tagged-Entries Text:', 'ela'), 'number_text_tagged', $settings['number_text_tagged'], __('The string to show for number of entries per tag. Can contain HTML. % is replaced with number of entries.', 'ela'), true);
        BelaHtml::adminTextInput(
                __('# of Comments Text:', 'ela'), 'comment_text', $settings['comment_text'], __('The string to show for comments. Can contain HTML. % is replaced with number of comments.', 'ela'), true);
        BelaHtml::adminTextInput(
                __('Closed Comment Text:', 'ela'), 'closed_comment_text', $settings['closed_comment_text'], __('The string to show if comments are closed on an entry. Can contain HTML.', 'ela'), true);
        BelaHtml::adminTextInput(
                __('Day of Posting Format:', 'ela'), 'day_format', $settings['day_format'], __('A date format string to show the day for each entry in the chronological tab only (\'jS\' to show 1st, 3rd, and 14th). Format string is in the <a href="http://www.php.net/date">php date format</a>. Reference to year and month in there will result in error : this intended for days only. Leave empty to show no date.', 'ela'));
        BelaHtml::adminTextInput(
                __('Error Class:', 'ela'), 'error_class', $settings['error_class'], __('The CSS class to put on paragraphs containing errors.', 'ela'));
        ?>
    </tbody>
</table>