<?php

/**
 * The presentation layer.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Chess
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2014 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Chess_XH
 */

namespace Chess;

class ImportView
{
    /** @var PgnImporter */
    private $importer;

    public static function make(PgnImporter $importer): self
    {
        return new self($importer);
    }

    public function __construct(PgnImporter $importer)
    {
        $this->importer = $importer;
    }

    public function render(): string
    {
        global $plugin_tx;

        return '<h1>Chess &ndash; ' . $plugin_tx['chess']['menu_main'] . '</h1>'
            . $this->renderForm();
    }

    private function renderForm(): string
    {
        global $sn, $_XH_csrfProtection;

        $result = '<form class="chess_import_form" action="' . $sn
            . '?chess" method="post">';
        if (isset($_XH_csrfProtection)) {
            $result .= $_XH_csrfProtection->tokenInput();
        }
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
