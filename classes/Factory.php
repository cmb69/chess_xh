<?php

namespace Chess;

class Factory
{
    public function makeGameView(Game $game, int $ply = 0, bool $flipped = false): GameView
    {
        return new GameView($game, $ply, $flipped);
    }

    public function makeImportCommand(PgnImporter $importer, ImportView $importView): ImportCommand
    {
        return new ImportCommand($importer, $importView);
    }

    public function makeInfoView(): InfoView
    {
        return new InfoView();
    }
}
