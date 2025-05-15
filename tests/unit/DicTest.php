<?php

namespace Chess;

use PHPUnit\Framework\TestCase;

/** @small */
class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $pth, $plugin_tx;
        $pth = ["folder" => ["plugins" => ""]];
        $plugin_tx = ["chess" => []];
    }

    public function testMakesChessController(): void
    {
        $this->assertInstanceOf(ChessController::class, Dic::chessController());
    }

    public function testMakesImportCommand(): void
    {
        $this->assertInstanceOf(ImportCommand::class, Dic::importCommand());
    }

    public function testMakesInfoView(): void
    {
        $this->assertInstanceOf(InfoView::class, Dic::infoView());
    }
}
