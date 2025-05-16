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

class Move
{
    /** @var string */
    private $source;

    /** @var string */
    private $destination;

    /** @var ?string */
    private $promotion;

    public function __construct(string $source, string $destination, ?string $promotion = null)
    {
        $this->source = (string) $source;
        $this->destination = (string) $destination;
        $this->promotion = $promotion;
    }

    /** @return string */
    public function getSource()
    {
        return $this->source;
    }

    /** @return string */
    public function getSourceFile()
    {
        return $this->source[0];
    }

    /** @return string */
    public function getSourceRank()
    {
        return $this->source[1];
    }

    /** @return string */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Returns the destination file.
     *
     * @return string
     */
    public function getDestinationFile(): string
    {
        return $this->destination[0];
    }

    public function getFileDistance(): int
    {
        return abs(ord($this->source[0]) - ord($this->destination[0]));
    }

    public function getPromotion(): ?string
    {
        return $this->promotion;
    }

    public function isSourceOrDestination(string $square): bool
    {
        return $square == $this->getSource() || $square == $this->getDestination();
    }

    public function getSan(Position $position): string
    {
        if ($position->isCastling($this)) {
            if ($this->getDestinationFile() == 'g') {
                return 'O-O';
            } else {
                return 'O-O-O';
            }
        }

        $result = '';

        $piece = $position->getPieceOn($this->source);
        $piece = strtoupper($piece[1]);
        if ($piece == 'P') {
            $piece = '';
        }
        $result .= $piece;

        if ($this->isCapture($position)) {
            if ($piece == '') {
                $result = $this->getSourceFile();
            }
            $result .= 'x';
        }

        $result .= $this->destination;

        if (isset($this->promotion)) {
            $result .= '=' . strtoupper($this->promotion);
        }

        $piece = $position->getPieceOn($this->source);
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

    private function isCapture(Position $position): bool
    {
        return $position->hasPieceOn($this->destination)
            || $position->isEnPassant($this);
    }
}
