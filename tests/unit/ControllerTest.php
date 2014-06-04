<?php

/**
 * Testing the controllers.
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
require_once './classes/Presentation.php';

/**
 * Testing the controllers.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class ControllerTest extends PHPUnit_Framework_TestCase
{
    private $_subject;

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

        $pth = array(
            'folder' => array('plugins' => '../')
        );
        $plugin_tx = array(
            'chess' => array(
                'message_load_error' => 'The chess file "%s" can\'t be loaded!'
            )
        );
        $this->_subject = new Chess_Controller();
    }

    /**
     * Tests the chess method.
     *
     * @return void
     */
    public function testChess()
    {
        $gameViewMock = $this->getMockBuilder('Chess_GameView')
            ->disableOriginalConstructor()->getMock();
        $gameViewMock->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $gameViewMockFactory = new PHPUnit_Extensions_MockStaticMethod(
            'Chess_GameView::make', $this->_subject
        );
        $gameViewMockFactory->expects($this->once())
            ->will($this->returnValue($gameViewMock));
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
}

?>
