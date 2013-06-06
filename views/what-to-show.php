<?php $this->render('common/nav-tab');?>
<h3 class="title"><?php _e('What to show?'); ?></h3>
<p><?php _e('Control the output infomation of ELA.'); ?></p>
<table class="form-table">
    <tbody>
        <?php
        better_ela_helper_chkbox(
                __('Show Newest First:', 'ela'), 'newest_first', $settings['newest_first'], __('Enabling this will show the newest post first in the listings.', 'ela'));
        better_ela_helper_chkbox(
                __('Show Number of Entries:', 'ela'), "num_entries", $settings['num_entries'], __('Sets whether the number of entries for each year, month, category should be shown.', 'ela'));
        better_ela_helper_chkbox(
                __('Show Number of Entries Per Tag:', 'ela'), "num_entries_tagged", $settings['num_entries_tagged'], __('Sets whether the number of entries for each tags should be shown', 'ela'));
        better_ela_helper_chkbox(
                __('Show Number of Comments:', 'ela'), "num_comments", $settings["num_comments"], __('Sets whether the number of comments for each entry should be shown', 'ela'));
        better_ela_helper_chkbox(
                __('Fade Anything Technique:', 'ela'), 'fade', $settings['fade'], __('Sets whether changes should fade using the Fade Anything ', 'ela'));
        better_ela_helper_chkbox(
                __('Hide Ping- and Trackbacks:', 'ela'), 'hide_pingbacks_and_trackbacks', $settings['hide_pingbacks_and_trackbacks'], __('Sets whether ping- and trackbacks should influence the number of comments on an entry', 'ela'));
        better_ela_helper_chkbox(
                __('Use the default CSS stylesheet:', 'ela'), 'use_default_style', $settings['use_default_style'], __('If it exists, will link the <strong>ela.css</strong> stylesheet of your theme. If not present, will link the default stylesheet.', 'ela'));
        better_ela_helper_chkbox(
                __('Layout the posts link into pages:', 'ela'), 'paged_posts', $settings['paged_posts'], __('Sets whether the posts list will be cut into several pages or just the complete list.', 'ela'));
        ?>
    </tbody>
</table>