<?php

/**
 * Html helper
 *
 * @author charles
 */
class BelaHtml {

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
                        <input type="checkbox" 
                               value="<?php echo $default; ?>" 
                               id="<?php echo $id; ?>" 
                               name="<?php echo $id; ?>" <?php checked('1', $default); ?>>
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

}
