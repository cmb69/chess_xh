<?php

namespace Chess;

use Plib\View;

class Factory
{
    public function makeGameView(Game $game, int $ply = 0, bool $flipped = false): GameView
    {
        global $pth, $plugin_tx;
        $view = new View($pth["folder"]["plugins"] . "chess/views/", $plugin_tx["chess"]);
        return new GameView($game, $view, $ply, $flipped);
    }
}
