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

class GameViewTest extends TestCase
{
    /** @var GameView */
    protected $subject;

    /** @var Game */
    private $_game;

    public function setUp(): void
    {
        global $pth, $sn, $su, $plugin_tx;

        $this->setConstant('CMSIMPLE_XH_VERSION', 'CMSimple_XH 1.6.2');
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
        $this->_game = new Game();
        $this->subject = new GameView($this->_game);
    }

    public function testFactory(): void
    {
        $this->assertInstanceOf(
            GameView::class, GameView::make(new Game())
        );
    }

    public function testRendersView(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'div',
                'id' => 'chess_view_',
                'attributes' => array('class' => 'chess_view')
            )
        );
    }

    public function testRendersTableWith8Rows(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'table',
                'attributes' => array('class' => 'chess_board'),
                'children' => array(
                    'only' => array('tag' => 'tr'),
                    'count' => 8
                )
            )
        );
    }

    public function testRendersRowWith8Cells(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'tr',
                'children' => array(
                    'only' => array('tag' => 'td'),
                    'count' => 8
                )
            )
        );
    }

    public function testRendersWhiteQueen(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'td',
                'attributes' => array('class' => 'chess_light'),
                'child' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'src' => './chess/images/wq.png',
                        'alt' => 'wq'
                    )
                )
            )
        );
    }

    public function testRendersBlackQueen(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'td',
                'attributes' => array('class' => 'chess_dark'),
                'child' => array(
                    'tag' => 'img',
                    'attributes' => array(
                        'src' => './chess/images/bq.png',
                        'alt' => 'bq'
                    )
                )
            )
        );
    }

    public function testRendersWhiteKingOnLightSquare(): void
    {
        $game = new Game();
        $game->move('e2', 'e4');
        $game->move('e7', 'e5');
        $game->move('e1', 'e2');
        $subject = new GameView($game, 2);
        $matcher = array(
            'tag' => 'td',
            'attributes' => array('class' => 'chess_dark'),
            'child' => array(
                'tag' => 'img',
                'attributes' => array(
                    'src' => './chess/images/wk.png',
                    'alt' => 'wk'
                )
            )
        );
        $this->assertTag($matcher, $subject->render());
    }

    public function testRendersEmptySquare(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'td',
                'content' => "\xC2\xA0"
            )
        );
    }

    public function testFlipped(): void
    {
        $this->subject = new GameView(new Game(), 0, true);
        $this->assertRenders(
            array(
                'tag' => 'table',
                'attributes' => array('class' => 'chess_board'),
                'children' => array(
                    'only' => array('tag' => 'tr'),
                    'count' => 8
                )
            )
        );
    }

    public function testRendersControlPanel(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'form',
                'attributes' => array(
                    'class' => 'chess_control_panel',
                    'action' => '/xh/#chess_view_',
                    'method' => 'get'
                )
            )
        );
    }

    public function testRendersControlPanelOldCMSimple(): void
    {
        $this->setConstant('CMSIMPLE_XH_VERSION', 'CMSimple 4.4.3');
        $this->assertRenders(
            array(
                'tag' => 'form',
                'attributes' => array(
                    'class' => 'chess_control_panel',
                    'action' => '/xh/#chess_view_',
                    'method' => 'post'
                )
            )
        );
    }

    public function testRendersSelectedInput(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'input',
                'attributes' => array(
                    'type' => 'hidden',
                    'name' => 'selected',
                    'value' => 'Chess'
                )
            )
        );
    }

    public function testRendersGameInput(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'input',
                'attributes' => array(
                    'type' => 'hidden',
                    'name' => 'chess_game',
                    'value' => ''
                )
            )
        );
    }

    public function testRendersFlippedInput(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'input',
                'attributes' => array(
                    'type' => 'hidden',
                    'name' => 'chess_flipped',
                    'value' => '0'
                ),
                'parent' => array('tag' => 'form')
            )
        );
    }

    public function testRendersPlyInput(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'input',
                'attributes' => array(
                    'type' => 'text',
                    'name' => 'chess_ply',
                    'value' => '0'
                ),
                'parent' => array('tag' => 'form')
            )
        );
    }

    public function testRendersPlyInputDoesntTopMax(): void
    {
        $_REQUEST['chess_action'] = 'goto';
        $_REQUEST['chess_ply'] = '23';
        $this->assertRenders(
            array(
                'tag' => 'input',
                'attributes' => array(
                    'type' => 'text',
                    'name' => 'chess_ply',
                    'value' => '0'
                ),
                'parent' => array('tag' => 'form')
            )
        );
    }

    public function testRendersFlipButton(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'button',
                'attributes' => array(
                    'name' => 'chess_action',
                    'value' => 'flip'
                ),
                'content' => 'Flip',
                'parent' => array('tag' => 'form')
            )
        );
    }

    public function testRendersStartButton(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'button',
                'attributes' => array(
                    'name' => 'chess_action',
                    'value' => 'start',
                    'disabled' => 'disabled'
                ),
                'content' => 'Start',
                'parent' => array('tag' => 'form')
            )
        );
    }

    public function testRendersPreviousButton(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'button',
                'attributes' => array(
                    'name' => 'chess_action',
                    'value' => 'previous',
                    'disabled' => 'disabled'
                ),
                'content' => 'Previous',
                'parent' => array('tag' => 'form')
            )
        );
    }

    public function testRendersGotoButton(): void
    {
        $this->_game->move('e2', 'e4');
        $this->assertRenders(
            array(
                'tag' => 'button',
                'attributes' => array(
                    'name' => 'chess_action',
                    'value' => 'goto'
                ),
                'content' => 'Go to',
                'parent' => array('tag' => 'form')
            )
        );
    }

    public function testRendersNextButton(): void
    {
        $this->_game->move('e2', 'e4');
        $this->assertRenders(
            array(
                'tag' => 'button',
                'attributes' => array(
                    'name' => 'chess_action',
                    'value' => 'next'
                ),
                'content' => 'Next',
                'parent' => array('tag' => 'form')
            )
        );
    }

    public function testRendersEndButton(): void
    {
        $this->_game->move('e2', 'e4');
        $this->_game->move('e7', 'e5');
        $this->assertRenders(
            array(
                'tag' => 'button',
                'attributes' => array(
                    'name' => 'chess_action',
                    'value' => 'end'
                ),
                'content' => 'End',
                'parent' => array('tag' => 'form')
            )
        );
    }
}
