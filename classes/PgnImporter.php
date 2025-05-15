<?php

/**
 * The service layer.
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
                    $result []= basename($entry, '.pgn');
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
