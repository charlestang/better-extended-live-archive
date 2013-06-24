<?php $this->renderPartial('common/nav-tab'); ?>
<p><?php _e('The layout of the posts when using a paged list instead of complete list .', 'bela'); ?></p>
<form method="post" actions="<?php echo BelaAdmin::URL('pagination');?>">
<table class="form-table paged-posts-section">
    <tbody>
        <?php
        BelaHtml::optionRadioGroup($options, BelaKey::STYLE_NAME, $styles);
        ?>
    </tbody>
</table>
<p class="submit">
    <input type="submit" value="<?php _e("Save Changes", 'bela');?>" class="button button-primary" id="submit" name="submit">
</p>
</form>