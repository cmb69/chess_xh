<?php

namespace Chess;

class Factory
{
    public function makeGameView(Game $game, int $ply = 0, bool $flipped = false): GameView
    {
        return new GameView($game, $ply, $flipped);
    }
}
