<?php

/**
 * The presentation layer.
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
 * The game views.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class GameView
{
    /**
     * The game.
     *
     * @var Game
     */
    private $_game;

    /**
     * The current ply number.
     *
     * @var int
     */
    private $_ply;

    /**
     * The current position.
     *
     * @var Position
     */
    private $_position;

    /**
     * Whether the board is flipped (i.e. the white side is at the top).
     *
     * @var bool
     */
    private $_flipped;

    /**
     * Makes a new game view.
     *
     * @param Game $game    A game.
     * @param int  $ply     A ply number.
     * @param bool $flipped Whether the board is flipped.
     *
     * @return GameView
     */
    public static function make(Game $game, $ply = 0, $flipped = false)
    {
        return new self($game, $ply, $flipped);
    }

    /**
     * Initializes a new instance.
     *
     * @param Game $game    A game.
     * @param int  $ply     A ply number.
     * @param bool $flipped Whether the board is flipped.
     *
     * @return void
     */
    public function __construct(Game $game, $ply = 0, $flipped = false)
    {
        $this->_game = $game;
        $this->_ply = (int) $ply;
        $this->_position = $game->getPosition(
            min($this->_ply, $this->_game->getPlyCount())
        );
        $this->_flipped = (bool) $flipped;
    }

    /**
     * Renders the game view.
     *
     * @return string (X)HTML.
     */
    public function render()
    {
        return '<div id="chess_view_' . $this->_game->getName()
            . '" class="chess_view">'
            . $this->_renderBoard() . $this->_renderControlPanel()
            . '</div>';
    }

    /**
     * Renders the board.
     *
     * @return string (X)HTML.
     */
    private function _renderBoard()
    {
        $result = '<table class="chess_board">';
        foreach ($this->_getRanks() as $rank) {
            $result .= $this->_renderRank($rank);
        }
        $result .= '</table>';
        return $result;
    }

    /**
     * Returns an array of ranks.
     *
     * @return array
     */
    private function _getRanks()
    {
        $ranks = range(8, 1, -1);
        if ($this->_flipped) {
            $ranks = array_reverse($ranks);
        }
        return $ranks;
    }

    /**
     * Renders a certain rank as table row.
     *
     * @param int $rank A rank.
     *
     * @return string (X)HTML.
     */
    private function _renderRank($rank)
    {
        $result = '<tr>';
        foreach ($this->_getFiles() as $file) {
            $result .= $this->_renderSquare($file, $rank);
        }
        $result .= '</tr>';
        return $result;
    }

    /**
     * Returns an array of files.
     *
     * @return array
     */
    private function _getFiles()
    {
        $files = array_map('chr', range(97, 104));
        if ($this->_flipped) {
            $files = array_reverse($files);
        }
        return $files;
    }

    /**
     * Renders a certain square.
     *
     * @param string $file A file.
     * @param int    $rank A rank.
     *
     * @return string (X)HTML.
     */
    private function _renderSquare($file, $rank)
    {
        $square = "$file$rank";
        $class = ((int) $rank + ord($file)) % 2 ? 'chess_light' : 'chess_dark';
        $result = '<td class="' . $class . '">';
        $move = $this->_game->getMove($this->_ply - 1);
        $moved = $move !== null && $move->isSourceOrDestination($square);
        if ($this->_position->hasPieceOn($square)) {
            $result .= $this->_renderPiece($this->_position->getPieceOn($square), $moved);
        } else {
            if ($moved) {
                $result .= '<span class="chess_move">&nbsp;</span>';
            } else {
                $result .= '&nbsp;';
            }
        }
        $result .= '</td>';
        return $result;
    }

    /**
     * Renders a piece.
     *
     * @param string $piece A piece.
     * @param bool   $moved Whether the piece is moved.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     */
    private function _renderPiece($piece, $moved)
    {
        global $pth;

        $src = $pth['folder']['plugins'] . 'chess/images/' . $piece . '.png';
        $class = $moved ? 'class="chess_move"' : '';
        return '<img ' . $class . ' src="' . $src . '" alt="' . $piece . '">';
    }

    /**
     * Renders the control panel.
     *
     * @return string (X)HTML.
     *
     * @global string The script name.
     * @global string The selected URL.
     */
    private function _renderControlPanel()
    {
        global $sn, $su;

        return '<form class="chess_control_panel" action="' . $sn
            . '#chess_view_' . $this->_game->getName() . '" method="'
            . 'get' . '">'
            . $this->_renderHiddenInput('selected', $su)
            . $this->_renderHiddenInput('chess_game', $this->_game->getName())
            . $this->_renderHiddenInput('chess_flipped', (string) (int) $this->_flipped)
            . $this->_renderButton('goto')
            . $this->_renderButton('start') . $this->_renderButton('previous')
            . $this->_renderPlyInput($this->_ply)
            . $this->_renderButton('next') . $this->_renderButton('end')
            . $this->_renderButton('flip')
            . '</form>';
    }

    /**
     * Renders the ply input field.
     *
     * @param int $value A ply.
     *
     * @return string (X)HTML.
     */
    private function _renderPlyInput($value)
    {
        return '<input type="text" name="chess_ply" value="' . $value . '">';
    }

    /**
     * Renders a hidden input field.
     *
     * @param string $name  A name attribute value.
     * @param string $value A value attribute value.
     *
     * @return string (X)HTML.
     */
    private function _renderHiddenInput($name, $value)
    {
        return '<input type="hidden" name="' . $name . '" value="' . $value . '">';
    }

    /**
     * Renders a button.
     *
     * @param string $which Which button to render.
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the plugins.
     */
    private function _renderButton($which)
    {
        global $plugin_tx;

        switch ($which) {
            case 'start':
                $value = 'start';
                $disabled = ($this->_ply == 0);
                break;
            case 'previous':
                $value = 'previous';
                $disabled = ($this->_ply == 0);
                break;
            case 'goto':
                $value = 'goto';
                $disabled = false;
                break;
            case 'next':
                $value = 'next';
                $disabled = ($this->_ply == $this->_game->getPlyCount());
                break;
            case 'end':
                $value = 'end';
                $disabled = ($this->_ply == $this->_game->getPlyCount());
                break;
            case 'flip':
                $value = 'flip';
                $disabled = false;
                break;
            default:
                $value = "";
                $disabled = true;
        }
        return '<button type="submit" name="chess_action" value="' . $value . '"'
            . ($disabled ? ' disabled="disabled"' : '') . '>'
            . $plugin_tx['chess']["label_$which"] . '</button>';
    }
}
