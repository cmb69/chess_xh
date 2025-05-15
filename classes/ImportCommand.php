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

class ImportCommand extends Presenter
{
    /** @var PgnImporter */
    private $importer;

    public static function make(PgnImporter $importer): ImportCommand
    {
        return new self($importer);
    }

    public function __construct(PgnImporter $importer)
    {
        parent::__construct();
        $this->importer = $importer;
    }

    /** @todo Add success message */
    public function execute(): void
    {
        global $action, $o, $_XH_csrfProtection;

        if ($action == 'import') {
            if (isset($_XH_csrfProtection)) {
                $_XH_csrfProtection->check();
            }
            $game = $_POST['chess_game'];
            if (Game::isValidName($game)) {
                $this->importer->import($game);
            } else {
                $o .= $this->renderFailure('invalid_name', $game);
            }
        }
        $view = ImportView::make($this->importer);
        $o .= $view->render();
    }
}
