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

class GameView
{
    /** @var Game */
    private $game;

    /** @var int */
    private $ply;

    /** @var Position */
    private $position;

    /** @var bool */
    private $flipped;

    public function __construct(Game $game, int $ply = 0, bool $flipped = false)
    {
        $this->game = $game;
        $this->ply = (int) $ply;
        $this->position = $game->getPosition(
            min($this->ply, $this->game->getPlyCount())
        );
        $this->flipped = (bool) $flipped;
    }

    public function render(): string
    {
        return '<div id="chess_view_' . $this->game->getName()
            . '" class="chess_view">' . "\n"
            . $this->renderBoard() . $this->renderControlPanel()
            . '</div>' . "\n";
    }

    private function renderBoard(): string
    {
        $result = '<table class="chess_board">' . "\n";
        foreach ($this->getRanks() as $rank) {
            $result .= $this->renderRank($rank);
        }
        $result .= '</table>' . "\n";
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

    private function renderRank(int $rank): string
    {
        $result = '<tr>' . "\n";
        foreach ($this->getFiles() as $file) {
            $result .= $this->renderSquare($file, $rank);
        }
        $result .= '</tr>' . "\n";
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

    private function renderControlPanel(): string
    {
        global $sn, $su;

        return '<form class="chess_control_panel" action="' . $sn
            . '#chess_view_' . $this->game->getName() . '" method="'
            . 'get' . '">'
            . $this->renderHiddenInput('selected', $su)
            . $this->renderHiddenInput('chess_game', $this->game->getName())
            . $this->renderHiddenInput('chess_flipped', (string) (int) $this->flipped)
            . $this->renderButton('goto')
            . $this->renderButton('start') . $this->renderButton('previous')
            . $this->renderPlyInput($this->ply)
            . $this->renderButton('next') . $this->renderButton('end')
            . $this->renderButton('flip')
            . '</form>';
    }

    private function renderPlyInput(int $value): string
    {
        return '<input type="text" name="chess_ply" value="' . $value . '">';
    }

    private function renderHiddenInput(string $name, string $value): string
    {
        return '<input type="hidden" name="' . $name . '" value="' . $value . '">';
    }

    private function renderButton(string $which): string
    {
        global $plugin_tx;

        switch ($which) {
            case 'start':
                $value = 'start';
                $disabled = ($this->ply == 0);
                break;
            case 'previous':
                $value = 'previous';
                $disabled = ($this->ply == 0);
                break;
            case 'goto':
                $value = 'goto';
                $disabled = false;
                break;
            case 'next':
                $value = 'next';
                $disabled = ($this->ply == $this->game->getPlyCount());
                break;
            case 'end':
                $value = 'end';
                $disabled = ($this->ply == $this->game->getPlyCount());
                break;
            case 'flip':
                $value = 'flip';
                $disabled = false;
                break;
            default:
                $value = "";
                $disabled = true;
        }
        return '<button type="submit" name="chess_action" value="' . $value . '"'
            . ($disabled ? ' disabled="disabled"' : '') . '>'
            . $plugin_tx['chess']["label_$which"] . '</button>';
    }
}
