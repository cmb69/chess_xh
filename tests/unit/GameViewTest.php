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

require_once '../../cmsimple/functions.php';
require_once './classes/Domain.php';
require_once './classes/Presentation.php';
require_once './tests/unit/TestBase.php';

/**
 * Testing the game views.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class GameViewTest extends TestBase
{
    /**
     * The test subject.
     *
     * @var Chess_GameView
     */
    protected $subject;

    /**
     * The game.
     *
     * @var Chess_Game
     */
    private $_game;

    /**
     * Sets up the test fixture.
     *
     * @return void
     *
     * @global array  The paths of system files and folders.
     * @global string The site name.
     * @global string The selected URL.
     * @global array  The localization of the plugins.
     */
    public function setUp()
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
        $this->_game = new Chess_Game();
        $this->subject = new Chess_GameView($this->_game);
    }

    /**
     * Tests the factory.
     *
     * @return void
     */
    public function testFactory()
    {
        $this->assertInstanceOf(
            'Chess_GameView', Chess_GameView::make(new Chess_Game())
        );
    }

    /**
     * Tests that the view is rendered.
     *
     * @return void
     */
    public function testRendersView()
    {
        $this->assertRenders(
            array(
                'tag' => 'div',
                'attributes' => array('class' => 'chess_view')
            )
        );
    }

    /**
     * Tests that a table with 8 rows is rendered.
     *
     * @return void
     */
    public function testRendersTableWith8Rows()
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

    /**
     * Tests that a row with 8 cells is rendered.
     *
     * @return void
     */
    public function testRendersRowWith8Cells()
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

    /**
     * Tests that a white queen is rendered.
     *
     * @return void
     */
    public function testRendersWhiteQueen()
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

    /**
     * Tests that a black queen is rendered.
     *
     * @return void
     */
    public function testRendersBlackQueen()
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

    /**
     * Tests that a white king is rendered on a light square.
     *
     * @return void
     */
    public function testRendersWhiteKingOnLightSquare()
    {
        $game = new Chess_Game();
        $game->move('e2', 'e4');
        $game->move('e7', 'e5');
        $game->move('e1', 'e2');
        $subject = new Chess_GameView($game, 2);
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

    /**
     * Tests that an empty square is renderd.
     *
     * @return void
     */
    public function testRendersEmptySquare()
    {
        $this->assertRenders(
            array(
                'tag' => 'td',
                'content' => "\xC2\xA0"
            )
        );
    }

    /**
     * Tests the flipped chess board.
     *
     * @return void
     */
    public function testFlipped()
    {
        $this->subject = new Chess_GameView(new Chess_Game(), null, true);
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

    /**
     * Tests that a control panel is rendered.
     *
     * @return void
     */
    public function testRendersControlPanel()
    {
        $this->assertRenders(
            array(
                'tag' => 'form',
                'attributes' => array(
                    'class' => 'chess_control_panel',
                    'action' => '/xh/',
                    'method' => 'get'
                ),
                'child' => array(
                    'tag' => 'input',
                    'attributes' => array(
                        'type' => 'hidden',
                        'name' => 'selected',
                        'value' => 'Chess'
                    )
                )
            )
        );
    }

    /**
     * Tests that the flip input field is rendered.
     *
     * @return void
     */
    public function testRendersFlippedInput()
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

    /**
     * Tests that the ply input field is rendered.
     *
     * @return void
     */
    public function testRendersPlyInput()
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

    /**
     * Tests that the ply input field doesn't top the maximum.
     *
     * @return void
     */
    public function testRendersPlyInputDoesntTopMax()
    {
        $_GET['chess_action'] = 'goto';
        $_GET['chess_ply'] = '23';
        $this->assertRenders(
            array(
                'tag' => 'input',
                'attributes' => array(
                    'type' => 'text',
                    'name' => 'chess_ply',
                    //'value' => '23'
                ),
                'parent' => array('tag' => 'form')
            )
        );
    }

    /**
     * Tests that the flip button is rendered.
     *
     * @return void
     */
    public function testRendersFlipButton()
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

    /**
     * Tests that the start button is rendered.
     *
     * @return void
     */
    public function testRendersStartButton()
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

    /**
     * Tests that the previous button is rendered.
     *
     * @return void
     */
    public function testRendersPreviousButton()
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

    /**
     * Tests that the "go to" button is rendered.
     *
     * @return void
     */
    public function testRendersGotoButton()
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

    /**
     * Tests that the next button is rendered.
     *
     * @return void
     */
    public function testRendersNextButton()
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

    /**
     * Tests that the end button is rendered.
     *
     * @return void
     */
    public function testRendersEndButton()
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

    /**
     * Tests that the script is rendered.
     *
     * @return void
     *
     * @global string The (X)HTML to insert at the bottom of the body.
     */
    public function testRendersScript()
    {
        global $bjs;

        $bjs = '';
        $this->subject->render();
        $this->assertTag(
            array(
                'tag' => 'script',
                'attributes' => array(
                    'type' => 'text/javascript',
                    'src' => './chess/chess.js'
                )
            ),
            $bjs
        );
    }
}

?>
