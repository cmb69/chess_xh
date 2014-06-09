<?php

/**
 * Testing the back end functionality of the controllers.
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
require_once '../../cmsimple/adminfuncs.php';
require_once './classes/Service.php';
require_once './classes/Presentation.php';
require_once './tests/unit/TestBase.php';

/**
 * Testing the back end functionality of the controllers.
 *
 * @category Testing
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class BackEndControllerTest extends TestBase
{
    /**
     * The subject under test.
     *
     * @var Chess_Controller
     */
    private $_subject;

    /**
     * Sets up the test fixture.
     *
     * @return void
     *
     * @global string Whether the plugin administration is requested.
     */
    public function setUp()
    {
        global $chess;

        $this->defineConstant('XH_ADM', true);
        $chess = 'true';
        $this->_subject = new Chess_Controller();
        $printPluginAdminMock = new PHPUnit_Extensions_MockFunction(
            'print_plugin_admin', $this->_subject
        );
        $printPluginAdminMock->expects($this->once());
    }

    /**
     * Tests the info view.
     *
     * @return void
     *
     * @global string The value of the <var>admin</var> GP parameter.
     */
    public function testInfoView()
    {
        global $admin;

        $admin = '';
        $infoViewFactory = new PHPUnit_Extensions_MockStaticMethod(
            'Chess_InfoView::make', $this->_subject
        );
        $infoViewMock = $this->getMock('Chess_InfoView');
        $infoViewMock->expects($this->once())->method('render');
        $infoViewFactory->expects($this->once())
            ->will($this->returnValue($infoViewMock));
        $this->_subject->dispatch();
    }

    /**
     * Tests the import command.
     *
     * @return void
     *
     * @global string The value of the <var>admin</var> GP parameter.
     */
    public function testImportCommand()
    {
        global $admin;

        $admin = 'plugin_main';
        $importCommandFactory = new PHPUnit_Extensions_MockStaticMethod(
            'Chess_ImportCommand::make', $this->_subject
        );
        $importCommand = $this->getMockBuilder('Chess_ImportCommand')
            ->disableOriginalConstructor()->getMock();
        $importCommand->expects($this->once())->method('execute');
        $importCommandFactory->expects($this->once())->with($this->anything())
            ->will($this->returnValue($importCommand));
        $this->_subject->dispatch();
    }

    /**
     * Tests the default administration functionality.
     *
     * @return void
     *
     * @global string The value of the <var>admin</var> GP parameter.
     * @global string The value of the <var>action</var> GP parameter.
     */
    public function testDefaultAdministration()
    {
        global $admin, $action;

        $admin = 'plugin_config';
        $action = 'plugin_edit';
        $pluginAdminCommonMock = new PHPUnit_Extensions_MockFunction(
            'plugin_admin_common', $this->_subject
        );
        $pluginAdminCommonMock->expects($this->once())
            ->with($action, $admin, 'chess');
        $this->_subject->dispatch();
    }
}

?>
