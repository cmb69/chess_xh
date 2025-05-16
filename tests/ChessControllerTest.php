<?php

namespace Chess;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\DocumentStore;
use Plib\FakeRequest;
use Plib\View;

class ChessControllerTest extends TestCase
{
    /** @var Controller */
    private $subject;

    /** @var View */
    private $view;

    public function setUp(): void
    {
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["chess"]);
        $this->subject = new ChessController("./", new DocumentStore(__DIR__ . "/"), $this->view);
    }

    public function testChess(): void
    {
        $request = new FakeRequest();
        $response = $this->subject->chess("italian", $request);
        Approvals::verifyHtml($response->output());
    }

    public function testChessInvalidName(): void
    {
        $request = new FakeRequest();
        $response = $this->subject->chess("italian!", $request);
        $this->assertStringContainsString("The name &quot;italian!&quot; is invalid", $response->output());
    }

    public function testChessFlipped(): void
    {
        $this->subject = new ChessController("./", new DocumentStore(__DIR__ . "/"), $this->view);
        $request = new FakeRequest(["url" => "http://example.com/?&chess_action=flip"]);
        $response = $this->subject->chess("italian", $request);
        Approvals::verifyHtml($response->output());
    }

    public function testChessStartAction(): void
    {
        $this->subject = new ChessController("./", new DocumentStore(__DIR__ . "/"), $this->view);
        $request = new FakeRequest(["url" => "http://example.com/?&chess_action=start&chess_ply=1"]);
        $response = $this->subject->chess("italian", $request);
        Approvals::verifyHtml($response->output());
    }

    public function testChessNextAction(): void
    {
        $this->subject = new ChessController("./", new DocumentStore(__DIR__ . "/"), $this->view);
        $request = new FakeRequest(["url" => "http://example.com/?&chess_action=next"]);
        $response = $this->subject->chess("italian", $request);
        Approvals::verifyHtml($response->output());
    }

    public function testChessPreviousAction(): void
    {
        $this->subject = new ChessController("./", new DocumentStore(__DIR__ . "/"), $this->view);
        $request = new FakeRequest(["url" => "http://example.com/?&chess_action=previous&chess_ply=1"]);
        $response = $this->subject->chess("italian", $request);
        Approvals::verifyHtml($response->output());
    }

    public function testChessEndAction(): void
    {
        $this->subject = new ChessController("./", new DocumentStore(__DIR__ . "/"), $this->view);
        $request = new FakeRequest(["url" => "http://example.com/?&chess_action=end"]);
        $response = $this->subject->chess("italian", $request);
        Approvals::verifyHtml($response->output());
    }

    public function testChessFailure(): void
    {
        $request = new FakeRequest();
        $response = $this->subject->chess("foo", $request);
        $this->assertStringContainsString("The chess file &quot;foo&quot; can't be loaded!", $response->output());
    }

    public function testChessAjax(): void
    {
        $this->subject = new ChessController("./", new DocumentStore(__DIR__ . "/"), $this->view);
        $request = new FakeRequest(["url" => "http://example.com/?&chess_game=italian&chess_ajax=1"]);
        $response = $this->subject->chess("italian", $request);
        $this->assertSame("Content-Type:text/html; charset=UTF-8", $response->contentType());
        Approvals::verifyHtml($response->output());
    }

    public function testIrrelevantAjax(): void
    {
        $this->subject = new ChessController("./", new DocumentStore(__DIR__ . "/"), $this->view);
        $request = new FakeRequest(["url" => "http://example.com/?&chess_game=spanish&chess_ajax=1"]);
        $response = $this->subject->chess("italian", $request);
        $this->assertNull($response->contentType());
        $this->assertSame("", $response->output());
    }

    public function testEmitsScript(): void
    {
        global $bjs;

        $bjs = '';
        $request = new FakeRequest();
        $this->subject->chess("italian", $request);
        $this->assertSame('<script type="text/javascript" src="./chess.js"></script>', $bjs);
    }
}
