<?php

/**
 * Testing the import commands.
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

class ImportCommandTest extends TestCase
{
    /** @var ImportCommand */
    private $_subject;

    /** @var PgnImporter */
    private $_importer;

    /** @var object */
    private $_importViewFactory;

    /** @var ImportView */
    private $_importView;

    public function setUp(): void
    {
        global $admin, $_XH_csrfProtection, $plugin_tx;

        $this->setConstant('XH_ADM', true);
        $admin = 'plugin_main';
        $_XH_csrfProtection = $this->getMockBuilder(XH\CSRFProtection::class)
            ->disableOriginalConstructor()->getMock();
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
        $this->_importer = $this->getMockBuilder(PgnImporter::class)
            ->disableOriginalConstructor()->getMock();
        $this->_subject = new ImportCommand($this->_importer);
        $this->_importViewFactory = $this->createFunctionMock(
            '\Chess\ImportView::make'
        );
        $this->_importViewFactory = $this->createFunctionMock('Chess\ImportView::make');
        $this->_importView = $this->getMockBuilder(ImportView::class)
            ->disableOriginalConstructor()->getMock();
    }

    public function testFactory(): void
    {
        $this->assertInstanceOf(
            ImportCommand::class, ImportCommand::make($this->_importer)
        );
    }

    public function testViewOnly(): void
    {
        global $action;

        $this->markTestSkipped();
        $action = 'plugin_text';
        $this->_importer->expects($this->never())->method('import');
        $this->_importView->expects($this->once())->method('render');
        $this->_importViewFactory->expects($this->once())->with($this->anything())
            ->will($this->returnValue($this->_importView));
        $this->_subject->execute();
    }

    public function testImport(): void
    {
        global $action, $_XH_csrfProtection;

        $this->markTestSkipped();
        $action = 'import';
        $_POST['chess_game'] = 'foo';
        $_XH_csrfProtection->expects($this->once())->method('check');
        $this->_importer->expects($this->once())->method('import')->with('foo');
        $this->_importView->expects($this->once())->method('render');
        $this->_importViewFactory->expects($this->once())->with($this->anything())
            ->will($this->returnValue($this->_importView));
        $this->_subject->execute();
    }

    public function testImportFailsForInvalidName(): void
    {
        global $o, $action, $_XH_csrfProtection;

        $this->markTestSkipped();
        $o = '';
        $action = 'import';
        $_POST['chess_game'] = 'foo!';
        $_XH_csrfProtection->expects($this->once())->method('check');
        $this->_importView->expects($this->once())->method('render');
        $this->_importViewFactory->expects($this->once())->with($this->anything())
            ->will($this->returnValue($this->_importView));
        $this->_subject->execute();
        $this->assertTag(
            array(
                'tag' => 'p',

            ),
            $o
        );
    }
}
