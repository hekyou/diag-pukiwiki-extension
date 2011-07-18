<?php
/**
 * diag.inc.php Pukiwiki Plugin
 *
 * Copyright (c) 2011 hekyou.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2011 hekyou.
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link      http://d.hatena.ne.jp/hekyou/20110717/p1
 * @author    hekyou <hekyolabs+pukiwiki@gmail.com>
 * @version   0.1.0  2011-07-15  hekyou : Create New.
 */
/**
 * [usage]
 * #diag(block|nw|seq|act){{
 * diagram {
 *   ...
 * }
 * }};
 */

defined('PLUGIN_DIAG_IMAGE_DIR') or define('PLUGIN_DIAG_IMAGE_DIR', '/tmp/');

defined('PLUGIN_DIAG_BLOCKDIAG_PATH') or define('PLUGIN_DIAG_BLOCKDIAG_PATH', '/usr/local/bin/blockdiag');
defined('PLUGIN_DIAG_NWDIAG_PATH')    or define('PLUGIN_DIAG_NWDIAG_PATH', '/usr/local/bin/nwdiag');
defined('PLUGIN_DIAG_SEQDIAG_PATH')   or define('PLUGIN_DIAG_SEQDIAG_PATH', '/usr/local/bin/seqdiag');
defined('PLUGIN_DIAG_ACTDIAG_PATH')   or define('PLUGIN_DIAG_ACTDIAG_PATH', '/usr/local/bin/actdiag');

defined('PLUGIN_DIAG_IMAGE_TYPE') or define('PLUGIN_DIAG_IMAGE_TYPE', 'png');
defined('PLUGIN_DIAG_FONT_PATH')  or define('PLUGIN_DIAG_FONT_PATH', ''); // --font=/Library/Fonts/Osaka.ttf
defined('PLUGIN_DIAG_ANTIALIAS')  or define('PLUGIN_DIAG_ANTIALIAS', '--antialias'); // or ''

function plugin_diag_init() {
    return mk_dir();
}

function plugin_diag_convert() {
    global $vars;

    if (!mk_dir()) {
        return 'Error(C): Fail mkdir()';
    }
    if (func_num_args() != 2) {
        return 'Error(C): The number of arguments is illegal.';
    }
    $args = func_get_args();
    $type = $args[0];
    $code = end($args);
    if ($type !== 'block' && $type !== 'nw' && $type !== 'seq' && $type !== 'act') {
        return 'Error(C): The value of the argument is illegal.';
    }

    return '<div align="center"><img src="'.get_script_uri().'?plugin=diag&type='.$type.'&code='.urlencode($code).'" /></div>';
}

function plugin_diag_inline() {
    return 'Error(I): Not supports.';
}

function plugin_diag_action() {
    global $vars;

    if (!isset($vars['type']) || !isset($vars['code']) || !$vars['code']) {
        return 'Error(A): The value of the arguments is illegal.';
    }

    $random_len = 50;
    $list = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_';

    $random = '';
    $list_len = strlen($list) - 1;
    mt_srand();
    while ($random_len > 0) {
        $random .= $list{mt_rand(0, $list_len)};
        $random_len--;
    }

    $dist_temp_name = 'dist_'.$random;
    $src_temp_name = 'src_'.$random;

    try {
        $fp = fopen(PLUGIN_DIAG_IMAGE_DIR.$src_temp_name, 'w');
        fwrite($fp, urldecode($vars['code']));
        fclose($fp);
    }
    catch (Exception $e) {
        return 'Error(A): '.$e->getMessage();
    }

    if ($vars['type'] === 'block') {
        $command = PLUGIN_DIAG_BLOCKDIAG_PATH;
    }
    else if ($vars['type'] === 'nw') {
        $command = PLUGIN_DIAG_NWDIAG_PATH;
    }
    else if ($vars['type'] === 'seq') {
        $command = PLUGIN_DIAG_SEQDIAG_PATH;
    }
    else if ($vars['type'] === 'act') {
        $command = PLUGIN_DIAG_ACTDIAG_PATH;
    }
    else {
        return 'Error(A): The type of the argument is illegal.';
    }

    $command .= ' -T '.PLUGIN_DIAG_IMAGE_TYPE.' '.PLUGIN_DIAG_FONT_PATH.' '.PLUGIN_DIAG_ANTIALIAS.' -o '.PLUGIN_DIAG_IMAGE_DIR.$dist_temp_name.' '.PLUGIN_DIAG_IMAGE_DIR.$src_temp_name;
    `$command`;
    header('Content-type: image/'.PLUGIN_DIAG_IMAGE_TYPE);
    echo file_get_contents(PLUGIN_DIAG_IMAGE_DIR.$dist_temp_name);

    unlink(PLUGIN_DIAG_IMAGE_DIR.$src_temp_name);
    unlink(PLUGIN_DIAG_IMAGE_DIR.$dist_temp_name);

    return;
}

function mk_dir() {
    try {
        if (!is_dir(PLUGIN_DIAG_IMAGE_DIR)) {
            mkdir(PLUGIN_DIAG_IMAGE_DIR, 0755);
        }
    }
    catch (Exception $e) {
        return false;
    }
    return true;
}

