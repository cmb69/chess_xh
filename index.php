<?php

/**
 * main ;)
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Chess
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2014 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Chess_XH
 */

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * The domain layer.
 */
require_once $pth['folder']['plugin_classes'] . 'Domain.php';

/**
 * The service layer.
 */
require_once $pth['folder']['plugin_classes'] . 'Service.php';

/**
 * The presentation layer.
 */
require_once $pth['folder']['plugin_classes'] . 'Presentation.php';

/**
 * The plugin version.
 */
define('CHESS_VERSION', '@CHESS_VERSION@');

if (!function_exists('XH_exit')) {
    /**
     * Exits the script.
     *
     * Fallback for CMSimple_XH < 1.6.2.
     *
     * @return void
     */
    function XH_exit()
    {
        exit;
    }
}

/**
 * Renders a game view.
 *
 * @param string $basename A basename of a data file.
 *
 * @return string (X)HTML.
 *
 * @global $_Chess_controller The chess controller.
 */
function chess($basename)
{
    global $_Chess_controller;

    return $_Chess_controller->chess($basename);
}

/**
 * The plugin controller.
 */
$_Chess_controller = new Chess_Controller();
$_Chess_controller->dispatch();

?>
