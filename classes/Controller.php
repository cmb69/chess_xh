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

class Controller extends Presenter
{
    /** @var Factory */
    private $factory;

    public function __construct(Factory $factory)
    {
        parent::__construct();
        $this->factory = $factory;
    }

    public function dispatch(): void
    {
        if (XH_ADM // @phpstan-ignore-line
            && XH_wantsPluginAdministration("chess")
        ) {
            $this->handleAdministration();
        }
    }

    private function handleAdministration(): void
    {
        global $admin, $o;

        $o .= print_plugin_admin('on');
        switch ($admin) {
            case '':
                $infoView = $this->factory->makeInfoView();
                $o .= $infoView->render();
                break;
            case 'plugin_main':
                $this->handleImport();
                break;
            default:
                $o .= plugin_admin_common();
        }
    }

    private function handleImport(): void
    {
        global $pth;

        $importer = new PgnImporter(
            $pth['folder']['plugins'] . 'chess/data/'
        );
        $importCommand = $this->factory->makeImportCommand($importer, new ImportView($importer));
        $importCommand->execute();
    }
}
