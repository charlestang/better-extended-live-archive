<?php $this->renderPartial('common/nav-tab'); ?>
<p><?php _e('Check the categories you want to show in the category tab.', 'bela'); ?></p>
<form method="post" actions="<?php echo BelaAdmin::URL('categoryExclusion'); ?>">
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row"><?php _e('Select categories:', 'bela'); ?></th>
                <td><fieldset><legend class="screen-reader-text">
                            <span><?php _e('Select categories:', 'bela'); ?></span></legend>
                        <?php
                        $categories = get_categories();
                        $excluded_cats = $options->get(BelaKey::EXCLUDE_CATEGORY_LIST);

                        $walker = new BelaAdminCategoryWalker();
                        echo $walker->walk($categories, 0, empty($excluded_cats) ? false : $excluded_cats, $options);
                        ?>
                    </fieldset></td>
            </tr>
        </tbody>
    </table>
    <p class="submit">
        <input type="submit" value="<?php _e("Save Changes", 'bela'); ?>" class="button button-primary" id="submit" name="submit">
    </p>
</form>