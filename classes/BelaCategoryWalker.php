<?php

/**
 * Description of BelaCategoryWalker
 *
 * @author charles
 */
class BelaCategoryWalker extends Walker {

    var $tree_type = 'category-index-table';
    var $db_fields = array(
        'id'     => 'id',
        'parent' => 'parent'
    );

    function start_el(&$output, $object, $depth = 0, $args = array(), $current_object_id = 0) {
        $elargs = func_get_args();
        if ($elargs[5]) {
            $numStr = str_replace('%', $object->count, $elargs[6]);
        } else {
            $numStr = '';
        }
        $output .= BelaHtml::menuItem(str_repeat($elargs[7], $depth) . $object->name . $numStr, array(
                    'class'  => 'category-entry',
                    'menu'   => $elargs[4],
                    'cat'    => $object->id,
                    'active' => $object->id == $elargs[3],
        ));
    }

}

class BelaAdminCategoryWalker extends Walker {

    var $tree_type = 'category-exclusion-page';
    var $db_fields = array(
        'id'     => 'cat_ID',
        'parent' => 'category_parent',
    );

    function start_el(&$output, $object, $depth = 0, $args = array(), $current_object_id = 0) {
        $elargs = func_get_args();
        $excluded_cats = $elargs[3];
        $options = $elargs[4];
        $checked = '';
        if ($excluded_cats !== false && in_array($object->term_taxonomy_id, $excluded_cats, true)) {
            $checked = ' checked="checked" ';
        }
        $output .= '<label for="category-' . $object->term_id . '">'
                . '<input type="checkbox"' . $checked 
                . 'name="' . $options->getNameAttr(BelaKey::EXCLUDE_CATEGORY_LIST) .'[] ' 
                . 'id="category-' .$object->term_id . '" '
                . 'value="' . $object->term_taxonomy_id . '">'
                . str_repeat('--&nbsp;', $depth) . $object->name
                . '(' . $object->category_count . ')</label><br/>';
    }

}
