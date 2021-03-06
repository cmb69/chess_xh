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

require_once './vendor/autoload.php';
require_once '../../cmsimple/functions.php';
require_once './classes/Domain.php';
require_once './classes/Presentation.php';
require_once './tests/unit/TestBase.php';

/**
 * Testing the front end functionality of the controllers.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class FrontEndControllerTest extends TestBase
{
    /**
     * The test subject.
     *
     * @var Chess_Controller
     */
    private $_subject;

    /**
     * The game view.
     *
     * @var Chess_GameView
     */
    private $_gameView;

    /**
     * The game view factory mock.
     *
     * @var object
     */
    private $_gameViewFactory;

    /**
     * Sets up the test fixture.
     *
     * @return void
     *
     * @global array The paths of system files and folders.
     */
    public function setUp()
    {
        global $pth, $plugin_tx;

        $this->defineConstant('XH_ADM', false);
        $pth = array(
            'folder' => array('plugins' => '../')
        );
        $plugin_tx = array(
            'chess' => array(
                'message_invalid_name' => 'The name "%s" is invalid!',
                'message_load_error' => 'The chess file "%s" can\'t be loaded!'
            )
        );
        new PHPUnit_Extensions_MockFunction('XH_exit', $this->_subject);
        $this->_subject = new Chess_Controller();
        $this->_gameView = $this->getMockBuilder('Chess_GameView')
            ->disableOriginalConstructor()->getMock();
        $this->_gameViewFactory = new PHPUnit_Extensions_MockStaticMethod(
            'Chess_GameView::make', $this->_subject
        );
    }

    /**
     * Tests that the script element is emitted.
     *
     * @return void
     *
     * @global string The (X)HTML to insert at the bottom of the body.
     */
    public function testDispatchEmitsScript()
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

    /**
     * Tests that the back end can't be accessed.
     *
     * @return void
     *
     * @global string Whether the plugin administration is requested.
     */
    public function testCantAccessBackEnd()
    {
        global $chess;

        $chess = 'true';
        $printPluginAdminMock = new PHPUnit_Extensions_MockFunction(
            'print_plugin_admin', $this->_subject
        );
        $printPluginAdminMock->expects($this->never());
        $this->_subject->dispatch();
    }

    /**
     * Tests the chess method.
     *
     * @return void
     */
    public function testChess()
    {
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->_gameViewFactory->expects($this->once())
            ->will($this->returnValue($this->_gameView));
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    /**
     * Tests chess() with an invalid name.
     *
     * @return void
     */
    public function testChessInvalidName()
    {
        $matcher = array(
            'tag' => 'p',
            'attributes' => array('class' => 'xh_fail'),
            'content' => 'The name "italian!" is invalid!'
        );
        $this->assertTag($matcher, $this->_subject->chess('italian!'));
    }

    /**
     * Tests the chess method for a board flipped twice.
     *
     * @return void
     */
    public function testChessFlipped()
    {
        $_REQUEST['chess_flipped'] = '1';
        $_REQUEST['chess_action'] = 'flip';
        $this->_subject = new Chess_Controller();
        $this->_gameView = $this->getMockBuilder('Chess_GameView')
            ->disableOriginalConstructor()->getMock();
        $this->_gameViewFactory = new PHPUnit_Extensions_MockStaticMethod(
            'Chess_GameView::make', $this->_subject
        );
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->_gameViewFactory->expects($this->once())
            ->with($this->anything(), $this->anything(), false)
            ->will($this->returnValue($this->_gameView));
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    /**
     * Tests the chess method for the "start" action.
     *
     * @return void
     */
    public function testChessStartAction()
    {
        $_REQUEST['chess_ply'] = '1';
        $_REQUEST['chess_action'] = 'start';
        $this->_subject = new Chess_Controller();
        $this->_gameView = $this->getMockBuilder('Chess_GameView')
            ->disableOriginalConstructor()->getMock();
        $this->_gameViewFactory = new PHPUnit_Extensions_MockStaticMethod(
            'Chess_GameView::make', $this->_subject
        );
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->_gameViewFactory->expects($this->once())
            ->with($this->anything(), 0, $this->anything())
            ->will($this->returnValue($this->_gameView));
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    /**
     * Tests the chess method for the "next" action.
     *
     * @return void
     */
    public function testChessNextAction()
    {
        $_REQUEST['chess_action'] = 'next';
        $this->_subject = new Chess_Controller();
        $this->_gameView = $this->getMockBuilder('Chess_GameView')
            ->disableOriginalConstructor()->getMock();
        $this->_gameViewFactory = new PHPUnit_Extensions_MockStaticMethod(
            'Chess_GameView::make', $this->_subject
        );
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->_gameViewFactory->expects($this->once())
            ->with($this->anything(), 1, $this->anything())
            ->will($this->returnValue($this->_gameView));
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    /**
     * Tests the chess method for the "previous" action.
     *
     * @return void
     */
    public function testChessPreviousAction()
    {
        $_REQUEST['chess_ply'] = '1';
        $_REQUEST['chess_action'] = 'previous';
        $this->_subject = new Chess_Controller();
        $this->_gameView = $this->getMockBuilder('Chess_GameView')
            ->disableOriginalConstructor()->getMock();
        $this->_gameViewFactory = new PHPUnit_Extensions_MockStaticMethod(
            'Chess_GameView::make', $this->_subject
        );
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->_gameViewFactory->expects($this->once())
            ->with($this->anything(), 0, $this->anything())
            ->will($this->returnValue($this->_gameView));
        $this->assertEquals('foo', $this->_subject->chess('italian'));
    }

    /**
     * Tests the chess method for the "end" action.
     *
     * @return void
     */
    public function testChessEndAction()
    {
        $_REQUEST['chess_action'] = 'end';
        $this->_subject = new Chess_Controller();
        $this->_gameView = $this->getMockBuilder('Chess_GameView')
            ->disableOriginalConstructor()->getMock();
        $this->_gameViewFactory = new PHPUnit_Extensions_MockStaticMethod(
            'Chess_GameView::make', $this->_subject
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

    /**
     * Test the chess method when failing under CMSimple_XH < 1.6.
     *
     * @return void
     */
    public function testChessFailureOldCMSimple()
    {
        runkit_function_rename('XH_message', 'XH_message_ORIG');
        $matcher = array(
            'tag' => 'p',
            'attributes' => array('class' => 'cmsimplecore_warning'),
            'content' => 'The chess file "foo" can\'t be loaded!'
        );
        $this->assertTag($matcher, $this->_subject->chess('foo'));
        runkit_function_rename('XH_message_ORIG', 'XH_message');
    }

    /**
     * Tests the chess() for Ajax.
     *
     * @return void
     */
    public function testChessAjax()
    {
        $_REQUEST['chess_ajax'] = '1';
        $_REQUEST['chess_game'] = 'italian';
        $this->_subject = new Chess_Controller();
        $this->_gameView = $this->getMockBuilder('Chess_GameView')
            ->disableOriginalConstructor()->getMock();
        $this->_gameViewFactory = new PHPUnit_Extensions_MockStaticMethod(
            'Chess_GameView::make', $this->_subject
        );
        $header = new PHPUnit_Extensions_MockFunction('header', $this->_subject);
        $header->expects($this->once())->with($this->stringContains('Content-Type'));
        $this->_gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->_gameViewFactory->expects($this->once())
            ->will($this->returnValue($this->_gameView));
        $exit = new PHPUnit_Extensions_MockFunction('XH_exit', $this->_subject);
        $exit->expects($this->once());
        $this->expectOutputString('foo');
        $this->_subject->chess('italian');
    }

    /**
     * Tests calling chess() with Ajax on irrelevant game.
     *
     * @return void
     */
    public function testIrrelevantAjax()
    {
        $_REQUEST['chess_ajax'] = '1';
        $_REQUEST['chess_game'] = 'spanish';
        $this->_subject = new Chess_Controller();
        $this->_gameViewFactory = new PHPUnit_Extensions_MockStaticMethod(
            'Chess_GameView::make', $this->_subject
        );
        $header = new PHPUnit_Extensions_MockFunction('header', $this->_subject);
        $header->expects($this->never());
        $this->_gameViewFactory->expects($this->never());
        $this->expectOutputString('');
        $this->_subject->chess('italian');
    }
}

?>
