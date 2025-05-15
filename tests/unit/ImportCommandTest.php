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

use PHPUnit\Framework\MockObject\MockObject;
use XH\CSRFProtection;

class ImportCommandTest extends TestCase
{
    /** @var ImportCommand */
    private $_subject;

    /** @var PgnImporter */
    private $_importer;

    /** @var ImportView&MockObject */
    private $_importView;

    public function setUp(): void
    {
        global $admin, $_XH_csrfProtection, $plugin_tx;

        $this->setConstant('XH_ADM', true);
        $admin = 'plugin_main';
        $_XH_csrfProtection = $this->getMockBuilder(CSRFProtection::class)
            ->disableOriginalConstructor()->getMock();
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
        $this->_importer = $this->getMockBuilder(PgnImporter::class)
            ->disableOriginalConstructor()->getMock();
        $this->_importView = $this->createMock(ImportView::class);
        $this->_subject = new ImportCommand($this->_importer, $this->_importView);
    }

    // public function testFactory(): void
    // {
    //     $this->assertInstanceOf(
    //         ImportCommand::class, ImportCommand::make($this->_importer)
    //     );
    // }

    public function testViewOnly(): void
    {
        global $action;

        $action = 'plugin_text';
        $this->_importer->expects($this->never())->method('import');
        $this->_importView->expects($this->once())->method('render');
        $this->_subject->execute();
    }

    public function testImport(): void
    {
        global $action, $_XH_csrfProtection;

        $action = 'import';
        $_POST['chess_game'] = 'foo';
        $_XH_csrfProtection->expects($this->once())->method('check');
        $this->_importer->expects($this->once())->method('import')->with('foo');
        $this->_importView->expects($this->once())->method('render');
        $this->_subject->execute();
    }

    public function testImportFailsForInvalidName(): void
    {
        global $o, $action, $_XH_csrfProtection;

        $o = '';
        $action = 'import';
        $_POST['chess_game'] = 'foo!';
        $_XH_csrfProtection->expects($this->once())->method('check');
        $this->_importView->expects($this->once())->method('render');
        $this->_subject->execute();
        $this->assertSame('<p class="xh_fail">The name &quot;foo!&quot; is invalid</p>', $o);
    }
}
