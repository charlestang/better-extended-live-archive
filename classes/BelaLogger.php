<?php

/**
 * Logger for the debug use
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class BelaLogger {

    public static function log() {
        if (!defined('BELA_DEBUG') || !BELA_DEBUG) {
            return;
        }

        $log = '[' . date('Y-m-d H:i:s') . ']';
        $backtrace = debug_backtrace();

        $callpoint = $backtrace[0];
        $callinfo = $backtrace[1];

        if (isset($callpoint['file'])) {
            $log .= '[file:' . str_replace(WP_PLUGIN_DIR, '', $callpoint['file']) . ']';
        }

        if (isset($callpoint['line'])) {
            $log .= '[line:' . $callpoint['line'] . ']';
        }

        if (isset($callinfo['class'])) {
            if (defined('BELA_DEBUG_CLASS')) {
                $classes = explode(',', BELA_DEBUG_CLASS);
                if (!in_array($callinfo['class'], $classes)) {
                    return;
                }
            }
            $log .= '[func:' . $callinfo['class'] . $callinfo['type'] . $callinfo['function'] . '()]';
        } else {
            $log .= '[func:' . $callinfo['function'] . '()]';
        }

        $log .= "\n=====Messages=====\n";

        $num = func_num_args();
        $arg_arr = func_get_args();

        for ($i = 0; $i < $num; $i++) {
            if (is_string($arg_arr[$i])) {
                $log .= $arg_arr[$i] . "\n";
            } else {
                $log .= var_export($arg_arr[$i], true) . "\n";
            }
        }

        error_log($log, 3, WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'bela.log');
    }

}
