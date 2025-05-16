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

namespace Chess\Model;

class Position
{
    /** @var array */
    private $pieces;

    public static function makeFromFen(string $fen): self
    {
        $result = new self();
        $result->pieces = array();
        $rank = 8;
        $file = 'a';
        for ($i = 0; $i < strlen($fen); ++$i) {
            $char = $fen[$i];
            if ($char == '/') {
                --$rank;
                $file = 'a';
            } elseif ($char >= '1' && $char <= '8') {
                $file = chr(ord($file) + (int) $char);
            } else {
                $color = ($char >= 'A' && $char <= 'Z') ? 'w' : 'b';
                $result->pieces[$file . $rank] = $color . strtolower($char);
                ++$file;
            }
        }
        return $result;
    }

    public function __construct()
    {
        $this->pieces = array(
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

    /** @todo Rename to isOccupied? */
    public function hasPieceOn(string $square): bool
    {
        return isset($this->pieces[$square]);
    }

    public function getPieceOn(string $square): string
    {
        return $this->pieces[$square];
    }

    public function applyMove(Move $move): void
    {
        if ($this->isCastling($move)) {
            $this->moveRookForCastling($move);
        } elseif ($this->isEnPassant($move)) {
            $this->removeEnPassantCapturedPawn($move);
        }
        $destination = $move->getDestination();
        $this->pieces[$destination] = $this->pieces[$move->getSource()];
        if ($move->getPromotion() !== null) {
            $this->pieces[$destination]
                = $this->pieces[$destination][0] . $move->getPromotion();
        }
        $this->removePiece($move->getSource());
    }

    public function canMoveKing(bool $isWhite): bool
    {
        $piece = $isWhite ? 'wk' : 'bk';
        $kingSquare = array_search($piece, $this->pieces);
        $destinations = $this->getCapturingDestinations($kingSquare);
        foreach ($destinations as $destination) {
            $position = clone $this;
            $position->applyMove(new Move($kingSquare, $destination));
            if (!$position->isUnderAttack($destination)) {
                return true;
            }
        }
        return false;
    }

    public function isUnderAttack(string $square): bool
    {
        foreach (array_keys($this->pieces) as $attacker) {
            if ($this->isAttacking($attacker, $square)) {
                return true;
            }
        }
        return false;
    }

    public function isAttacking(string $source, string $destination): bool
    {
        if ($this->pieces[$source][0] == $this->pieces[$destination][0]) {
            return false;
        }
        return in_array($destination, $this->getCapturingDestinations($source));
    }

    private function getCapturingDestinations(string $square): array
    {
        $result = array();
        switch ($this->pieces[$square][1]) {
            case 'p':
                if ($this->pieces[$square][0] == 'w') {
                    $directions = array('nw', 'ne');
                } else {
                    $directions = array('sw', 'se');
                }
                $result = $this->getNeighborSquares($square, $directions);
                break;
            case 'n':
                $result = $this->getKnightsSquares($square);
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
                    $result = array_merge($result, $this->getSquaresTo($direction, $square));
                }
                break;
            case 'k':
                $directions = array('n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw');
                foreach ($directions as $direction) {
                    $neighbor = $this->getNeighborSquare($square, $direction);
                    if ($neighbor) {
                        $result[] = $neighbor;
                    }
                }
                break;
        }
        return $result;
    }

    private function getKnightsSquares(string $square): array
    {
        $result = array();
        foreach (array('n', 'e', 's', 'w') as $direction) {
            $square1 = $this->getNeighborSquare($square, $direction);
            if ($square1) {
                switch ($direction) {
                    case 'n':
                        $result = array_merge(
                            $result,
                            $this->getNeighborSquares($square1, array('nw', 'ne'))
                        );
                        break;
                    case 'e':
                        $result = array_merge(
                            $result,
                            $this->getNeighborSquares($square1, array('ne', 'se'))
                        );
                        break;
                    case 's':
                        $result = array_merge(
                            $result,
                            $this->getNeighborSquares($square1, array('se', 'sw'))
                        );
                        break;
                    case 'w':
                        $result = array_merge(
                            $result,
                            $this->getNeighborSquares($square1, array('sw', 'nw'))
                        );
                        break;
                }
            }
        }
        return $result;
    }

    private function getSquaresTo(string $direction, string $square): array
    {
        $result = array();
        while ($square = $this->getNeighborSquare($square, $direction)) {
            $result[] = $square;
            if ($this->hasPieceOn($square)) {
                break;
            }
        }
        return $result;
    }

    private function getNeighborSquares(string $square, array $directions): array
    {
        $result = array();
        foreach ($directions as $direction) {
            $neighbor = $this->getNeighborSquare($square, $direction);
            if ($neighbor) {
                $result[] = $neighbor;
            }
        }
        return $result;
    }

    private function getNeighborSquare(string $square, string $direction): string
    {
        $file = $square[0];
        $rank = $square[1];
        switch ($direction) {
            case 'n':
                ++$rank;
                break;
            case 'ne':
                ++$rank;
                ++$file;
                break;
            case 'e':
                ++$file;
                break;
            case 'se':
                --$rank;
                ++$file;
                break;
            case 's':
                --$rank;
                break;
            case 'sw':
                --$rank;
                $file = chr(ord($file) - 1);
                break;
            case 'w':
                $file = chr(ord($file) - 1);
                break;
            case 'nw':
                ++$rank;
                $file = chr(ord($file) - 1);
                break;
        }
        $square = $file . $rank;
        return $this->isValidSquare($square) ? $square : false;
    }

    private function isValidSquare(string $square): bool
    {
        return $square[0] >= 'a' && $square[0] <= 'h'
            && $square[1] >= '1' && $square[1] <= '8';
    }

    public function isChecked(bool $isWhite): bool
    {
        $king = $isWhite ? 'wk' : 'bk';
        return ($kingSquare = array_search($king, $this->pieces))
            && $this->isUnderAttack($kingSquare);
    }

    public function isCastling(Move $move): bool
    {
        return $this->pieces[$move->getSource()][1] == 'k'
            && $move->getFileDistance() == 2;
    }

    private function moveRookForCastling(Move $move): void
    {
        if ($move->getDestinationFile() == 'g') { // king's side
            $rookFrom = 'h' . $move->getSourceRank();
            $rookTo = 'f' . $move->getSourceRank();
        } else { // queen's side
            $rookFrom = 'a' . $move->getSourceRank();
            $rookTo = 'd' . $move->getSourceRank();
        }
        $this->pieces[$rookTo] = $this->pieces[$rookFrom];
        $this->removePiece($rookFrom);
    }

    public function isEnPassant(Move $move): bool
    {
        return $this->pieces[$move->getSource()][1] == 'p'
            && $move->getDestinationFile() != $move->getSourceFile()
            && !$this->hasPieceOn($move->getDestination());
    }

    private function removeEnPassantCapturedPawn(Move $move): void
    {
        $this->removePiece(
            $move->getDestinationFile() . $move->getSourceRank()
        );
    }

    private function removePiece(string $square): void
    {
        unset($this->pieces[$square]);
    }

    public function __toString(): string
    {
        $ranks = array();
        for ($rank = 8; $rank >= 1; --$rank) {
            $ranks[] = $this->rankToString($rank);
        }
        return implode('/', $ranks);
    }

    private function rankToString(int $rank): string
    {
        $result = '';
        $emptySquares = 0;
        for ($file = 'a'; $file <= 'h'; ++$file) {
            if (isset($this->pieces[$file . $rank])) {
                if ($emptySquares > 0) {
                    $result .= $emptySquares;
                    $emptySquares = 0;
                }
                $piece = $this->pieces[$file . $rank];
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
