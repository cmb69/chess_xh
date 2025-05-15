<?php

namespace Chess;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;
use XH\CSRFProtection;

class ImportCommandTest extends TestCase
{
    /** @var ImportCommand */
    private $subject;

    /** @var PgnImporter */
    private $importer;

    /** @var ImportView&MockObject */
    private $importView;

    /** @var View */
    private $view;

    public function setUp(): void
    {
        global $admin, $_XH_csrfProtection, $plugin_tx;

        $admin = 'plugin_main';
        $_XH_csrfProtection = $this->getMockBuilder(CSRFProtection::class)
            ->disableOriginalConstructor()->getMock();
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
        $this->importer = $this->getMockBuilder(PgnImporter::class)
            ->disableOriginalConstructor()->getMock();
        $this->importView = $this->createMock(ImportView::class);
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["chess"]);
        $this->subject = new ImportCommand($this->importer, $this->importView, $this->view);
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
        $this->importer->expects($this->never())->method('import');
        $this->importView->expects($this->once())->method('render');
        $request = new FakeRequest();
        $this->subject->execute($request);
    }

    public function testImport(): void
    {
        global $action, $_XH_csrfProtection;

        $action = 'import';
        $_XH_csrfProtection->expects($this->once())->method('check');
        $this->importer->expects($this->once())->method('import')->with('foo');
        $this->importView->expects($this->once())->method('render');
        $request = new FakeRequest([
            "post" => ["chess_game" => "foo"],
        ]);
        $this->subject->execute($request);
    }

    public function testImportFailsForInvalidName(): void
    {
        global $o, $action, $_XH_csrfProtection;

        $o = '';
        $action = 'import';
        $_XH_csrfProtection->expects($this->once())->method('check');
        $this->importView->expects($this->once())->method('render');
        $request = new FakeRequest([
            "post" => ["chess_game" => "foo!"],
        ]);
        $this->subject->execute($request);
        $this->assertStringContainsString("The name &quot;foo!&quot; is invalid", $o);
    }
}
