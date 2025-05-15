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

namespace Chess;

use __PHP_Incomplete_Class;

/**
 * The games.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class Game
{
    /**
     * The name of the game (no pun intended ;).
     *
     * @var string.
     */
    private $name;

    /**
     * The moves.
     *
     * @var array A list of records.
     */
    private $moves;

    /**
     * Returns whether a name is a valid game name.
     *
     * @param string $basename A basename.
     *
     * @return bool
     */
    public static function isValidName($basename)
    {
        return (bool) preg_match('/^[a-z0-9_-]+$/ui', $basename);
    }

    /**
     * Returns a game loaded from a file; <var>null</var> if the game can't be
     * loaded.
     *
     * @param string $basename A basename of a data file.
     *
     * @return ?Game
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
            $result->name = $basename;
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
        $this->name = '';
        $this->moves = array();
    }

    /**
     * Returns the name of the game.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the number of plies.
     *
     * @return int
     */
    public function getPlyCount()
    {
        return count($this->moves);
    }

    /**
     * Returns the position after a certain ply.
     *
     * @param int $ply A ply number.
     *
     * @return Position
     */
    public function getPosition($ply)
    {
        $position = new Position();
        $ply = min($ply, $this->getPlyCount());
        for ($i = 0; $i < $ply; ++$i) {
            $position->applyMove($this->moves[$i]);
        }
        return $position;
    }

    /**
     * Returns a certain move.
     *
     * @param int $ply A ply number.
     *
     * @return ?Move
     */
    public function getMove($ply)
    {
        if ($ply >= 0 && $ply < $this->getPlyCount()) {
            return $this->moves[$ply];
        } else {
            return null;
        }
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
        $this->moves[] = new Move($from, $to, $promotion);
    }

    /**
     * Returns the game in PGN.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->exportTagPairs() . "\n" . $this->exportMoveText();
    }

    /**
     * Returns the PGN tag pairs.
     *
     * @return string
     */
    private function exportTagPairs()
    {
        $result = '';
        $tagNames = array(
            'event', 'site', 'date', 'round', 'white', 'black', 'result'
        );
        foreach ($tagNames as $tagName) {
            $result .= $this->exportTagPair($tagName);
        }
        return $result;
    }

    /**
     * Returns a PGN tag pair.
     *
     * @param string $name A tag name.
     *
     * @return string
     */
    private function exportTagPair($name)
    {
        switch ($name) {
            case 'date':
                $value = '??.??.??';
                break;
            case 'result':
                $value = '*';
                break;
            default:
                $value = '?';
        }
        return sprintf('[%s "%s"]' . "\n", ucfirst($name), $value);
    }

    /**
     * Returns the PGN movetext.
     *
     * @return string
     */
    private function exportMoveText()
    {
        $result = '';
        for ($i = 0; $i < $this->getPlyCount(); ++$i) {
            if ($i % 2 == 0) {
                $result .= (int) ($i / 2) + 1 . '. ';
            }
            $result .= $this->moves[$i]->getSan($this->getPosition($i)) . ' ';
        }
        $result .= '*';
        return $result;
    }
}
