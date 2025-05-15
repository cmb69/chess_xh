<?php

namespace Chess;

use ApprovalTests\Approvals;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Plib\CsrfProtector;
use Plib\FakeRequest;
use Plib\View;

class ImportCommandTest extends TestCase
{
    /** @var ImportCommand */
    private $subject;

    /** @var PgnImporter */
    private $importer;

    /** @var CsrfProtector&Stub */
    private $csrfProtector;

    /** @var View */
    private $view;

    public function setUp(): void
    {
        $this->importer = $this->getMockBuilder(PgnImporter::class)
            ->disableOriginalConstructor()->getMock();
        $this->importer->expects($this->any())->method('findAll')
            ->will($this->returnValue(array('foo', 'bar', 'baz')));
        $this->csrfProtector = $this->createStub(CsrfProtector::class);
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["chess"]);
        $this->subject = new ImportCommand($this->importer, $this->csrfProtector, $this->view);
    }

    public function testViewOnly(): void
    {
        $this->importer->expects($this->never())->method('import');
        $request = new FakeRequest(["url" => "http://example.com/?&action=plugin_text"]);
        $response = $this->subject->execute($request);
        Approvals::verifyHtml($response->output());
    }

    public function testImport(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $this->importer->expects($this->once())->method('import')->with('foo');
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=import",
            "post" => ["chess_game" => "foo"],
        ]);
        $this->subject->execute($request);
    }

    public function testImportFailsForInvalidName(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=import",
            "post" => ["chess_game" => "foo!"],
        ]);
        $response = $this->subject->execute($request);
        $this->assertStringContainsString("The name &quot;foo!&quot; is invalid", $response->output());
    }
}
