<?php

namespace Chess;

use ApprovalTests\Approvals;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Plib\CsrfProtector;
use Plib\FakeRequest;
use Plib\View;

class ImportCommandTest extends TestCase
{
    /** @var PgnImporter&MockObject */
    private $importer;

    /** @var CsrfProtector&Stub */
    private $csrfProtector;

    /** @var View */
    private $view;

    public function setUp(): void
    {
        $this->importer = $this->createMock(PgnImporter::class);
        $this->importer->expects($this->any())->method("findAll")->willReturn(["foo", "bar", "baz"]);
        $this->csrfProtector = $this->createStub(CsrfProtector::class);
        $this->csrfProtector->method("token")->willReturn("0123456789ABCDEF");
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["chess"]);
    }

    private function sut(): ImportCommand
    {
        return new ImportCommand($this->importer, $this->csrfProtector, $this->view);
    }

    public function testViewOnly(): void
    {
        $request = new FakeRequest(["url" => "http://example.com/?&action=plugin_text"]);
        $response = $this->sut()->execute($request);
        Approvals::verifyHtml($response->output());
    }

    public function testImport(): void
    {
        $this->importer->expects($this->once())->method("import")->with("foo");
        $this->csrfProtector->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=import",
            "post" => ["chess_game" => "foo"],
        ]);
        $this->sut()->execute($request);
    }

    public function testImportFailsForInvalidName(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=import",
            "post" => ["chess_game" => "foo!"],
        ]);
        $response = $this->sut()->execute($request);
        $this->assertStringContainsString("The name &quot;foo!&quot; is invalid", $response->output());
    }
}
