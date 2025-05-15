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

class Controller extends Presenter
{
    /**
     * The name of the requested game.
     *
     * @var string
     */
    private $requestedGame;

    /**
     * The requested ply.
     *
     * @var int
     */
    private $requestedPly;

    /**
     * Whether the board is flipped.
     *
     * @var bool
     */
    private $isFlipped;

    /**
     * The requested action.
     *
     * @var string
     */
    private $requestedAction;

    /**
     * Whether we're responding to an Ajax request.
     *
     * @var bool
     */
    private $isAjaxRequest;

    /**
     * Initializes a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->requestedGame = isset($_REQUEST['chess_game'])
            ? $_REQUEST['chess_game'] : "";
        if (!Game::isValidName($this->requestedGame)) {
            $this->requestedGame = "";
        }
        $this->requestedPly = isset($_REQUEST['chess_ply'])
            ? (int) $_REQUEST['chess_ply'] : 0;
        $this->isFlipped = isset($_REQUEST['chess_flipped'])
            ? (bool) $_REQUEST['chess_flipped'] : false;
        $this->requestedAction = isset($_REQUEST['chess_action'])
            ? $_REQUEST['chess_action'] : "";
        $actions = array('start', 'previous', 'next', 'end', 'flip');
        if (!in_array($this->requestedAction, $actions)) {
            $this->requestedAction = "";
        }
        $this->isAjaxRequest = isset($_REQUEST['chess_ajax']);
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
        $this->emitScript();
        if (XH_ADM // @phpstan-ignore-line
            && XH_wantsPluginAdministration("chess")
        ) {
            $this->handleAdministration();
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
    private function emitScript()
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
    private function handleAdministration()
    {
        global $admin, $o;

        $o .= print_plugin_admin('on');
        switch ($admin) {
            case '':
                $infoView = InfoView::make();
                $o .= $infoView->render();
                break;
            case 'plugin_main':
                $this->handleImport();
                break;
            default:
                $o .= plugin_admin_common();
        }
    }

    /**
     * Creates and executes an import command.
     *
     * @return void
     *
     * @global array  The paths of system files and folders.
     */
    private function handleImport()
    {
        global $pth;

        $importer = new PgnImporter(
            $pth['folder']['plugins'] . 'chess/data/'
        );
        $importCommand = ImportCommand::make($importer);
        $importCommand->execute();
    }

    /**
     * Returns the game view.
     *
     * @param string $basename A basename of a data file.
     *
     * @return string|void
     */
    public function chess($basename)
    {
        if ($this->isAjaxRequest && $this->requestedGame != $basename) {
            return;
        }
        if (!Game::isValidName($basename)) {
            return $this->renderFailure('invalid_name', $basename);
        }
        $game = Game::load($basename);
        if (!$game) {
            return $this->renderFailure('load_error', $basename);
        }
        $gameView = GameView::make($game, $this->getPly($game), $this->isFlipped());
        if ($this->isAjaxRequest) {
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
     * @param Game $game A game.
     *
     * @return int
     */
    private function getPly(Game $game)
    {
        $result = $this->requestedPly;
        switch ($this->requestedAction) {
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
    private function isFlipped()
    {
        $result = $this->isFlipped;
        if ($this->requestedAction == 'flip') {
            $result = !$result;
        }
        return $result;
    }
}
