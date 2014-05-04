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
