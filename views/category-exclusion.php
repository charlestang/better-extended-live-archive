<?php $this->renderPartial('common/nav-tab'); ?>
<h3 class="title"><?php _e('What categories to show?'); ?></h3>
<?php //var_dump($settings['excluded_categories']); ?>
<p><?php _e('Check the categories you want to show in the category tab.'); ?></p>
<table class="form-table"><tbody>
        <tr valign="top">
            <th scope="row"><?php _e('Select categories:', 'ela'); ?></th>
            <td><fieldset><legend class="screen-reader-text">
                        <span><?php _e('Select categories:', 'ela'); ?></span></legend>
                    <?php
                    global $wpdb;
                    $asides_table = array();
                    $asides_table = explode(',', $settings['excluded_categories']);
                    $query = "SELECT t.term_id AS `cat_ID`, t.name AS `cat_name`
                      FROM $wpdb->terms AS t
                      INNER JOIN {$wpdb->term_taxonomy} AS tt
                            ON (t.term_id = tt.term_id)
                      WHERE tt.taxonomy = 'category'
            ";
                    $asides_cats = $wpdb->get_results($query);
                    $asides_content = '';
                    $asides_select = '';
                    foreach ($asides_cats as $cat) {
                        $checked = in_array($cat->cat_ID, $asides_table) ? '' : 'checked="checked"';
                        $asides_select .= $cat->cat_ID . ',';
                        $asides_content .= '<label for="category-' . $cat->cat_ID . '">';
                        $asides_content .= '<input value="' . $cat->cat_ID . '" type="checkbox" name="excluded_categories[]" id="category-' . $cat->cat_ID . '" ' . $checked . '/>';
                        $asides_content .= $cat->cat_name . '</label><br/>';
                    }
                    echo $asides_content;
                    ?>
                </fieldset></td>
        </tr>
        <tr valign="top">
            <th scope="row">&nbsp;</th>
            <td>
                <input type="button" onclick="javascript:selectAllCategories('<?php echo $asides_select; ?>')" value="<?php _e('Select All Categories') ?>" />
                <input type="button" onclick="javascript:unselectAllCategories('<?php echo $asides_select; ?>')" value="<?php _e('Unselect All Categories') ?>" />
            </td>
        </tr>
    </tbody></table>