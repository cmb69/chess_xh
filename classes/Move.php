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

/**
 * The moves.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class Move
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
     * @var ?string
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

    /**
     * Returns whether a square is the source or destination of this move.
     *
     * @param string $square A square in AN.
     *
     * @return bool
     */
    public function isSourceOrDestination($square)
    {
        return $square == $this->getSource()
            || $square == $this->getDestination();
    }

    /**
     * Returns the SAN of the move.
     *
     * @param Position $position A position.
     *
     * @return string
     */
    public function getSan(Position $position)
    {
        if ($position->isCastling($this)) {
            if ($this->getDestinationFile() == 'g') {
                return 'O-O';
            } else {
                return 'O-O-O';
            }
        }

        $result = '';

        $piece = $position->getPieceOn($this->_source);
        $piece = strtoupper($piece[1]);
        if ($piece == 'P') {
            $piece = '';
        }
        $result .= $piece;

        if ($this->_isCapture($position)) {
            if ($piece == '') {
                $result = $this->getSourceFile();
            }
            $result .= 'x';
        }

        $result .= $this->_destination;

        if (isset($this->_promotion)) {
            $result .= '=' . strtoupper($this->_promotion);
        }

        $piece = $position->getPieceOn($this->_source);
        $isWhite = $piece[0] != 'w';
        $position1 = clone $position;
        $position1->applyMove($this);
        if ($position1->isChecked($isWhite)) {
            if ($position1->canMoveKing($isWhite)) {
                $result .= '+';
            } else {
                $result .= '#';
            }
        }

        return $result;
    }

    /**
     * Returns whether the move is capturing.
     *
     * @param Position $position A position.
     *
     * @return bool
     */
    private function _isCapture(Position $position)
    {
        return $position->hasPieceOn($this->_destination)
            || $position->isEnPassant($this);
    }
}
