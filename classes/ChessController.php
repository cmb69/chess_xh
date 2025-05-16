<?php

/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Chess_XH.
 *
 * Chess_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Chess_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Chess_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Chess;

use Plib\Response;
use Plib\View;

class ChessController
{
    /** @var Factory */
    private $factory;

    /** @var View */
    private $view;

    public function __construct(Factory $factory, View $view)
    {
        $this->factory = $factory;
        $this->view = $view;
    }

    public function chess(string $basename): Response
    {
        $requestedGame = isset($_REQUEST['chess_game'])
            ? $_REQUEST['chess_game'] : "";
        if (!Game::isValidName($requestedGame)) {
            $requestedGame = "";
        }
        if (isset($_REQUEST['chess_ajax']) && $requestedGame != $basename) {
            return Response::create();
        }
        if (!Game::isValidName($basename)) {
            return Response::create($this->view->message("fail", "message_invalid_name", $basename));
        }
        $game = Game::load($basename);
        if (!$game) {
            return Response::create($this->view->message("fail", "message_load_error", $basename));
        }
        $this->emitScript();
        $gameView = $this->factory->makeGameView();
        if (isset($_REQUEST['chess_ajax'])) {
            return Response::create($gameView->render($game, $this->getPly($game), $this->isFlipped()))
                ->withContentType("Content-Type:text/html; charset=UTF-8");
        } else {
            return Response::create($gameView->render($game, $this->getPly($game), $this->isFlipped()));
        }
    }

    private function getPly(Game $game): int
    {
        $result = isset($_REQUEST['chess_ply'])
            ? (int) $_REQUEST['chess_ply'] : 0;
        switch ($this->requestedAction()) {
            case 'start':
                $result = 0;
                break;
            case 'next':
                $result = $result + 1;
                break;
            case 'previous':
                $result = $result - 1;
                break;
            case 'end':
                $result = $game->getPlyCount();
        }
        return max(0, min($game->getPlyCount(), $result));
    }

    private function isFlipped(): bool
    {
        $result = isset($_REQUEST['chess_flipped'])
            ? (bool) $_REQUEST['chess_flipped'] : false;
        if ($this->requestedAction() == 'flip') {
            $result = !$result;
        }
        return $result;
    }

    private function requestedAction(): string
    {
        $res = isset($_REQUEST['chess_action'])
            ? $_REQUEST['chess_action'] : "";
        $actions = array('start', 'previous', 'next', 'end', 'flip');
        if (!in_array($res, $actions)) {
            $res = "";
        }
        return $res;
    }

    private function emitScript(): void
    {
        global $pth, $bjs;

        $bjs = '<script type="text/javascript" src="'
            . $pth['folder']['plugins'] . 'chess/chess.js"></script>';
    }
}
