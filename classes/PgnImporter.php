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

use PgnParser;

class PgnImporter
{
    /** @var string */
    private $dataFolder;

    public function __construct(string $dataFolder)
    {
        $this->dataFolder = (string) $dataFolder;
    }

    public function findAll(): array
    {
        $result = array();
        if ($dir = opendir($this->dataFolder)) {
            while ($entry = readdir($dir)) {
                if (pathinfo($entry, PATHINFO_EXTENSION) == 'pgn') {
                    $result[] = basename($entry, '.pgn');
                }
            }
        }
        natcasesort($result);
        return $result;
    }

    public function import(string $name): void
    {
        global $pth;

        $folder = $pth['folder']['plugins'] . 'chess/parser/';
        include_once $folder . 'Board0x88Config.php';
        include_once $folder . 'CHESS_JSON.php';
        include_once $folder . 'FenParser0x88.php';
        include_once $folder . 'GameParser.php';
        include_once $folder . 'MoveBuilder.php';
        include_once $folder . 'PgnGameParser.php';
        include_once $folder . 'PgnParser.php';

        $parser = new PgnParser($this->dataFolder . $name . '.pgn');
        $games = $parser->getGames();
        foreach ($games as $i => $pgnGame) {
            $game = new Game();
            foreach ($pgnGame['moves'] as $move) {
                if (preg_match('/=(.)$/', $move['m'], $matches)) {
                    $promotion = strtolower($matches[1]);
                } else {
                    $promotion = null;
                }
                $game->move($move['from'], $move['to'], $promotion);
            }
            $suffix = ($i > 0) ? '_' . $i : '';
            file_put_contents($this->dataFolder . $name . $suffix . '.dat', serialize($game));
        }
    }
}
