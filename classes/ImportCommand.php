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
use Plib\Request;
use Plib\Response;
use Plib\View;

class ImportCommand
{
    /** @var PgnImporter */
    private $importer;

    /** @var CsrfProtector */
    private $csrfProtector;

    /** @var View */
    private $view;

    public function __construct(
        PgnImporter $importer,
        CsrfProtector $csrfProtector,
        View $view
    ) {
        $this->importer = $importer;
        $this->csrfProtector = $csrfProtector;
        $this->view = $view;
    }

    /** @todo Add success message */
    public function execute(Request $request): Response
    {
        $o = "";
        if ($request->get("action") === "import") {
            if (!$this->csrfProtector->check($request->post("chess_token"))) {
                return Response::create($this->view->message("fail", "error_unauthorized"));
            }
            $game = $request->post("chess_game");
            if (Game::isValidName($game)) {
                $this->importer->import($game);
            } else {
                $o .= $this->view->message("fail", "message_invalid_name", $game);
            }
        }
        $o .= $this->render($request);
        return Response::create($o);
    }

    public function render(Request $request): string
    {
        return $this->view->render("import", [
            "url" => $request->url()->with("action", "import")->relative(),
            "token" => $this->csrfProtector->token(),
            "filenames" => $this->importer->findAll(),
        ]);
    }
}
