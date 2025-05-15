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

class FrontEndControllerTest extends TestCase
{
    /** @var Controller */
    private $_subject;

    /** @var GameView */
    private $_gameView;

    /** @var object */
    private $_gameViewFactory;

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
        $this->_subject = new Controller();
        $this->_gameView = $this->getMockBuilder(GameView::class)
            ->disableOriginalConstructor()->getMock();
        $this->_gameViewFactory = $this->createFunctionMock(
            'Chess\GameView::make'
        );
    }

    public function testDispatchEmitsScript(): void
    {
        global $bjs;

        $bjs = '';
        $this->_subject->dispatch();
        $this->assertTag(
            array(
                'tag' => 'script',
                'attributes' => array(
                    'type' => 'text/javascript',
                    'src' => '../chess/chess.js'
                )
            ),
            $bjs
        );
    }

    public function testCantAccessBackEnd(): void
    {
        global $chess;

        $chess = 'true';
        $printPluginAdminMock = $this->createFunctionMock(
            'print_plugin_admin'
        );
        $printPluginAdminMock->expects($this->never());
        $this->_subject->dispatch();
    }

    public function testChess(): void
    {
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->_gameViewFactory->expects($this->once())
            ->will($this->returnValue($this->_gameView));

        $this->markTestSkipped();
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    public function testChessInvalidName(): void
    {
        $matcher = array(
            'tag' => 'p',
            'attributes' => array('class' => 'xh_fail'),
            'content' => 'The name "italian!" is invalid!'
        );
        $this->assertTag($matcher, $this->_subject->chess('italian!'));
    }

    public function testChessFlipped(): void
    {
        $_REQUEST['chess_flipped'] = '1';
        $_REQUEST['chess_action'] = 'flip';
        $this->_subject = new Controller();
        $this->_gameView = $this->getMockBuilder(GameView::class)
            ->disableOriginalConstructor()->getMock();
        $this->_gameViewFactory = $this->createFunctionMock(
            'Chess\GameView::make'
        );
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->_gameViewFactory->expects($this->once())
            ->with($this->anything(), $this->anything(), false)
            ->will($this->returnValue($this->_gameView));

        $this->markTestSkipped();
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    public function testChessStartAction(): void
    {
        $_REQUEST['chess_ply'] = '1';
        $_REQUEST['chess_action'] = 'start';
        $this->_subject = new Controller();
        $this->_gameView = $this->getMockBuilder(GameView::class)
            ->disableOriginalConstructor()->getMock();
        $this->_gameViewFactory = $this->createFunctionMock(
            'Chess\GameView::make'
        );
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->_gameViewFactory->expects($this->once())
            ->with($this->anything(), 0, $this->anything())
            ->will($this->returnValue($this->_gameView));
        $this->markTestSkipped();
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    public function testChessNextAction(): void
    {
        $_REQUEST['chess_action'] = 'next';
        $this->_subject = new Controller();
        $this->_gameView = $this->getMockBuilder(GameView::class)
            ->disableOriginalConstructor()->getMock();
        $this->_gameViewFactory = $this->createFunctionMock(
            'Chess\GameView::make'
        );
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->_gameViewFactory->expects($this->once())
            ->with($this->anything(), 1, $this->anything())
            ->will($this->returnValue($this->_gameView));
        $this->markTestSkipped();
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    public function testChessPreviousAction(): void
    {
        $_REQUEST['chess_ply'] = '1';
        $_REQUEST['chess_action'] = 'previous';
        $this->_subject = new Controller();
        $this->_gameView = $this->getMockBuilder(GameView::class)
            ->disableOriginalConstructor()->getMock();
        $this->_gameViewFactory = $this->createFunctionMock(
            'Chess\GameView::make'
        );
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->_gameViewFactory->expects($this->once())
            ->with($this->anything(), 0, $this->anything())
            ->will($this->returnValue($this->_gameView));
        $this->markTestSkipped();
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    public function testChessEndAction(): void
    {
        $this->markTestSkipped();
        $_REQUEST['chess_action'] = 'end';
        $this->_subject = new Controller();
        $this->_gameView = $this->getMockBuilder(GameView::class)
            ->disableOriginalConstructor()->getMock();
        $this->_gameViewFactory = $this->createFunctionMock(
            'Chess\GameView::make'
        );
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->_gameViewFactory->expects($this->once())
            ->with($this->anything(), 6, $this->anything())
            ->will($this->returnValue($this->_gameView));
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    /**
     * Test the chess method when failing.
     *
     * @return void
     */
    public function testChessFailure()
    {
        $matcher = array(
            'tag' => 'p',
            'attributes' => array('class' => 'xh_fail'),
            'content' => 'The chess file "foo" can\'t be loaded!'
        );
        $this->assertTag($matcher, $this->_subject->chess('foo'));
    }

    public function testChessFailureOldCMSimple(): void
    {
        $messageMock = $this->createFunctionMock("XH_message");
        $matcher = array(
            'tag' => 'p',
            'attributes' => array('class' => 'cmsimplecore_warning'),
            'content' => 'The chess file "foo" can\'t be loaded!'
        );
        $this->assertTag($matcher, $this->_subject->chess('foo'));
        $messageMock->restore();
    }

    public function testChessAjax(): void
    {
        $this->markTestSkipped();
        $_REQUEST['chess_ajax'] = '1';
        $_REQUEST['chess_game'] = 'italian';
        $this->_subject = new Controller();
        $this->_gameView = $this->getMockBuilder(GameView::class)
            ->disableOriginalConstructor()->getMock();
        $this->_gameViewFactory = $this->createFunctionMock(
            'Chess\GameView::make'
        );
        $header = $this->createFunctionMock('header');
        $header->expects($this->once())->with($this->stringContains('Content-Type'));
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->_gameViewFactory->expects($this->once())
            ->will($this->returnValue($this->_gameView));
        $exit = $this->createFunctionMock('XH_exit');
        $exit->expects($this->once());
        $this->expectOutputString('foo');
        $this->_subject->chess('italian');
    }

    public function testIrrelevantAjax(): void
    {
        $_REQUEST['chess_ajax'] = '1';
        $_REQUEST['chess_game'] = 'spanish';
        $this->_subject = new Controller();
        $this->_gameViewFactory = $this->createFunctionMock(
            'Chess\GameView::make'
        );
        $header = $this->createFunctionMock('header');
        $header->expects($this->never());
        $this->_gameViewFactory->expects($this->never());
        $this->expectOutputString('');
        $this->_subject->chess('italian');
    }
}
