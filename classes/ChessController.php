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
    /** @var Factory */
    private $factory;

    /** @var string */
    private $requestedGame;

    /** @var int */
    private $requestedPly;

    /** @var bool */
    private $isFlipped;

    /** @var string */
    private $requestedAction;

    /** @var bool */
    private $isAjaxRequest;

    public function __construct(Factory $factory)
    {
        parent::__construct();
        $this->factory = $factory;
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

    public function dispatch(): void
    {
        $this->emitScript();
        if (XH_ADM // @phpstan-ignore-line
            && XH_wantsPluginAdministration("chess")
        ) {
            $this->handleAdministration();
        }
    }

    private function emitScript(): void
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

    private function handleAdministration(): void
    {
        global $admin, $o;

        $o .= print_plugin_admin('on');
        switch ($admin) {
            case '':
                $infoView = $this->factory->makeInfoView();
                $o .= $infoView->render();
                break;
            case 'plugin_main':
                $this->handleImport();
                break;
            default:
                $o .= plugin_admin_common();
        }
    }

    private function handleImport(): void
    {
        global $pth;

        $importer = new PgnImporter(
            $pth['folder']['plugins'] . 'chess/data/'
        );
        $importCommand = $this->factory->makeImportCommand($importer, new ImportView($importer));
        $importCommand->execute();
    }

    /** @return string|void */
    public function chess(string $basename)
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
        $gameView = $this->factory->makeGameView($game, $this->getPly($game), $this->isFlipped());
        if ($this->isAjaxRequest) {
            header('Content-Type:text/html; charset=UTF-8');
            echo $gameView->render();
            XH_exit();
        } else {
            return $gameView->render();
        }
    }

    private function getPly(Game $game): int
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

    private function isFlipped(): bool
    {
        $result = $this->isFlipped;
        if ($this->requestedAction == 'flip') {
            $result = !$result;
        }
        return $result;
    }
}
