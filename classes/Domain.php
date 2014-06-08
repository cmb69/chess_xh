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
        $this->_moves[] = new Chess_Move($from, $to, $promotion);
    }

    /**
     * Makes a move and changes the position accordingly.
     *
     * @param array      &$position A position.
     * @param Chess_Move $move      A move.
     *
     * @return void
     */
    private function _doMove(&$position, $move)
    {
        switch ($position[$move->getSource()][1]) {
        case 'k':
            if ($move->getFileDistance() == 2) {
                // castling
                if ($move->getDestinationFile() == 'g') {
                    // king's side
                    $rookFrom = 'h' . $move->getSourceRank();
                    $rookTo = 'f' . $move->getSourceRank();
                } else {
                    // queen's side
                    $rookFrom = 'a' . $move->getSourceRank();
                    $rookTo = 'd' . $move->getSourceRank();
                }
                $position[$rookTo] = $position[$rookFrom];
                unset($position[$rookFrom]);
            }
            break;
        case 'p':
            if ($move->getDestinationFile() != $move->getSourceFile()
                && !isset($position[$move->getDestination()])
            ) {
                // en passant
                unset(
                    $position[$move->getDestinationFile() . $move->getSourceRank()]
                );
            }
            break;
        }
        $position[$move->getDestination()] = $position[$move->getSource()];
        if ($move->getPromotion() !== null) {
            $position[$move->getDestination()]
                = $position[$move->getDestination()][0] . $move->getPromotion();
        }
        unset($position[$move->getSource()]);
    }
}

/**
 * The moves.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class Chess_Move
{
    /**
     * The source square.
     *
     * @var string
     */
    private $_source;

    /**
     * The destination square.
     *
     * @var string
     */
    private $_destination;

    /**
     * The piece to promote to.
     *
     * @var string
     */
    private $_promotion;

    /**
     * Initializes a new instance.
     *
     * @param string $source      The source square.
     * @param string $destination The destination square.
     * @param string $promotion   The piece to promote to.
     *
     * @return void
     */
    public function __construct($source, $destination, $promotion = null)
    {
        $this->_source = (string) $source;
        $this->_destination = (string) $destination;
        $this->_promotion = $promotion;
    }

    /**
     * Returns the source square.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Returns the source file.
     *
     * @return string
     */
    public function getSourceFile()
    {
        return $this->_source[0];
    }

    /**
     * Returns the source rank.
     *
     * @return string
     */
    public function getSourceRank()
    {
        return $this->_source[1];
    }

    /**
     * Returns the destination square.
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->_destination;
    }

    /**
     * Returns the destination file.
     *
     * @return string
     */
    public function getDestinationFile()
    {
        return $this->_destination[0];
    }

    /**
     * Returns the file distance.
     *
     * @return int
     */
    public function getFileDistance()
    {
        return abs(ord($this->_source[0]) - ord($this->_destination[0]));
    }

    /**
     * Returns the piece to promote to.
     *
     * @return string
     */
    public function getPromotion()
    {
        return $this->_promotion;
    }
}

?>
