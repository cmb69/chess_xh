<?php

/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Chess_XH.
 *
 * Chess_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Chess_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Chess_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Chess;

use Plib\CsrfProtector;
use Plib\DocumentStore;
use Plib\SystemChecker;
use Plib\View;

class Dic
{
    public const VERSION = "1.0beta2";

    public static function chessController(): ChessController
    {
        return new ChessController(new DocumentStore(self::contentFolder()), self::view());
    }

    public static function importCommand(): ImportCommand
    {
        return new ImportCommand(
            new PgnImporter(self::contentFolder()),
            new CsrfProtector(),
            self::view()
        );
    }

    public static function infoView(): InfoView
    {
        global $pth;
        return new InfoView(
            $pth["folder"]["plugins"] . "chess/",
            new DocumentStore(self::contentFolder()),
            new SystemChecker(),
            self::view()
        );
    }

    private static function contentFolder(): string
    {
        global $pth;
        return $pth["folder"]["content"] . $pth["folder"]["base"] . "chess/";
    }

    private static function view(): View
    {
        global $pth, $plugin_tx;
        return new View($pth["folder"]["plugins"] . "chess/views/", $plugin_tx["chess"]);
    }
}
