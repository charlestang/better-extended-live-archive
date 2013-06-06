<?php $this->renderPartial('common/nav-tab');?>
<h3 class="title"><?php _e('How to cut?'); ?></h3>
<p><?php _e('Control the cut off of ELA.'); ?></p>
<table class="form-table">
    <tbody>
        <?php
        better_ela_helper_txtbox(
                __('Max Entry Title Length:', 'ela'), 'truncate_title_length', $settings['truncate_title_length'], __('Length at which to truncate title of entries. Set to <strong>0</strong> to leave the titles not truncated.', 'ela'));
        better_ela_helper_txtbox(
                __('Max Cat. Title Length:', 'ela'), 'truncate_cat_length', $settings['truncate_cat_length'], __('Length at which to truncate name of categories. Set to <strong>0</strong> to leave the category names not truncated', 'ela'));
        better_ela_helper_txtbox(
                __('Truncated Text:', 'ela'), 'truncate_title_text', $settings['truncate_title_text'], __('The text that will be written after the entries titles and the categories names that have been truncated. &#8230; (<strong>&amp;#8230;</strong>) is a common example.', 'ela'));
        better_ela_helper_chkbox(
                __('Truncate at space:', 'ela'), 'truncate_title_at_space', $settings['truncate_title_at_space'], __('Sets whether at title should be truncated at the last space before the length to be truncated to, or if words should be truncated mid-senten...', 'ela'));
        better_ela_helper_chkbox(
                __('Abbreviate month names:', 'ela'), 'abbreviated_month', $settings['abbreviated_month'], __('Sets whether the month names will be abbreviated to three letters.', 'ela'));
        ?>
        <tr>
            <th scope="row"><?php _e('Displayed tags:', 'ela'); ?></th>
            <td>
                <fieldset><legend class="screen-reader-text"><span><?php _e('Displayed tags:', 'ela'); ?></span></legend>
                    <label title="tag_soup_cut0"><input type="radio" value="0" name="tag_soup_cut" id="tag_soup_cut0" <?php checked('0', $settings['tag_soup_cut']); ?> /> <?php _e('Show all tags.', 'ela'); ?></label><br>
                    <label title="tag_soup_cut1"><input type="radio" value="1" name="tag_soup_cut" id="tag_soup_cut1" <?php checked('1', $settings['tag_soup_cut']); ?> /> <?php _e('Show the first <strong>X</strong> most-used tags.', 'ela'); ?></label><br>
                    <label title="tag_soup_cut2"><input type="radio" value="2" name="tag_soup_cut" id="tag_soup_cut2" <?php checked('2', $settings['tag_soup_cut']); ?> /> <?php _e('Show tags with more than <strong>X</strong> posts.', 'ela'); ?></label><br>
                </fieldset>
            </td>
        </tr>
<?php
better_ela_helper_txtbox(
        __('The X in the selected above description:', 'ela'), 'tag_soup_X', $settings['tag_soup_X'], __('Sets depending on the selection made above the number of post per tag needed to display the tag or the number of most-used tags to display.', 'ela'));
?>
    </tbody>
</table>