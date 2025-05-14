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

/**
 * The import commands.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class Chess_ImportCommand extends Chess_Presenter
{
    /**
     * The PGN importer.
     *
     * @var Chess_PgnImporter.
     */
    private $_importer;

    /**
     * Returns a new self instance.
     *
     * @param Chess_PgnImporter $importer A PGN importer.
     *
     * @return Chess_ImportCommand
     */
    public static function make(Chess_PgnImporter $importer)
    {
        return new self($importer);
    }

    /**
     * Initializes a new instance.
     *
     * @param Chess_PgnImporter $importer A PGN importer.
     *
     * @return void
     */
    public function __construct(Chess_PgnImporter $importer)
    {
        parent::__construct();
        $this->_importer = $importer;
    }

    /**
     * Executes the command.
     *
     * @return void
     *
     * @global string            The value of the <var>action</var> GP parameter.
     * @global string            The HTML of the contents area.
     * @global XH_CSRFProtection The CSRF protector.
     *
     * @todo Add success message.
     */
    public function execute()
    {
        global $action, $o, $_XH_csrfProtection;

        if ($action == 'import') {
            if (isset($_XH_csrfProtection)) {
                $_XH_csrfProtection->check();
            }
            $game = $_POST['chess_game'];
            if (Chess_Game::isValidName($game)) {
                $this->_importer->import($game);
            } else {
                $o .= $this->renderFailure('invalid_name', $game);
            }
        }
        $view = Chess_ImportView::make($this->_importer);
        $o .= $view->render();
    }
}
