<?php

/**
 * Testing the front end functionality of the controllers.
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

use Plib\View;

class ChessControllerTest extends TestCase
{
    /** @var Controller */
    private $_subject;

    /** @var GameView */
    private $_gameView;

    /** @var object */
    private $_gameViewFactory;

    /** @var View */
    private $view;

    public function setUp(): void
    {
        global $pth, $plugin_tx;

        $this->setConstant('XH_ADM', false);
        $pth = array(
            'folder' => array('plugins' => '../')
        );
        $plugin_tx = array(
            'chess' => array(
                'message_invalid_name' => 'The name "%s" is invalid!',
                'message_load_error' => 'The chess file "%s" can\'t be loaded!'
            )
        );
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
        $this->createFunctionMock('XH_exit');
        $this->_gameView = $this->getMockBuilder(GameView::class)
            ->disableOriginalConstructor()->getMock();
        $this->_gameViewFactory = $this->createStub(Factory::class);
        $this->_gameViewFactory->method("makeGameView")->willReturn($this->_gameView);
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["chess"]);
        $this->_subject = new ChessController($this->_gameViewFactory, $this->view);
    }

    public function testChess(): void
    {
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    public function testChessInvalidName(): void
    {
        $this->assertStringContainsString(
            "The name &quot;italian!&quot; is invalid",
            $this->_subject->chess('italian!')
        );
    }

    public function testChessFlipped(): void
    {
        $_REQUEST['chess_flipped'] = '1';
        $_REQUEST['chess_action'] = 'flip';
        $this->_subject = new ChessController($this->_gameViewFactory, $this->view);
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    public function testChessStartAction(): void
    {
        $_REQUEST['chess_ply'] = '1';
        $_REQUEST['chess_action'] = 'start';
        $this->_subject = new ChessController($this->_gameViewFactory, $this->view);
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    public function testChessNextAction(): void
    {
        $_REQUEST['chess_action'] = 'next';
        $this->_subject = new ChessController($this->_gameViewFactory, $this->view);
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    public function testChessPreviousAction(): void
    {
        $_REQUEST['chess_ply'] = '1';
        $_REQUEST['chess_action'] = 'previous';
        $this->_gameView = $this->getMockBuilder(GameView::class)
            ->disableOriginalConstructor()->getMock();
        $factory = $this->createStub(Factory::class);
        $factory->method("makeGameView")->willReturn($this->_gameView);
        $this->_subject = new ChessController($factory, $this->view);
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    public function testChessEndAction(): void
    {
        $_REQUEST['chess_action'] = 'end';
        $this->_gameView = $this->getMockBuilder(GameView::class)
            ->disableOriginalConstructor()->getMock();
        $factory = $this->createStub(Factory::class);
        $factory->method("makeGameView")->willReturn($this->_gameView);
        $this->_subject = new ChessController($factory, $this->view);
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    public function testChessFailure(): void
    {
        $this->assertStringContainsString(
            "The chess file &quot;foo&quot; can't be loaded!",
            $this->_subject->chess('foo')
        );
    }

    public function testChessAjax(): void
    {
        $_REQUEST['chess_ajax'] = '1';
        $_REQUEST['chess_game'] = 'italian';
        $this->_gameView = $this->getMockBuilder(GameView::class)
            ->disableOriginalConstructor()->getMock();
        $factory = $this->createStub(Factory::class);
        $factory->method("makeGameView")->willReturn($this->_gameView);
        $this->_subject = new ChessController($factory, $this->view);
        $header = $this->createFunctionMock('header');
        $header->expects($this->once())->with($this->stringContains('Content-Type'));
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $exit = $this->createFunctionMock('XH_exit');
        $exit->expects($this->once());
        $this->expectOutputString('foo');
        $this->_subject->chess('italian');
    }

    public function testIrrelevantAjax(): void
    {
        $_REQUEST['chess_ajax'] = '1';
        $_REQUEST['chess_game'] = 'spanish';
        $factory = $this->createStub(Factory::class);
        $this->_subject = new ChessController($factory, $this->view);
        $header = $this->createFunctionMock('header');
        $header->expects($this->never());
        $this->expectOutputString('');
        $this->_subject->chess('italian');
    }

    public function testEmitsScript(): void
    {
        global $bjs, $pth;

        $pth = ["folder" => ["plugins" => "../"]];
        $bjs = '';
        $this->_subject->chess('italian');
        $this->assertSame('<script type="text/javascript" src="../chess/chess.js"></script>', $bjs);
    }
}
