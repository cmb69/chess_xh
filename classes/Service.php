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

/**
 * The PGN importers.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class Chess_PgnImporter
{
    /**
     * The path of the data folder.
     *
     * @var string
     */
    private $_dataFolder;

    /**
     * Initializes a new instance.
     *
     * @param string $dataFolder A data folder path.
     *
     * @return void
     */
    public function __construct($dataFolder)
    {
        $this->_dataFolder = (string) $dataFolder;
    }

    /**
     * Returns a list of all PGN files.
     *
     * @return array
     */
    public function findAll()
    {
        $result = array();
        if ($dir = opendir($this->_dataFolder)) {
            while ($entry = readdir($dir)) {
                if (pathinfo($entry, PATHINFO_EXTENSION) == 'pgn') {
                    $result []= basename($entry, '.pgn');
                }
            }
        }
        natcasesort($result);
        return $result;
    }

    /**
     * Imports a PGN file.
     *
     * @param string $name A basename of a file.
     *
     * @return void
     *
     * @global array The paths of system files and folders.
     */
    public function import($name)
    {
        global $pth;

        $folder = $pth['folder']['plugins'] . 'chess/classes/chessParser/';
        include_once $folder . 'Board0x88Config.php';
        include_once $folder . 'CHESS_JSON.php';
        include_once $folder . 'FenParser0x88.php';
        include_once $folder . 'GameParser.php';
        include_once $folder . 'MoveBuilder.php';
        include_once $folder . 'PgnGameParser.php';
        include_once $folder . 'PgnParser.php';

        $parser = new PgnParser($this->_dataFolder . $name . '.pgn');
        $games = $parser->getGames();
        foreach ($games as $i => $pgnGame) {
            $game = new Chess_Game();
            foreach ($pgnGame['moves'] as $move) {
                if (preg_match('/=(.)$/', $move['m'], $matches)) {
                    $promotion = strtolower($matches[1]);
                } else {
                    $promotion = null;
                }
                $game->move($move['from'], $move['to'], $promotion);
            }
            $suffix = ($i > 0) ? '_' . $i : '';
            file_put_contents(
                $this->_dataFolder . $name . $suffix . '.dat', serialize($game)
            );
        }
    }
}

?>
