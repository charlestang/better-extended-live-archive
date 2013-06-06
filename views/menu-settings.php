<?php $this->render('common/nav-tab'); ?>
<h3 class="title"><?php _e('What about the menu?'); ?></h3>
<p><?php _e('Customize the menu of ELA.'); ?></p>
<table class="form-table"><tbody>
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
        better_ela_helper_txtbox(
                __('Chronological Tab Text:', 'ela'), 'menu_month', $settings['menu_month'], __('The text written in the chronological tab.', 'ela'), true);
        better_ela_helper_txtbox(
                __('By Category Tab Text:', 'ela'), 'menu_cat', $settings['menu_cat'], __('The text written in the categories tab.', 'ela'), true);
        better_ela_helper_txtbox(
                __('By Tag Tab Text:', 'ela'), 'menu_tag', $settings['menu_tag'], __('The text written in the tags tab.', 'ela'), true);
        better_ela_helper_txtbox(
                __('Before Child Text:', 'ela'), 'before_child', $settings['before_child'], __('The text written before each category which is a child of another. This is recursive.', 'ela'), true);
        better_ela_helper_txtbox(
                __('After Child Text:', 'ela'), 'after_child', $settings['after_child'], __('The text that after each category which is a child of another. This is recursive.', 'ela'), true);
        better_ela_helper_txtbox(
                __('Loading Content:', 'ela'), 'loading_content', $settings['loading_content'], __('The text displayed when the data are being fetched from the server (basically when stuff is loading). Can contain HTML.', 'ela'), true);
        better_ela_helper_txtbox(
                __('Idle Content:', 'ela'), 'idle_content', $settings['idle_content'], __('The text displayed when no data are being fetched from the server (basically when stuff is not loading). Can contain HTML.', 'ela'), true);
        ?>
    </tbody></table>