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

use LogicException;
use Plib\Document;
use Plib\DocumentStore;

final class Game implements Document
{
    /** @var string */
    private $name;

    /** @var array  */
    private $moves;

    public static function isValidName(string $basename): bool
    {
        return (bool) preg_match('/^[a-z0-9_-]+$/ui', $basename);
    }

    public static function fromString(string $contents, string $key): ?self
    {
        $that = unserialize($contents);
        if (!($that instanceof self)) {
            return null;
        }
        $that->name = basename($key, ".dat");
        return $that;
    }

    public static function retrieve(string $name, DocumentStore $store): ?self
    {
        return $store->retrieve($name . ".dat", self::class);
    }

    // public static function load(string $filename): ?Game
    // {
    //     if (!is_readable($filename)) {
    //         return null;
    //     }
    //     $result = unserialize(file_get_contents($filename));
    //     if ($result) {
    //         $result->name = basename($filename, ".dat");
    //         return $result;
    //     } else {
    //         return null;
    //     }
    // }

    public function __construct()
    {
        $this->name = '';
        $this->moves = array();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPlyCount(): int
    {
        return count($this->moves);
    }

    public function getPosition(int $ply): Position
    {
        $position = new Position();
        $ply = min($ply, $this->getPlyCount());
        for ($i = 0; $i < $ply; ++$i) {
            $position->applyMove($this->moves[$i]);
        }
        return $position;
    }

    public function getMove(int $ply): ?Move
    {
        if ($ply >= 0 && $ply < $this->getPlyCount()) {
            return $this->moves[$ply];
        } else {
            return null;
        }
    }

    /** We're assuming valid moves only for now */
    public function move(string $from, string $to, ?string $promotion = null): void
    {
        $this->moves[] = new Move($from, $to, $promotion);
    }

    public function __toString(): string
    {
        return $this->exportTagPairs() . "\n" . $this->exportMoveText();
    }

    private function exportTagPairs(): string
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

    private function exportTagPair(string $name): string
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

    private function exportMoveText(): string
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

    public function toString(): string
    {
        throw new LogicException("can't save games");
    }
}
