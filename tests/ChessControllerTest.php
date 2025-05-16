<?php

namespace Chess;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\DocumentStore;
use Plib\FakeRequest;
use Plib\View;

class ChessControllerTest extends TestCase
{
    /** @var View */
    private $view;

    public function setUp(): void
    {
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["chess"]);
    }

    private function sut(): ChessController
    {
        return new ChessController("./", new DocumentStore(__DIR__ . "/"), $this->view);
    }

    public function testChess(): void
    {
        $request = new FakeRequest();
        $response = $this->sut()("italian", $request);
        Approvals::verifyHtml($response->output());
    }

    public function testChessInvalidName(): void
    {
        $request = new FakeRequest();
        $response = $this->sut()("italian!", $request);
        $this->assertStringContainsString("The name &quot;italian!&quot; is invalid", $response->output());
    }

    public function testChessFlipped(): void
    {
        $request = new FakeRequest(["url" => "http://example.com/?&chess_action=flip"]);
        $response = $this->sut()("italian", $request);
        Approvals::verifyHtml($response->output());
    }

    public function testChessStartAction(): void
    {
        $request = new FakeRequest(["url" => "http://example.com/?&chess_action=start&chess_ply=1"]);
        $response = $this->sut()("italian", $request);
        Approvals::verifyHtml($response->output());
    }

    public function testChessNextAction(): void
    {
        $request = new FakeRequest(["url" => "http://example.com/?&chess_action=next"]);
        $response = $this->sut()("italian", $request);
        Approvals::verifyHtml($response->output());
    }

    public function testChessPreviousAction(): void
    {
        $request = new FakeRequest(["url" => "http://example.com/?&chess_action=previous&chess_ply=1"]);
        $response = $this->sut()("italian", $request);
        Approvals::verifyHtml($response->output());
    }

    public function testChessEndAction(): void
    {
        $request = new FakeRequest(["url" => "http://example.com/?&chess_action=end"]);
        $response = $this->sut()("italian", $request);
        Approvals::verifyHtml($response->output());
    }

    public function testChessFailure(): void
    {
        $request = new FakeRequest();
        $response = $this->sut()("foo", $request);
        $this->assertStringContainsString("The chess file &quot;foo&quot; can't be loaded!", $response->output());
    }

    public function testChessAjax(): void
    {
        $request = new FakeRequest(["url" => "http://example.com/?&chess_game=italian&chess_ajax=1"]);
        $response = $this->sut()("italian", $request);
        $this->assertSame("Content-Type:text/html; charset=UTF-8", $response->contentType());
        Approvals::verifyHtml($response->output());
    }

    public function testIrrelevantAjax(): void
    {
        $request = new FakeRequest(["url" => "http://example.com/?&chess_game=spanish&chess_ajax=1"]);
        $response = $this->sut()("italian", $request);
        $this->assertNull($response->contentType());
        $this->assertSame("", $response->output());
    }
}
