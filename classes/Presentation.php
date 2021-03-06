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

/**
 * The abstract base class for all presentation classes.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
abstract class Chess_Presenter
{
    /**
     * The localization.
     *
     * @var array
     */
    protected $lang;

    /**
     * Initializes a new instance.
     *
     * @return void
     *
     * @global array The localization of the plugins.
     */
    public function __construct()
    {
        global $plugin_tx;

        $this->lang = $plugin_tx['chess'];
    }

    /**
     * Returns a failure message.
     *
     * @param string $key A message key.
     *
     * @return string (X)HTML.
     */
    protected function renderFailure($key)
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

/**
 * The controllers.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class Chess_Controller extends Chess_Presenter
{
    /**
     * The name of the requested game.
     *
     * @var string
     */
    private $_requestedGame;

    /**
     * The requested ply.
     *
     * @var int
     */
    private $_requestedPly;

    /**
     * Whether the board is flipped.
     *
     * @var bool
     */
    private $_isFlipped;

    /**
     * The requested action.
     *
     * @var string
     */
    private $_requestedAction;

    /**
     * Whether we're responding to an Ajax request.
     *
     * @var bool
     */
    private $_isAjaxRequest;

    /**
     * Initializes a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_requestedGame = isset($_REQUEST['chess_game'])
            ? stsl($_REQUEST['chess_game']) : false;
        if (!Chess_Game::isValidName($this->_requestedGame)) {
            $this->_requestedGame = false;
        }
        $this->_requestedPly = isset($_REQUEST['chess_ply'])
            ? (int) stsl($_REQUEST['chess_ply']) : 0;
        $this->_isFlipped = isset($_REQUEST['chess_flipped'])
            ? (bool) $_REQUEST['chess_flipped'] : false;
        $this->_requestedAction = isset($_REQUEST['chess_action'])
            ? stsl($_REQUEST['chess_action']) : false;
        $actions = array('start', 'previous', 'next', 'end', 'flip');
        if (!in_array($this->_requestedAction, $actions)) {
            $this->_requestedAction = false;
        }
        $this->_isAjaxRequest = isset($_REQUEST['chess_ajax']);
    }

    /**
     * Dispatch according to the request.
     *
     * @return void
     *
     * @global string Whether the wrapper administration is requested.
     */
    public function dispatch()
    {
        global $chess;

        $this->_emitScript();
        if (XH_ADM && isset($chess) && $chess == 'true') {
            $this->_handleAdministration();
        }
    }

    /**
     * Emits the script element.
     *
     * @return void
     *
     * @global array  The paths of system files and folders.
     * @global string The (X)HTML to insert at the bottom of the body.
     * @global string The (X)HTML to insert in the head.
     */
    private function _emitScript()
    {
        global $pth, $bjs, $hjs;

        $script = '<script type="text/javascript" src="'
            . $pth['folder']['plugins'] . 'chess/chess.js"></script>';
        if (isset($bjs)) {
            $bjs .= $script;
        } else {
            $hjs .= $script;
        }
    }

    /**
     * Handles the administration.
     *
     * @return void
     *
     * @global string The value of the <var>admin</var> GP parameter.
     * @global string The value of the <var>action</var> GP parameter.
     * @global string The HTML of the contents area.
     */
    private function _handleAdministration()
    {
        global $admin, $action, $o;

        $o .= print_plugin_admin('on');
        switch ($admin) {
        case '':
            $infoView = Chess_InfoView::make();
            $o .= $infoView->render();
            break;
        case 'plugin_main':
            $this->_handleImport();
            break;
        default:
            $o .= plugin_admin_common($action, $admin, 'chess');
        }
    }

    /**
     * Creates and executes an import command.
     *
     * @return void
     *
     * @global array  The paths of system files and folders.
     */
    private function _handleImport()
    {
        global $pth;

        $importer = new Chess_PgnImporter(
            $pth['folder']['plugins'] . 'chess/data/'
        );
        $importCommand = Chess_ImportCommand::make($importer);
        $importCommand->execute();
    }

    /**
     * Returns the game view.
     *
     * @param string $basename A basename of a data file.
     *
     * @return string (X)HTML.
     */
    public function chess($basename)
    {
        if ($this->_isAjaxRequest && $this->_requestedGame != $basename) {
            return;
        }
        if (!Chess_Game::isValidName($basename)) {
            return $this->renderFailure('invalid_name', $basename);
        }
        $game = Chess_Game::load($basename);
        if (!$game) {
            return $this->renderFailure('load_error', $basename);
        }
        $gameView = Chess_GameView::make(
            $game, $this->_getPly($game), $this->_isFlipped()
        );
        if ($this->_isAjaxRequest) {
            header('Content-Type:text/html; charset=UTF-8');
            echo $gameView->render();
            XH_exit();
        } else {
            return $gameView->render();
        }
    }

    /**
     * Returns the requested ply.
     *
     * @param Chess_Game $game A game.
     *
     * @return int
     */
    private function _getPly(Chess_Game $game)
    {
        $result = $this->_requestedPly;
        switch ($this->_requestedAction) {
        case 'start':
            $result = 0;
            break;
        case 'next':
            $result = min($result + 1, $game->getPlyCount());
            break;
        case 'previous':
            $result = max($result - 1, 0);
            break;
        case 'end':
            $result = $game->getPlyCount();
        }
        return $result;
    }

    /**
     * Returns whether the board shall be flipped.
     *
     * @return bool
     */
    private function _isFlipped()
    {
        $result = $this->_isFlipped;
        if ($this->_requestedAction == 'flip') {
            $result = !$result;
        }
        return $result;
    }
}

/**
 * The game views.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class Chess_GameView
{
    /**
     * The game.
     *
     * @var Chess_Game
     */
    private $_game;

    /**
     * The current ply number.
     *
     * @var int
     */
    private $_ply;

    /**
     * The current position.
     *
     * @var Chess_Position
     */
    private $_position;

    /**
     * Whether the board is flipped (i.e. the white side is at the top).
     *
     * @var bool
     */
    private $_flipped;

    /**
     * Makes a new game view.
     *
     * @param Chess_Game $game    A game.
     * @param int        $ply     A ply number.
     * @param bool       $flipped Whether the board is flipped.
     *
     * @return Chess_GameView
     */
    public static function make(Chess_Game $game, $ply = 0, $flipped = false)
    {
        return new self($game, $ply, $flipped);
    }

    /**
     * Initializes a new instance.
     *
     * @param Chess_Game $game    A game.
     * @param int        $ply     A ply number.
     * @param bool       $flipped Whether the board is flipped.
     *
     * @return void
     */
    public function __construct(Chess_Game $game, $ply = 0, $flipped = false)
    {
        $this->_game = $game;
        $this->_ply = (int) $ply;
        $this->_position = $game->getPosition(
            min($this->_ply, $this->_game->getPlyCount())
        );
        $this->_flipped = (bool) $flipped;
    }

    /**
     * Renders the game view.
     *
     * @return string (X)HTML.
     */
    public function render()
    {
        return '<div id="chess_view_' . $this->_game->getName()
            . '" class="chess_view">'
            . $this->_renderBoard() . $this->_renderControlPanel()
            . '</div>';
    }

    /**
     * Renders the board.
     *
     * @return string (X)HTML.
     */
    private function _renderBoard()
    {
        $result = '<table class="chess_board">';
        foreach ($this->_getRanks() as $rank) {
            $result .= $this->_renderRank($rank);
        }
        $result .= '</table>';
        return $result;
    }

    /**
     * Returns an array of ranks.
     *
     * @return array
     */
    private function _getRanks()
    {
        $ranks = range(8, 1, -1);
        if ($this->_flipped) {
            $ranks = array_reverse($ranks);
        }
        return $ranks;
    }

    /**
     * Renders a certain rank as table row.
     *
     * @param int $rank A rank.
     *
     * @return string (X)HTML.
     */
    private function _renderRank($rank)
    {
        $result = '<tr>';
        foreach ($this->_getFiles() as $file) {
            $result .= $this->_renderSquare($file, $rank);
        }
        $result .= '</tr>';
        return $result;
    }

    /**
     * Returns an array of files.
     *
     * @return array
     */
    private function _getFiles()
    {
        $files = array_map('chr', range(97, 104));
        if ($this->_flipped) {
            $files = array_reverse($files);
        }
        return $files;
    }

    /**
     * Renders a certain square.
     *
     * @param string $file A file.
     * @param string $rank A rank.
     *
     * @return string (X)HTML.
     */
    private function _renderSquare($file, $rank)
    {
        $square = "$file$rank";
        $class = ($rank + ord($file)) % 2 ? 'chess_light' : 'chess_dark';
        $result = '<td class="' . $class . '">';
        $move = $this->_game->getMove($this->_ply - 1);
        $moved = isset($move) && $move->isSourceOrDestination($square);
        if ($this->_position->hasPieceOn($square)) {
            $result .= $this->_renderPiece(
                $this->_position->getPieceOn($square), $moved
            );
        } else {
            if ($moved) {
                $result .= '<span class="chess_move">&nbsp;</span>';
            } else {
                $result .= '&nbsp;';
            }
        }
        $result .= '</td>';
        return $result;
    }

    /**
     * Renders a piece.
     *
     * @param string $piece A piece.
     * @param bool   $moved Whether the piece is moved.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     */
    private function _renderPiece($piece, $moved)
    {
        global $pth;

        $src = $pth['folder']['plugins'] . 'chess/images/' . $piece . '.png';
        $class = $moved ? 'class="chess_move"' : '';
        return tag('img ' . $class . ' src="' . $src . '" alt="' . $piece . '"');
    }

    /**
     * Renders the control panel.
     *
     * @return string (X)HTML.
     *
     * @global string The script name.
     * @global string The selected URL.
     */
    private function _renderControlPanel()
    {
        global $sn, $su;

        return '<form class="chess_control_panel" action="' . $sn
            . '#chess_view_' . $this->_game->getName() . '" method="'
            . $this->_getMethod() . '">'
            . $this->_renderHiddenInput('selected', $su)
            . $this->_renderHiddenInput('chess_game', $this->_game->getName())
            . $this->_renderHiddenInput('chess_flipped', (int) $this->_flipped)
            . $this->_renderButton('goto')
            . $this->_renderButton('start') . $this->_renderButton('previous')
            . $this->_renderPlyInput($this->_ply)
            . $this->_renderButton('next') . $this->_renderButton('end')
            . $this->_renderButton('flip')
            . '</form>';
    }

    /**
     * Returns the appropriate form method according to the CMSimple version.
     *
     * @return bool
     */
    private function _getMethod()
    {
        if (strpos(CMSIMPLE_XH_VERSION, 'CMSimple_XH') === 0
            && version_compare(CMSIMPLE_XH_VERSION, 'CMSimple_XH 1.6', 'ge')
        ) {
            return 'get';
        } else {
            return 'post';
        }
    }

    /**
     * Renders the ply input field.
     *
     * @param string $value A ply.
     *
     * @return string (X)HTML.
     */
    private function _renderPlyInput($value)
    {
        return tag(
            'input type="text" name="chess_ply" value="' . $value . '"'
        );
    }

    /**
     * Renders a hidden input field.
     *
     * @param string $name  A name attribute value.
     * @param string $value A value attribute value.
     *
     * @return string (X)HTML.
     */
    private function _renderHiddenInput($name, $value)
    {
        return tag(
            'input type="hidden" name="' . $name . '" value="' . $value . '"'
        );
    }

    /**
     * Renders a button.
     *
     * @param string $which Which button to render.
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the plugins.
     */
    private function _renderButton($which)
    {
        global $plugin_tx;

        switch ($which) {
        case 'start':
            $value = 'start';
            $disabled = ($this->_ply == 0);
            break;
        case 'previous':
            $value = 'previous';
            $disabled = ($this->_ply == 0);
            break;
        case 'goto':
            $value = 'goto';
            $disabled = false;
            break;
        case 'next':
            $value = 'next';
            $disabled = ($this->_ply == $this->_game->getPlyCount());
            break;
        case 'end';
            $value = 'end';
            $disabled = ($this->_ply == $this->_game->getPlyCount());
            break;
        case 'flip':
            $value = 'flip';
            $disabled = false;
            break;
        }
        return '<button type="submit" name="chess_action" value="' . $value . '"'
            . ($disabled ? ' disabled="disabled"' : '') . '>'
            . $plugin_tx['chess']["label_$which"] . '</button>';
    }
}

/**
 * The info views.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class Chess_InfoView
{
    /**
     * Returns a new self instance.
     *
     * @return Chess_InfoView.
     */
    public static function make()
    {
        return new Chess_InfoView();
    }

    /**
     * Renders the view.
     *
     * @return string (X)HTML.
     */
    public function render()
    {
        return '<h1>Chess</h1>'
            . $this->_renderIcon()
            . '<p>Version: ' . CHESS_VERSION . '</p>'
            . $this->_renderCopyright() . $this->_renderLicense();
    }

    /**
     * Renders the plugin icon.
     *
     * @return (X)HTML.
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the plugins.
     */
    private function _renderIcon()
    {
        global $pth, $plugin_tx;

        return tag(
            'img src="' . $pth['folder']['plugins'] . 'chess/chess.png"'
            . ' class="chess_icon" alt="' . $plugin_tx['chess']['alt_icon']
            . '"'
        );
    }

    /**
     * Renders the copyright info.
     *
     * @return (X)HTML.
     */
    private function _renderCopyright()
    {
        return <<<EOT
<p>Copyright &copy; 2014
    <a href="http://3-magi.net/" target="_blank">Christoph M. Becker</a>
</p>
EOT;
    }

    /**
     * Renders the license info.
     *
     * @return (X)HTML.
     */
    private function _renderLicense()
    {
        return <<<EOT
<p class="chess_license">This program is free software: you can
redistribute it and/or modify it under the terms of the GNU General Public
License as published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.</p>
<p class="chess_license">This program is distributed in the hope that it
will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
Public License for more details.</p>
<p class="chess_license">You should have received a copy of the GNU
General Public License along with this program. If not, see <a
href="http://www.gnu.org/licenses/" target="_blank">http://www.gnu.org/licenses/</a>.
</p>
EOT;
    }
}

/**
 * The import commands.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class Chess_ImportCommand extends Chess_Presenter
{
    /**
     * The PGN importer.
     *
     * @var Chess_PgnImporter.
     */
    private $_importer;

    /**
     * Returns a new self instance.
     *
     * @param Chess_PgnImporter $importer A PGN importer.
     *
     * @return Chess_ImportCommand
     */
    public static function make(Chess_PgnImporter $importer)
    {
        return new self($importer);
    }

    /**
     * Initializes a new instance.
     *
     * @param Chess_PgnImporter $importer A PGN importer.
     *
     * @return void
     */
    public function __construct(Chess_PgnImporter $importer)
    {
        parent::__construct();
        $this->_importer = $importer;
    }

    /**
     * Executes the command.
     *
     * @return void
     *
     * @global string            The value of the <var>action</var> GP parameter.
     * @global string            The HTML of the contents area.
     * @global XH_CSRFProtection The CSRF protector.
     *
     * @todo Add success message.
     */
    public function execute()
    {
        global $action, $o, $_XH_csrfProtection;

        if ($action == 'import') {
            if (isset($_XH_csrfProtection)) {
                $_XH_csrfProtection->check();
            }
            $game = stsl($_POST['chess_game']);
            if (Chess_Game::isValidName($game)) {
                $this->_importer->import($game);
            } else {
                $o .= $this->renderFailure('invalid_name', $game);
            }
        }
        $view = Chess_ImportView::make($this->_importer);
        $o .= $view->render();
    }
}

/**
 * The import views.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class Chess_ImportView
{
    /**
     * The PGN importer.
     *
     * @var Chess_PgnImporter
     */
    private $_importer;

    /**
     * Returns a new self instance.
     *
     * @param Chess_PgnImporter $importer A PGN importer.
     *
     * @return Chess_ImportView
     */
    public static function make(Chess_PgnImporter $importer)
    {
        return new self($importer);
    }

    /**
     * Initializes a new instance.
     *
     * @param Chess_PgnImporter $importer A PGN importer.
     *
     * @return void
     */
    public function __construct(Chess_PgnImporter $importer)
    {
        $this->_importer = $importer;
    }

    /**
     * Renders the view.
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the plugins.
     */
    public function render()
    {
        global $plugin_tx;

        return '<h1>Chess &ndash; ' . $plugin_tx['chess']['menu_main'] . '</h1>'
            . $this->_renderForm();
    }

    /**
     * Renders the form.
     *
     * @return string (X)HTML.
     *
     * @global string            The script name.
     * @global XH_CSRFProtection The CSRF protector.
     */
    private function _renderForm()
    {
        global $sn, $_XH_csrfProtection;

        $result = '<form class="chess_import_form" action="' . $sn
            . '?chess" method="post">';
        if (isset($_XH_csrfProtection)) {
            $result .= $_XH_csrfProtection->tokenInput();
        }
        $result .= tag('input type="hidden" name="admin" value="plugin_main"')
            . tag('input type="hidden" name="action" value="import"')
            . $this->_renderList()
            . '</form>';
        return $result;
    }

    /**
     * Renders the list.
     *
     * @return string (X)HTML.
     */
    private function _renderList()
    {
        $result = '<ul>';
        foreach ($this->_importer->findAll() as $name) {
            $result .= $this->_renderListItem($name);
        }
        $result .= '</ul>';
        return $result;
    }

    /**
     * Renders a list item.
     *
     * @param string $name A basename.
     *
     * @return string (X)HTML
     *
     * @global array The localization of the plugins.
     */
    private function _renderListItem($name)
    {
        global $plugin_tx;

        return '<li>'
            . $name
            . '<button name="chess_game" value="' . $name . '">'
            . $plugin_tx['chess']['label_import'] . '</button>'
            . '</li>';
    }
}

?>
