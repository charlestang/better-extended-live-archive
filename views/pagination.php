<?php $this->render('common/nav-tab'); ?>
    <h3 class="title"><?php _e('What about paged posts?.','ela');?></h3>
    <p><?php _e('The layout of the posts when using a paged list instead of complete list .','ela');?></p>
    <table class="form-table paged-posts-section"><tbody>
    <?php
    better_ela_helper_txtbox(
        __('Max # of Posts per page:','ela'),
        'paged_post_num', $settings['paged_post_num'],
        __('The max number of posts that will be listed per page.','ela'),
        true);
    better_ela_helper_txtbox(
        __('Next Page of Posts:','ela'),
        'paged_post_next', $settings['paged_post_next'],
        __('The text written as the link to the next page.','ela'),
        true);
    better_ela_helper_txtbox(
        __('Previous Page of Posts:','ela'),
        'paged_post_prev', $settings['paged_post_prev'],
        __('The text written as the link to the previous page.','ela'),
        true);
    ?>
    </tbody></table>