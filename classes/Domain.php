<?php

/**
 * The domain layer.
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
 * The games.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class Chess_Game
{
    /**
     * The start position.
     *
     * @var array A map from squares to pieces.
     */
    private static $_startPosition = array(
        'a1' => 'wr', 'b1' => 'wn', 'c1' => 'wb', 'd1' => 'wq',
        'e1' => 'wk', 'f1' => 'wb', 'g1' => 'wn', 'h1' => 'wr',
        'a2' => 'wp', 'b2' => 'wp', 'c2' => 'wp', 'd2' => 'wp',
        'e2' => 'wp', 'f2' => 'wp', 'g2' => 'wp', 'h2' => 'wp',
        'a7' => 'bp', 'b7' => 'bp', 'c7' => 'bp', 'd7' => 'bp',
        'e7' => 'bp', 'f7' => 'bp', 'g7' => 'bp', 'h7' => 'bp',
        'a8' => 'br', 'b8' => 'bn', 'c8' => 'bb', 'd8' => 'bq',
        'e8' => 'bk', 'f8' => 'bb', 'g8' => 'bn', 'h8' => 'br'
    );

    /**
     * The moves.
     *
     * @var array A list of records.
     */
    private $_moves;

    /**
     * Returns a game loaded from a file; <var>null</var> if the game can't be
     * loaded.
     *
     * @param string $basename A basename of a data file.
     *
     * @return Chess_Game
     */
    public static function load($basename)
    {
        global $pth;

        $filename = $pth['folder']['plugins'] . 'chess/data/' . $basename
            . '.dat';
        if (!is_readable($filename)) {
            return null;
        }
        $result = unserialize(file_get_contents($filename));
        if ($result) {
            return $result;
        } else {
            return null;
        }
    }

    /**
     * Initializes a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_moves = array();
    }

    /**
     * Returns the number of plies.
     *
     * @return int
     */
    public function getPlyCount()
    {
        return count($this->_moves);
    }

    /**
     * Returns the position after a certain ply.
     *
     * @param int $ply A ply number.
     *
     * @return array A map from squares to pieces.
     */
    public function getPosition($ply)
    {
        $position = self::$_startPosition;
        for ($i = 0; $i < $ply; ++$i) {
            $this->_doMove($position, $this->_moves[$i]);
        }
        return $position;
    }

    /**
     * Registers a move.
     *
     * We're assuming valid moves only for now.
     *
     * @param string $from      A square.
     * @param string $to        A square.
     * @param string $promotion A piece.
     *
     * @return void
     */
    public function move($from, $to, $promotion = null)
    {
        $this->_moves[] = compact('from', 'to', 'promotion');
    }

    /**
     * Makes a move and changes the position accordingly.
     *
     * @param array &$position A position.
     * @param array $move      A move record.
     *
     * @return void
     */
    private function _doMove(&$position, $move)
    {
        extract($move);
        switch ($position[$from][1]) {
        case 'k':
            if (abs(ord($from[0]) - ord($to[0])) == 2) {
                // castling
                if ($to[0] == 'g') {
                    // king's side
                    $rookFrom = "h$from[1]";
                    $rookTo = "f$from[1]";
                } else {
                    // queen's side
                    $rookFrom = "a$from[1]";
                    $rookTo = "d$from[1]";
                }
                $position[$rookTo] = $position[$rookFrom];
                unset($position[$rookFrom]);
            }
            break;
        case 'p':
            if ($to[0] != $from[0] && !isset($position[$to])) {
                // en passant
                unset($position["$to[0]$from[1]"]);
            }
            break;
        }
        $position[$to] = $position[$from];
        if (isset($promotion)) {
            $position[$to] = $position[$to][0] . $promotion;
        }
        unset($position[$from]);
    }
}

?>
