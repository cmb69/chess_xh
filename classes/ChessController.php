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

use Plib\DocumentStore;
use Plib\Request;
use Plib\Response;
use Plib\View;

class ChessController
{
    /** @var string */
    private $pluginFolder;

    /** @var DocumentStore */
    private $store;

    /** @var View */
    private $view;

    public function __construct(
        string $pluginFolder,
        DocumentStore $store,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->store = $store;
        $this->view = $view;
    }

    public function chess(string $basename, Request $request): Response
    {
        $requestedGame = $request->get("chess_game") ?? "";
        if (!Game::isValidName($requestedGame)) {
            $requestedGame = "";
        }
        if ($request->get("chess_ajax") !== null && $requestedGame != $basename) {
            return Response::create();
        }
        if (!Game::isValidName($basename)) {
            return Response::create($this->view->message("fail", "message_invalid_name", $basename));
        }
        $game = Game::retrieve($basename, $this->store);
        if (!$game) {
            return Response::create($this->view->message("fail", "message_load_error", $basename));
        }
        $this->emitScript();
        if ($request->get("chess_ajax") !== null) {
            return Response::create($this->render($game, $this->getPly($request, $game), $this->isFlipped($request)))
                ->withContentType("Content-Type:text/html; charset=UTF-8");
        } else {
            return Response::create($this->render($game, $this->getPly($request, $game), $this->isFlipped($request)));
        }
    }

    private function getPly(Request $request, Game $game): int
    {
        $result = (int) ($request->get("chess_ply") ?? "");
        switch ($this->requestedAction($request)) {
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

    private function isFlipped(Request $request): bool
    {
        $result = (bool) ($request->get("chess_flipped") ?? "");
        if ($this->requestedAction($request) == 'flip') {
            $result = !$result;
        }
        return $result;
    }

    private function requestedAction(Request $request): string
    {
        $res = $request->get("chess_action") ?? "";
        $actions = array('start', 'previous', 'next', 'end', 'flip');
        if (!in_array($res, $actions)) {
            $res = "";
        }
        return $res;
    }

    private function emitScript(): void
    {
        global $bjs;

        $bjs = '<script type="text/javascript" src="'
            . $this->pluginFolder . 'chess.js"></script>';
    }

    public function render(Game $game, int $ply, bool $flipped): string
    {
        global $sn, $su;
        $position = $game->getPosition(min($ply, $game->getPlyCount()));
        return $this->view->render("main", [
            "name" => $game->getName(),
            "ranks" => $this->board($game, $ply, $position, $flipped),
            "url" => $sn . '#chess_view_' . $game->getName(),
            "selected" => $su,
            "ply" => $ply,
            "flipped" => (int) $flipped,
            "start_disabled" => $ply === 0 ? "disabled" : "",
            "end_disabled" => $ply === $game->getPlyCount() ? "disabled" : "",
        ]);
    }

    private function board(Game $game, int $ply, Position $position, bool $flipped): array
    {
        $result = [];
        foreach ($this->getRanks($flipped) as $rank) {
            $result[] = $this->rank($game, $rank, $ply, $position, $flipped);
        }
        return $result;
    }

    private function getRanks(bool $flipped): array
    {
        $ranks = range(8, 1, -1);
        if ($flipped) {
            $ranks = array_reverse($ranks);
        }
        return $ranks;
    }

    private function rank(Game $game, int $rank, int $ply, Position $position, bool $flipped): array
    {
        $result = [];
        foreach ($this->getFiles($flipped) as $file) {
            $result[] = $this->renderSquare($game, $file, $rank, $ply, $position);
        }
        return $result;
    }

    private function getFiles(bool $flipped): array
    {
        $files = array_map('chr', range(97, 104));
        if ($flipped) {
            $files = array_reverse($files);
        }
        return $files;
    }

    private function renderSquare(Game $game, string $file, int $rank, int $ply, Position $position): string
    {
        $square = "$file$rank";
        $class = ((int) $rank + ord($file)) % 2 ? 'chess_light' : 'chess_dark';
        $result = '<td class="' . $class . '">' . "\n";
        $move = $game->getMove($ply - 1);
        $moved = $move !== null && $move->isSourceOrDestination($square);
        if ($position->hasPieceOn($square)) {
            $result .= $this->renderPiece($position->getPieceOn($square), $moved);
        } else {
            if ($moved) {
                $result .= '<span class="chess_move">&nbsp;</span>';
            } else {
                $result .= '&nbsp;';
            }
        }
        $result .= '</td>' . "\n";
        return $result;
    }

    private function renderPiece(string $piece, bool $moved): string
    {
        $src = $this->pluginFolder . 'images/' . $piece . '.png';
        $class = $moved ? 'class="chess_move"' : '';
        return '<img ' . $class . ' src="' . $src . '" alt="' . $piece . '">';
    }
}
