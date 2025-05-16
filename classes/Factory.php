<?php

namespace Chess;

use Plib\View;

class Factory
{
    public function makeGameView(): GameView
    {
        global $pth, $plugin_tx;
        $view = new View($pth["folder"]["plugins"] . "chess/views/", $plugin_tx["chess"]);
        return new GameView($view);
    }
}
