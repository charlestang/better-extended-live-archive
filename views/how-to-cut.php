<?php /* @var $options BelaOptions */ ?>
<?php $this->renderPartial('common/nav-tab'); ?>
<h3 class="title"><?php _e('How to cut?'); ?></h3>
<p><?php _e('Control the cut off of ELA.'); ?></p>
<form method="post" actions="<?php echo BelaAdmin::URL('howToCut');?>">
<table class="form-table">
    <tbody>
        <?php
        BelaHtml::optionTextInput($options, BelaKey::MAX_ENTRY_TITLE_LENGTH);
        BelaHtml::optionTextInput($options, BelaKey::MAX_CATEGORY_NAME_LENGTH);
        BelaHtml::optionTextInput($options, BelaKey::TRUNCATED_TEXT);
        BelaHtml::optionCheckbox($options, BelaKey::TRUNCATE_BREAK_WORD);
        BelaHtml::optionCheckbox($options, BelaKey::ABBREVIATE_MONTH_NAME);
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
        BelaHtml::optionTextInput($options, BelaKey::TAG_STRATEGY_THRESHOLD);
        ?>
    </tbody>
</table>
<p class="submit">
    <input type="submit" value="<?php _e("Save Changes", 'bela');?>" class="button button-primary" id="submit" name="submit">
</p>
</form>