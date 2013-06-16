<?php

/**
 * Html helper
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class BelaHtml {

    /**
     * Generate a check box for options item.
     * @param BelaOptions $options
     * @param int $key
     */
    public static function optionCheckbox($options, $key) {
        $label = $options->getLabel($key);
        $id = $options->getNameAttr($key);
        $value = $options->get($key) ? 1 : 0;
        $description = $options->getDescription($key);
        self::adminCheckbox($label, $id, $value, $description);
    }

    /**
     * Generate a text box for options item.
     * @param BelaOptions $options
     * @param int $key
     * @param string $html
     */
    public static function optionTextInput($options, $key, $html = false) {
        $label = $options->getLabel($key);
        $id = $options->getNameAttr($key);
        $value = $options->get($key);
        $description = $options->getDescription($key);
        self::adminTextInput($label, $id, $value, $description, $html);
    }

    /**
     * Show a radio group on the admin page
     * @param BelaOptions $options
     * @param int $key
     * @param array $group
     */
    public static function optionRadioGroup($options, $key, $group) {
        $label = $options->getLabel($key);
        $name = $options->getNameAttr($key);
        $current = $options->get($key);
        ?>
        <tr>
            <th scope="row"><?php echo $label; ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text">
                        <span><?php echo $label; ?></span>
                    </legend>
                    <?php
                    foreach ($group as $value) {
                        self::adminRadio($name, $value, $current, $options->getLabel($value));
                    }
                    ?>
                </fieldset>
            </td>
        </tr>
        <?php
    }

    public static function adminCheckbox($caption, $id, $default, $description) {
        ?>
        <tr valign="top">
            <th scope="row"><?php echo $caption; ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text">
                        <span><?php echo $caption; ?></span>
                    </legend>
                    <label for="<?php echo $id; ?>">
                        <input type="hidden" 
                               value="0" 
                               name="<?php echo $id; ?>" />
                        <input type="checkbox" 
                               value="1" 
                               id="<?php echo $id; ?>" 
                               name="<?php echo $id; ?>" <?php checked('1', $default); ?> />
                               <?php echo $description; ?>
                    </label>
                </fieldset>
            </td>
        </tr>
        <?php
    }

    public static function adminTextInput($caption, $id, $default, $description, $html = false) {
        if ($html) {
            $default = htmlspecialchars(stripslashes($default));
        }
        ?>
        <tr valign="top">
            <th scope="row"><label for="<?php echo $id; ?>"><?php echo $caption; ?></label></th>
            <td>
                <input type="text" 
                       class="regular-text" 
                       style="width:12.5em;" 
                       value="<?php echo $default; ?>" 
                       id="<?php echo $id; ?>" 
                       name="<?php echo $id; ?>">
                <span class="description"><?php echo $description; ?></span>
            </td>
        </tr>
        <?php
    }

    public static function adminRadio($name, $value, $current, $label) {
        $id = 'radio-id-' . $name . '-' . $value;
        ?>
        <label title="<?php echo $id; ?>">
            <input type="radio" 
                   id="<?php echo $id; ?>"
                   name="<?php echo $name; ?>" 
                   value="<?php echo $value; ?>" 
                   <?php checked($value, $current); ?> /> 
                   <?php echo $label; ?>
        </label><br/>
        <?php
    }

    /**
     * Generate a <li> item 
     * @param string $content
     * @param array $options
     */
    public static function menuItem($content, $options) {
        $li = '<li';
        $class = array();
        if (isset($options['active'])) {
            if ($options['active']) {
                if (isset($options['activeClass'])) {
                    $class[] = explode(' ', $options['activeClass']);
                    unset($options['activeClass']);
                } else {
                    $class[] = 'active';
                }
            }
            unset($options['active']);
        }


        if (isset($options['class'])) {
            $class = array_merge($class, explode(' ', $options['class']));
            unset($options['class']);
        }

        $li .= ' class="' . implode(' ', $class) . '"';

        foreach ($options as $key => $val) {
            $li .= ' ';
            $li .= $key . '="' . $val . '"';
        }

        $li .= '>' . $content . '</li>';
        return $li;
    }

}
