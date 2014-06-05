<?php /* @var $options BelaOptions */ ?>
<?php $this->renderPartial('common/nav-tab'); ?>
<p><?php _e('Customize the menu of ELA.'); ?></p>
<form method="post" actions="<?php echo BelaAdmin::URL('menuSettings'); ?>">
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row"><abel for="menu_order_tab0"><?php _e('Tab Order:', 'bela'); ?></label></th>
                <td>
                    <select name="<?php echo $options->getNameAttr(BelaKey::NAVIGATION_TABS_ORDER); ?>[]" multiple="multiple" class="multiselect">
                        <?php
                        $tabs = $options->get(BelaKey::NAVIGATION_TABS_ORDER);
                        $available = array(BelaKey::ORDER_KEY_BY_DATE, BelaKey::ORDER_KEY_BY_CATEGORY, BelaKey::ORDER_KEY_BY_TAGS);
                        $diff = array_diff($available, $tabs);
                        $available = array_merge($tabs, $diff);
                        foreach ($available as $tab) {
                            echo '<option value="', $tab, '" ', in_array($tab, $tabs) ? 'selected >' : '>';
                            echo $options->getLabel($tab);
                            echo '</option>';
                        }
                        ?>
                    </select>
                    <br/><span class="description"><?php _e('Press CTRL(COMMAND on MacOS) to select multiple option.', 'bela'); ?></span>
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
<script type="text/javascript">
    jQuery(function(){
        jQuery(".multiselect").multiselect();
    });
</script>