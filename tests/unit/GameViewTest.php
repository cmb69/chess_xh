<?php

/**
 * Testing the game views.
 *
 * PHP version 5
 *
 * @category  Testing
 * @package   Chess
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2014 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Chess_XH
 */

namespace Chess;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;

class GameViewTest extends TestCase
{
    /** @var GameView */
    protected $subject;

    /** @var Game */
    private $game;

    public function setUp(): void
    {
        global $pth, $sn, $su, $plugin_tx;

        $pth = array(
            'folder' => array('plugins' => './')
        );
        $sn = '/xh/';
        $su = 'Chess';
        $plugin_tx = array(
            'chess' => array(
                'label_flip' => 'Flip',
                'label_start' => 'Start',
                'label_next' => 'Next',
                'label_goto' => 'Go to',
                'label_previous' => 'Previous',
                'label_end' => 'End'
            )
        );
        $this->game = new Game();
        $this->subject = new GameView($this->game);
    }

    public function testFactory(): void
    {
        $this->assertInstanceOf(GameView::class, GameView::make(new Game()));
    }

    public function testRendersView(): void
    {
        Approvals::verifyHtml($this->subject->render());
    }

    public function testRendersWhiteKingOnLightSquare(): void
    {
        $game = new Game();
        $game->move('e2', 'e4');
        $game->move('e7', 'e5');
        $game->move('e1', 'e2');
        $subject = new GameView($game, 2);
        Approvals::verifyHtml($subject->render());
    }

    public function testFlipped(): void
    {
        $this->subject = new GameView(new Game(), 0, true);
        Approvals::verifyHtml($this->subject->render());
    }

    public function testRendersPlyInputDoesntTopMax(): void
    {
        $_REQUEST['chess_action'] = 'goto';
        $_REQUEST['chess_ply'] = '23';
        Approvals::verifyHtml($this->subject->render());
    }
}
