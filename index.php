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
 * Prevent direct access and usage from unsupported CMSimple_XH versions.
 */
if (!defined('CMSIMPLE_XH_VERSION')
    || strpos(CMSIMPLE_XH_VERSION, 'CMSimple_XH') !== 0
    || version_compare(CMSIMPLE_XH_VERSION, 'CMSimple_XH 1.5.4', 'lt')
) {
    header('HTTP/1.1 403 Forbidden');
    header('Content-Type: text/plain; charset=UTF-8');
    die(<<<EOT
Chess_XH detected an unsupported CMSimple_XH version.
Uninstall Chess_XH or upgrade to a supported CMSimple_XH version!
EOT
    );
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
