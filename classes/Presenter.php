<?php

/**
 * The presentation layer.
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

namespace Chess;

abstract class Presenter
{
    /** @var array */
    protected $lang;

    public function __construct()
    {
        global $plugin_tx;

        $this->lang = $plugin_tx['chess'];
    }

    protected function renderFailure(string $key): string
    {
        $args = func_get_args();
        array_shift($args);
        $message = vsprintf($this->lang['message_' . $key], $args);
        if (function_exists('XH_message')) {
            return XH_message('fail', $message);
        } else {
            return '<p class="cmsimplecore_warning">' . $message . '<p>';
        }
    }
}
