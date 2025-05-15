<?php

/**
 * Main ;)
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

use Chess\Controller;
use Chess\Factory;

if (!defined('CMSIMPLE_XH_VERSION')) {
    http_response_code(403);
    exit;
}

/**
 * The paths.
 *
 * @var array{folder:array<string,string>,file:array<string,string>} $pth
 */

/**
 * The domain layer.
 */
require_once $pth['folder']['plugin_classes'] . 'Game.php';
require_once $pth['folder']['plugin_classes'] . 'Position.php';
require_once $pth['folder']['plugin_classes'] . 'Move.php';

/**
 * The service layer.
 */
require_once $pth['folder']['plugin_classes'] . 'PgnImporter.php';

/**
 * The presentation layer.
 */
require_once $pth['folder']['plugin_classes'] . 'Presenter.php';
require_once $pth['folder']['plugin_classes'] . 'ChessController.php';
require_once $pth['folder']['plugin_classes'] . 'GameView.php';
require_once $pth['folder']['plugin_classes'] . 'InfoView.php';
require_once $pth['folder']['plugin_classes'] . 'ImportCommand.php';
require_once $pth['folder']['plugin_classes'] . 'ImportView.php';

/**
 * The plugin version.
 */
define('CHESS_VERSION', '1.0beta2');

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
$_Chess_controller = new Controller(new Factory());
$_Chess_controller->dispatch();
