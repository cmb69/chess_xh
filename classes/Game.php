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

class Game
{
    /** @var string */
    private $name;

    /** @var array  */
    private $moves;

    public static function isValidName(string $basename): bool
    {
        return (bool) preg_match('/^[a-z0-9_-]+$/ui', $basename);
    }

    public static function load(string $basename): ?Game
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
}
