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

/**
 * Testing the back end functionality of the controllers.
 *
 * @category Testing
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class BackEndControllerTest extends TestCase
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
    public function setUp(): void
    {
        global $chess, $plugin_tx;

        $this->setConstant('XH_ADM', true);
        $chess = 'true';
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
        $this->_subject = new Chess_Controller();
        $printPluginAdminMock = $this->createFunctionMock(
            'print_plugin_admin'
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
        global $admin, $pth;

        $this->markTestSkipped();
        $admin = '';
        $pth = ["folder" => ["plugins" => ""]];
        $infoViewFactory = $this->createFunctionMock(
            'Chess_InfoView::make'
        );
        $infoViewMock = $this->createMock(Chess_InfoView::class);
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
        global $admin, $pth;

        $this->markTestSkipped();
        $admin = 'plugin_main';
        $pth = ["folder" => ["plugins" => ""]];
        $importCommandFactory = $this->createFunctionMock(
            'Chess_ImportCommand::make'
        );
        $importCommand = $this->getMockBuilder(Chess_ImportCommand::class)
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
        global $admin, $action, $pth;

        $admin = 'plugin_config';
        $action = 'plugin_edit';
        $pth = ["folder" => ["plugins" => ""]];
        $pluginAdminCommonMock = $this->createFunctionMock(
            'plugin_admin_common'
        );
        $this->createFunctionMock("XH_wantsPluginAdministration")->expects($this->once())->willReturn(true);
        $pluginAdminCommonMock->expects($this->once());
        $this->_subject->dispatch();
    }
}

?>
