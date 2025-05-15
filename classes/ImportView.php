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
    /**
     * The PGN importer.
     *
     * @var PgnImporter
     */
    private $importer;

    /**
     * Returns a new self instance.
     *
     * @param PgnImporter $importer A PGN importer.
     *
     * @return ImportView
     */
    public static function make(PgnImporter $importer)
    {
        return new self($importer);
    }

    /**
     * Initializes a new instance.
     *
     * @param PgnImporter $importer A PGN importer.
     *
     * @return void
     */
    public function __construct(PgnImporter $importer)
    {
        $this->importer = $importer;
    }

    /**
     * Renders the view.
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the plugins.
     */
    public function render()
    {
        global $plugin_tx;

        return '<h1>Chess &ndash; ' . $plugin_tx['chess']['menu_main'] . '</h1>'
            . $this->renderForm();
    }

    /**
     * Renders the form.
     *
     * @return string (X)HTML.
     *
     * @global string            The script name.
     * @global XH_CSRFProtection The CSRF protector.
     */
    private function renderForm()
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

    /**
     * Renders the list.
     *
     * @return string (X)HTML.
     */
    private function renderList()
    {
        $result = '<ul>';
        foreach ($this->importer->findAll() as $name) {
            $result .= $this->renderListItem($name);
        }
        $result .= '</ul>';
        return $result;
    }

    /**
     * Renders a list item.
     *
     * @param string $name A basename.
     *
     * @return string (X)HTML
     *
     * @global array The localization of the plugins.
     */
    private function renderListItem($name)
    {
        global $plugin_tx;

        return '<li>'
            . $name
            . '<button name="chess_game" value="' . $name . '">'
            . $plugin_tx['chess']['label_import'] . '</button>'
            . '</li>';
    }
}
