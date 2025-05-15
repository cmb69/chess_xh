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
    public function execute(Request $request): void
    {
        global $action, $o;

        if ($action == 'import') {
            if (!$this->csrfProtector->check($request->post("chess_token"))) {
                $o .= "not authorized";
                return;
            }
            $game = $request->post("chess_game");
            if (Game::isValidName($game)) {
                $this->importer->import($game);
            } else {
                $o .= $this->view->message("fail", "message_invalid_name", $game);
            }
        }
        $o .= $this->render();
    }

    public function render(): string
    {
        global $plugin_tx;

        return '<h1>Chess &ndash; ' . $plugin_tx['chess']['menu_main'] . '</h1>'
            . $this->renderForm();
    }

    private function renderForm(): string
    {
        global $sn;

        $token = $this->csrfProtector->token();
        $result = '<form class="chess_import_form" action="' . $sn
            . '?chess" method="post">'
            . '<input type="hidden" name="chess_token" value="' . $token . '">';
        $result .= '<input type="hidden" name="admin" value="plugin_main">'
            . '<input type="hidden" name="action" value="import">'
            . $this->renderList()
            . '</form>';
        return $result;
    }

    private function renderList(): string
    {
        $result = '<ul>';
        foreach ($this->importer->findAll() as $name) {
            $result .= $this->renderListItem($name);
        }
        $result .= '</ul>';
        return $result;
    }

    private function renderListItem(string $name): string
    {
        global $plugin_tx;

        return '<li>'
            . $name
            . '<button name="chess_game" value="' . $name . '">'
            . $plugin_tx['chess']['label_import'] . '</button>'
            . '</li>';
    }
}
