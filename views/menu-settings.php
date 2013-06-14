<?php $this->renderPartial('common/nav-tab'); ?>
<h3 class="title"><?php _e('What about the menu?'); ?></h3>
<p><?php _e('Customize the menu of ELA.'); ?></p>
<form method="post" actions="<?php echo BelaAdmin::URL('menuSettings'); ?>">
    <table class="form-table">
        <tbody>
            <?php
            if (!empty($settings['menu_order'])) {
                $menu_table = preg_split('/[\s,]+/', $settings['menu_order']);
            }
            ?>
            <tr valign="top">
                <th scope="row"><label for="menu_order_tab0"><?php _e('Tab Order:', 'ela'); ?></label></th>
                <td>
                    <?php for ($i = 0; $i < 3; $i++) : ?>
                        <select id="menu_order_tab<?php echo $i; ?>" name="menu_order[]">
                            <option value="none" <?php selected('none', $menu_table[$i]) ?>><?php _e('None', 'ela'); ?></option>
                            <option value="chrono" <?php selected('chrono', $menu_table[$i]) ?>><?php _e('By date', 'ela'); ?></option>
                            <option value="cats" <?php selected('cats', $menu_table[$i]) ?>><?php _e('By category', 'ela'); ?></option>
                            <option value="tags" <?php selected('tags', $menu_table[$i]) ?>><?php _e('By tag', 'ela'); ?></option></select>
                    <?php endfor; ?>
                    <br/><?php _e('The order of the tab to display.', 'ela'); ?>
                </td>
            </tr>
            <?php
            BelaHtml::optionTextInput($options, BelaKey::BY_DATE_TEXT, true);
            BelaHtml::optionTextInput($options, BelaKey::BY_CATEGORY_TEXT, true);
            BelaHtml::optionTextInput($options, BelaKey::BY_TAGS_TEXT, true);
            BelaHtml::optionTextInput($options, BelaKey::TEXT_BEFORE_CHILD_CATEGORY, true);
            BelaHtml::optionTextInput($options, BelaKey::TEXT_AFTER_CHILD_CATEGORY, true);
            BelaHtml::optionTextInput($options, BelaKey::TEXT_WHEN_CONTENT_LOADING, true);
            BelaHtml::optionTextInput($options, BelaKey::TEXT_WHEN_BLANK_CONTENT, true);
            ?>
        </tbody>
    </table>
    <p class="submit">
        <input type="submit" value="<?php _e("Save Changes", 'bela'); ?>" class="button button-primary" id="submit" name="submit">
    </p>
</form>