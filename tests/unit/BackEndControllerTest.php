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

namespace Chess;

class BackEndControllerTest extends TestCase
{
    /** @var Controller */
    private $_subject;

    private $factory;

    public function setUp(): void
    {
        global $chess, $plugin_tx;

        $this->setConstant('XH_ADM', true);
        $chess = 'true';
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
        $this->factory = $this->createStub(Factory::class);
        $this->_subject = new Controller($this->factory);
        $printPluginAdminMock = $this->createFunctionMock(
            'print_plugin_admin'
        );
        $printPluginAdminMock->expects($this->once());
    }

    public function testInfoView(): void
    {
        global $admin, $pth;

        $fmock = $this->createFunctionMock("XH_wantsPluginAdministration");
        $fmock->expects($this->once())->willReturn(true);
        $admin = '';
        $pth = ["folder" => ["plugins" => ""]];
        $infoViewMock = $this->createMock(InfoView::class);
        $infoViewMock->expects($this->once())->method('render');
        $this->factory->method("makeInfoView")->willReturn($infoViewMock);
        $this->_subject->dispatch();
        $fmock->restore();
    }

    public function testImportCommand(): void
    {
        global $admin, $pth;

        $fmock = $this->createFunctionMock("XH_wantsPluginAdministration");
        $fmock->expects($this->once())->willReturn(true);
        $admin = 'plugin_main';
        $pth = ["folder" => ["plugins" => ""]];
        $importCommand = $this->getMockBuilder(ImportCommand::class)
            ->disableOriginalConstructor()->getMock();
        $this->factory->method("makeImportCommand")->willReturn($importCommand);
        $importCommand->expects($this->once())->method('execute');
        $this->_subject->dispatch();
        $fmock->restore();
    }

    public function testDefaultAdministration(): void
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
