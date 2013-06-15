<?php

/**
 * Description of BelaAjax
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class BelaAjax {
    const BELA_AJAX_VAR = 'bela_ajax';
    /**
     * The BelaOptions object
     * @var BelaOptions 
     */
    public $options = null;

    /**
     * Constructor of the AJAX processor
     */
    public function __construct($options) {
        $this->options = $options;
    }

    /**
     * Ajax request entry point
     */
    public function entry() {

    }

}
