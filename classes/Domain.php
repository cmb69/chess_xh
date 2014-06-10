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
        $position = new Chess_Position();
        $ply = min($ply, $this->getPlyCount());
        for ($i = 0; $i < $ply; ++$i) {
            $position->applyMove($this->_moves[$i]);
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
     * Returns the game in PGN.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_exportTagPairs() . "\n" . $this->_exportMoveText();
    }

    /**
     * Returns the PGN tag pairs.
     *
     * @return string
     */
    private function _exportTagPairs()
    {
        $result = '';
        $tagNames = array(
            'event', 'site', 'date', 'round', 'white', 'black', 'result'
        );
        foreach ($tagNames as $tagName) {
            $result .= $this->_exportTagPair($tagName);
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
    private function _exportTagPair($name)
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
    private function _exportMoveText()
    {
        $result = '';
        for ($i = 0; $i < $this->getPlyCount(); ++$i) {
            if ($i % 2 == 0) {
                $result .= (int) ($i / 2) + 1 . '. ';
            }
            $result .= $this->_moves[$i]->getSan($this->getPosition($i)) . ' ';
        }
        $result .= '*';
        return $result;
    }
}

/**
 * The positions.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class Chess_Position
{
    /**
     * The sparse map of squares to pieces.
     *
     * @var array
     */
    private $_pieces;

    /**
     * Creates a new position from a FEN string.
     *
     * @param string $fen A FEN string.
     *
     * @return Chess_Position
     */
    public static function makeFromFen($fen)
    {
        $result = new self();
        $result->_pieces = array();
        $rank = 8;
        $file = 'a';
        for ($i = 0; $i < strlen($fen); ++$i) {
            $char = $fen[$i];
            if ($char == '/') {
                --$rank;
                $file = 'a';
            } elseif ($char >= '1' && $char <= '8') {
                $file = chr(ord($file) + $char);
            } else {
                $color = ($char >= 'A' && $char <= 'Z') ? 'w' : 'b';
                $result->_pieces[$file . $rank] = $color . strtolower($char);
                ++$file;
            }
        }
        return $result;
    }

    /**
     * Initializes a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_pieces = array(
            'a1' => 'wr', 'b1' => 'wn', 'c1' => 'wb', 'd1' => 'wq',
            'e1' => 'wk', 'f1' => 'wb', 'g1' => 'wn', 'h1' => 'wr',
            'a2' => 'wp', 'b2' => 'wp', 'c2' => 'wp', 'd2' => 'wp',
            'e2' => 'wp', 'f2' => 'wp', 'g2' => 'wp', 'h2' => 'wp',
            'a7' => 'bp', 'b7' => 'bp', 'c7' => 'bp', 'd7' => 'bp',
            'e7' => 'bp', 'f7' => 'bp', 'g7' => 'bp', 'h7' => 'bp',
            'a8' => 'br', 'b8' => 'bn', 'c8' => 'bb', 'd8' => 'bq',
            'e8' => 'bk', 'f8' => 'bb', 'g8' => 'bn', 'h8' => 'br'
        );
    }

    /**
     * Returns whether there is a piece on a certain square.
     *
     * @param string $square A square.
     *
     * @return bool
     *
     * @todo Rename to isOccupied?
     */
    public function hasPieceOn($square)
    {
        return isset($this->_pieces[$square]);
    }

    /**
     * Returns the piece on a certain square.
     *
     * @param string $square A square.
     *
     * @return string
     */
    public function getPieceOn($square)
    {
        return $this->_pieces[$square];
    }

    /**
     * Applies a move. Doesn't check for validity.
     *
     * @param Chess_Move $move A move.
     *
     * @return void
     */
    public function applyMove($move)
    {
        if ($this->isCastling($move)) {
            $this->_moveRookForCastling($move);
        } elseif ($this->isEnPassant($move)) {
            $this->_removeEnPassantCapturedPawn($move);
        }
        $destination = $move->getDestination();
        $this->_pieces[$destination] = $this->_pieces[$move->getSource()];
        if ($move->getPromotion() !== null) {
            $this->_pieces[$destination]
                = $this->_pieces[$destination][0] . $move->getPromotion();
        }
        $this->_removePiece($move->getSource());
    }

    /**
     * Returns whether a king can be moved.
     *
     * @param bool $isWhite Whether to check the white or black king.
     *
     * @return bool
     */
    public function canMoveKing($isWhite)
    {
        $piece = $isWhite ? 'wk' : 'bk';
        $kingSquare = array_search($piece, $this->_pieces);
        $destinations = $this->_getCapturingDestinations($kingSquare);
        foreach ($destinations as $destination) {
            $position = clone $this;
            $position->applyMove(new Chess_Move($kingSquare, $destination));
            if (!$position->isUnderAttack($destination)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns whether a square is under attack.
     *
     * @param string $square A square in AN.
     *
     * @return bool
     */
    public function isUnderAttack($square)
    {
        foreach (array_keys($this->_pieces) as $attacker) {
            if ($this->isAttacking($attacker, $square)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns whether a piece is attacking a square.
     *
     * @param string $source      A square in AN.
     * @param string $destination A square in AN.
     *
     * @return bool
     */
    public function isAttacking($source, $destination)
    {
        if ($this->_pieces[$source][0] == $this->_pieces[$destination][0]) {
            return false;
        }
        return in_array($destination, $this->_getCapturingDestinations($source));
    }

    /**
     * Returns the allowed capturing destinations of a piece.
     *
     * @param string $square A square in AN.
     *
     * @return array
     */
    private function _getCapturingDestinations($square)
    {
        $result = array();
        switch ($this->_pieces[$square][1]) {
        case 'p':
            if ($this->_pieces[$square][0] == 'w') {
                $directions = array('nw', 'ne');
            } else {
                $directions = array('sw', 'se');
            }
            $result = $this->_getNeighborSquares($square, $directions);
            break;
        case 'n':
            $result = $this->_getKnightsSquares($square);
            break;
        case 'b':
            $directions = array('ne', 'se', 'sw', 'nw');
            // fall through
        case 'r':
            $directions = array('n', 'e', 's', 'w');
            // fall through
        case 'q':
            $directions = array('n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw');
            foreach ($directions as $direction) {
                $result = array_merge(
                    $result, $this->_getSquaresTo($direction, $square)
                );
            }
            break;
        case 'k':
            $directions = array('n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw');
            foreach ($directions as $direction) {
                $neighbor = $this->_getNeighborSquare($square, $direction);
                if ($neighbor) {
                    $result []= $neighbor;
                }
            }
            break;
        }
        return $result;
    }

    /**
     * Returns an array of allowed knight's squares.
     *
     * @param string $square A square in AN.
     *
     * @return array
     */
    private function _getKnightsSquares($square)
    {
        $result = array();
        foreach (array('n', 'e', 's', 'w') as $direction) {
            $square1 = $this->_getNeighborSquare($square, $direction);
            if ($square1) {
                switch ($direction) {
                case 'n';
                    $result = array_merge(
                        $result,
                        $this->_getNeighborSquares($square1, array('nw', 'ne'))
                    );
                    break;
                case 'e':
                    $result = array_merge(
                        $result,
                        $this->_getNeighborSquares($square1, array('ne', 'se'))
                    );
                    break;
                case 's':
                    $result = array_merge(
                        $result,
                        $this->_getNeighborSquares($square1, array('se', 'sw'))
                    );
                    break;
                case 'w':
                    $result = array_merge(
                        $result,
                        $this->_getNeighborSquares($square1, array('sw', 'nw'))
                    );
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Returns the squares in a certain direction.
     *
     * Use only for queen, rook and bishop.
     *
     * @param string $direction A direction, e.g. 'n' or 'se'.
     * @param string $square    A square in AN.
     *
     * @return array
     */
    private function _getSquaresTo($direction, $square)
    {
        $result = array();
        while ($square = $this->_getNeighborSquare($square, $direction)) {
            $result []= $square;
            if ($this->hasPieceOn($square)) {
                break;
            }
        }
        return $result;
    }

    /**
     * Returns the neighboring squares.
     *
     * @param string $square     A square in AN.
     * @param array  $directions An array of directions.
     *
     * @return array
     */
    private function _getNeighborSquares($square, $directions)
    {
        $result = array();
        foreach ($directions as $direction) {
            $neighbor = $this->_getNeighborSquare($square, $direction);
            if ($neighbor) {
                $result []= $neighbor;
            }
        }
        return $result;
    }

    /**
     * Returns a neighboring square.
     *
     * @param string $square    A square in AN.
     * @param string $direction A direction.
     *
     * @return string
     */
    private function _getNeighborSquare($square, $direction)
    {
        $file = $square[0]; $rank = $square[1];
        switch ($direction) {
        case 'n':
            ++$rank;
            break;
        case 'ne':
            ++$rank; ++$file;
            break;
        case 'e':
            ++$file;
            break;
        case 'se':
            --$rank; ++$file;
            break;
        case 's':
            --$rank;
            break;
        case 'sw':
            --$rank; $file = chr(ord($file) - 1);
            break;
        case 'w':
            $file = chr(ord($file) - 1);
            break;
        case 'nw':
            ++$rank; $file = chr(ord($file) - 1);
            break;
        }
        $square = $file . $rank;
        return $this->_isValidSquare($square) ? $square : false;
    }

    /**
     * Returns whether a square is valid (i.e. exists on the board).
     *
     * @param string $square A square in AN.
     *
     * @return bool
     */
    private function _isValidSquare($square)
    {
        return $square[0] >= 'a' && $square[0] <= 'h'
            && $square[1] >= '1' && $square[1] <= '8';
    }

    /**
     * Returns whether a king is checked.
     *
     * @param bool $isWhite Whether the white (vs. black) king is relevant.
     *
     * @return bool
     */
    public function isChecked($isWhite)
    {
        $king = $isWhite ? 'wk' : 'bk';
        return ($kingSquare = array_search($king, $this->_pieces))
            && $this->isUnderAttack($kingSquare);
    }

    /**
     * Returns whether a move is castling.
     *
     * @param Chess_Move $move A move.
     *
     * @return bool
     */
    public function isCastling($move)
    {
        return $this->_pieces[$move->getSource()][1] == 'k'
            && $move->getFileDistance() == 2;
    }

    /**
     * Moves the rook when castling.
     *
     * @param Chess_Move $move A move.
     *
     * @return void
     */
    private function _moveRookForCastling($move)
    {
        if ($move->getDestinationFile() == 'g') { // king's side
            $rookFrom = 'h' . $move->getSourceRank();
            $rookTo = 'f' . $move->getSourceRank();
        } else { // queen's side
            $rookFrom = 'a' . $move->getSourceRank();
            $rookTo = 'd' . $move->getSourceRank();
        }
        $this->_pieces[$rookTo] = $this->_pieces[$rookFrom];
        $this->_removePiece($rookFrom);
    }

    /**
     * Returns whether a move is an en passant capture.
     *
     * @param Chess_Move $move A move.
     *
     * @return void
     */
    public function isEnPassant($move)
    {
        return $this->_pieces[$move->getSource()][1] == 'p'
            && $move->getDestinationFile() != $move->getSourceFile()
            && !$this->hasPieceOn($move->getDestination());
    }

    /**
     * Removes an en passant captured pawn.
     *
     * @param Chess_Move $move A move.
     *
     * @return void
     */
    private function _removeEnPassantCapturedPawn($move)
    {
        $this->_removePiece(
            $move->getDestinationFile() . $move->getSourceRank()
        );
    }

    /**
     * Removes a piece from the position.
     *
     * @param string $square A square.
     *
     * @return void
     */
    private function _removePiece($square)
    {
        unset($this->_pieces[$square]);
    }

    /**
     * Returns a string representation of the object (piece placement of FEN).
     *
     * @return string
     */
    public function __toString()
    {
        $ranks = array();
        for ($rank = 8; $rank >= 1; --$rank) {
            $ranks []= $this->_rankToString($rank);
        }
        return implode('/', $ranks);
    }

    /**
     * Returns the FEN piece placement of a certain rank.
     *
     * @param string $rank A rank.
     *
     * @return string
     */
    private function _rankToString($rank)
    {
        $result = '';
        $emptySquares = 0;
        for ($file = 'a'; $file <= 'h'; ++$file) {
            if (isset($this->_pieces[$file . $rank])) {
                if ($emptySquares > 0) {
                    $result .= $emptySquares;
                    $emptySquares = 0;
                }
                $piece = $this->_pieces[$file . $rank];
                if ($piece[0] == 'w') {
                    $piece = strtoupper($piece[1]);
                } else {
                    $piece = $piece[1];
                }
                $result .= $piece;
            } else {
                ++$emptySquares;
            }
        }
        if ($emptySquares > 0) {
            $result .= $emptySquares;
        }
        return $result;
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

    /**
     * Returns the SAN of the move.
     *
     * @param Chess_Position $position A position.
     *
     * @return string
     */
    public function getSan(Chess_Position $position)
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
     * @param Chess_Position $position A position.
     *
     * @return bool
     */
    private function _isCapture(Chess_Position $position)
    {
        return $position->hasPieceOn($this->_destination)
            || $position->isEnPassant($this);
    }
}

?>
