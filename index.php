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
use Chess\Dic;
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
 * The plugin version.
 */
define('CHESS_VERSION', '1.0beta2');

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
    return Dic::chessController()->chess($basename)();
}
