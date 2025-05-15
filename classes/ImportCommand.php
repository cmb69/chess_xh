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

use Plib\View;

class ImportCommand
{
    /** @var PgnImporter */
    private $importer;

    /** @var ImportView */
    private $importView;

    /** @var View */
    private $view;

    public function __construct(
        PgnImporter $importer,
        ImportView $importView,
        View $view
    ) {
        $this->importer = $importer;
        $this->importView = $importView;
        $this->view = $view;
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
                $o .= $this->view->message("fail", "message_invalid_name", $game);
            }
        }
        $o .= $this->importView->render();
    }
}
