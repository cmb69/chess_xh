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

use Plib\View;

class GameView
{
    /** @var Game */
    private $game;

    /** @var View */
    private $view;

    /** @var int */
    private $ply;

    /** @var Position */
    private $position;

    /** @var bool */
    private $flipped;

    public function __construct(Game $game, View $view, int $ply = 0, bool $flipped = false)
    {
        $this->game = $game;
        $this->view = $view;
        $this->ply = (int) $ply;
        $this->position = $game->getPosition(
            min($this->ply, $this->game->getPlyCount())
        );
        $this->flipped = (bool) $flipped;
    }

    public function render(): string
    {
        global $sn, $su;
        return $this->view->render("main", [
            "name" => $this->game->getName(),
            "ranks" => $this->board(),
            "url" => $sn . '#chess_view_' . $this->game->getName(),
            "selected" => $su,
            "ply" => $this->ply,
            "flipped" => (int) $this->flipped,
            "start_disabled" => $this->ply === 0 ? "disabled" : "",
            "end_disabled" => $this->ply === $this->game->getPlyCount() ? "disabled" : "",
        ]);
    }

    private function board(): array
    {
        $result = [];
        foreach ($this->getRanks() as $rank) {
            $result[] = $this->rank($rank);
        }
        return $result;
    }

    private function getRanks(): array
    {
        $ranks = range(8, 1, -1);
        if ($this->flipped) {
            $ranks = array_reverse($ranks);
        }
        return $ranks;
    }

    private function rank(int $rank): array
    {
        $result = [];
        foreach ($this->getFiles() as $file) {
            $result[] = $this->renderSquare($file, $rank);
        }
        return $result;
    }

    private function getFiles(): array
    {
        $files = array_map('chr', range(97, 104));
        if ($this->flipped) {
            $files = array_reverse($files);
        }
        return $files;
    }

    private function renderSquare(string $file, int $rank): string
    {
        $square = "$file$rank";
        $class = ((int) $rank + ord($file)) % 2 ? 'chess_light' : 'chess_dark';
        $result = '<td class="' . $class . '">' . "\n";
        $move = $this->game->getMove($this->ply - 1);
        $moved = $move !== null && $move->isSourceOrDestination($square);
        if ($this->position->hasPieceOn($square)) {
            $result .= $this->renderPiece($this->position->getPieceOn($square), $moved);
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
        global $pth;

        $src = $pth['folder']['plugins'] . 'chess/images/' . $piece . '.png';
        $class = $moved ? 'class="chess_move"' : '';
        return '<img ' . $class . ' src="' . $src . '" alt="' . $piece . '">';
    }
}
