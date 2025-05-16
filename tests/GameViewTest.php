<?php

namespace Chess;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\View;

class GameViewTest extends TestCase
{
    /** @var GameView */
    protected $subject;

    /** @var Game */
    private $game;

    /** @var View */
    private $view;

    public function setUp(): void
    {
        global $pth, $sn, $su, $plugin_tx;

        $pth = array(
            'folder' => array('plugins' => './')
        );
        $sn = '/xh/';
        $su = 'Chess';
        $plugin_tx = array(
            'chess' => array(
                'label_flip' => 'Flip',
                'label_start' => 'Start',
                'label_next' => 'Next',
                'label_goto' => 'Go to',
                'label_previous' => 'Previous',
                'label_end' => 'End'
            )
        );
        $this->game = new Game();
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["chess"]);
        $this->subject = new GameView($this->view);
    }

    public function testRendersView(): void
    {
        Approvals::verifyHtml($this->subject->render($this->game, 0, false));
    }

    public function testRendersWhiteKingOnLightSquare(): void
    {
        $game = new Game();
        $game->move('e2', 'e4');
        $game->move('e7', 'e5');
        $game->move('e1', 'e2');
        $subject = new GameView($this->view, 2);
        Approvals::verifyHtml($subject->render($game, 2, false));
    }

    public function testFlipped(): void
    {
        $this->subject = new GameView($this->view, 0);
        Approvals::verifyHtml($this->subject->render(new Game(), 0, true));
    }
}
