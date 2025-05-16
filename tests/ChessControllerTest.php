<?php

namespace Chess;

use PHPUnit\Framework\TestCase;
use Plib\View;

class ChessControllerTest extends TestCase
{
    /** @var Controller */
    private $subject;

    /** @var GameView */
    private $gameView;

    /** @var View */
    private $view;

    public function setUp(): void
    {
        global $pth, $plugin_tx;

        $pth = array(
            'folder' => array('plugins' => '../')
        );
        $plugin_tx = array(
            'chess' => array(
                'message_invalid_name' => 'The name "%s" is invalid!',
                'message_load_error' => 'The chess file "%s" can\'t be loaded!'
            )
        );
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
        $this->gameView = $this->getMockBuilder(GameView::class)
            ->disableOriginalConstructor()->getMock();
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["chess"]);
        $this->subject = new ChessController($this->gameView, $this->view);
    }

    public function testChess(): void
    {
        $this->gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->subject->chess('italian')->output());
    }

    public function testChessInvalidName(): void
    {
        $this->assertStringContainsString(
            "The name &quot;italian!&quot; is invalid",
            $this->subject->chess('italian!')->output()
        );
    }

    public function testChessFlipped(): void
    {
        $_REQUEST['chess_flipped'] = '1';
        $_REQUEST['chess_action'] = 'flip';
        $this->subject = new ChessController($this->gameView, $this->view);
        $this->gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->subject->chess('italian')->output());
    }

    public function testChessStartAction(): void
    {
        $_REQUEST['chess_ply'] = '1';
        $_REQUEST['chess_action'] = 'start';
        $this->subject = new ChessController($this->gameView, $this->view);
        $this->gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->subject->chess('italian')->output());
    }

    public function testChessNextAction(): void
    {
        $_REQUEST['chess_action'] = 'next';
        $this->subject = new ChessController($this->gameView, $this->view);
        $this->gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->subject->chess('italian')->output());
    }

    public function testChessPreviousAction(): void
    {
        $_REQUEST['chess_ply'] = '1';
        $_REQUEST['chess_action'] = 'previous';
        $this->gameView = $this->getMockBuilder(GameView::class)
            ->disableOriginalConstructor()->getMock();
        $this->subject = new ChessController($this->gameView, $this->view);
        $this->gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->subject->chess('italian')->output());
    }

    public function testChessEndAction(): void
    {
        $_REQUEST['chess_action'] = 'end';
        $this->gameView = $this->getMockBuilder(GameView::class)
            ->disableOriginalConstructor()->getMock();
        $this->subject = new ChessController($this->gameView, $this->view);
        $this->gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->subject->chess('italian')->output());
    }

    public function testChessFailure(): void
    {
        $this->assertStringContainsString(
            "The chess file &quot;foo&quot; can't be loaded!",
            $this->subject->chess('foo')->output()
        );
    }

    public function testChessAjax(): void
    {
        $_REQUEST['chess_ajax'] = '1';
        $_REQUEST['chess_game'] = 'italian';
        $this->gameView = $this->getMockBuilder(GameView::class)
            ->disableOriginalConstructor()->getMock();
        $this->subject = new ChessController($this->gameView, $this->view);
        $this->gameView->expects($this->once())->method('render')
            ->will($this->returnValue('foo'));
        $response = $this->subject->chess('italian');
        $this->assertSame("foo", $response->output());
        $this->assertSame("Content-Type:text/html; charset=UTF-8", $response->contentType());
    }

    public function testIrrelevantAjax(): void
    {
        $_REQUEST['chess_ajax'] = '1';
        $_REQUEST['chess_game'] = 'spanish';
        $this->subject = new ChessController($this->gameView, $this->view);
        $response = $this->subject->chess('italian');
        $this->assertNull($response->contentType());
        $this->assertSame("", $response->output());
    }

    public function testEmitsScript(): void
    {
        global $bjs, $pth;

        $pth = ["folder" => ["plugins" => "../"]];
        $bjs = '';
        $this->subject->chess('italian');
        $this->assertSame('<script type="text/javascript" src="../chess/chess.js"></script>', $bjs);
    }
}
