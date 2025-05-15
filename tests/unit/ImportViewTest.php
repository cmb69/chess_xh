<?php

/**
 * Testing the import views.
 *
 * PHP version 5
 *
 * @category  Testing
 * @package   Chess
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2014 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Chess_XH
 */

namespace Chess;

use ApprovalTests\Approvals;
use XH\CSRFProtection;

class ImportViewTest
{
    /** @var ImportView */
    protected $subject;

    /** @var PgnImporter */
    private $_importer;

    public function setUp(): void
    {
        global $sn, $plugin_tx, $_XH_csrfProtection;

        $sn = '/xh/';
        $plugin_tx = array(
            'chess' => array(
                'label_import' => 'Import',
                'menu_main' => 'Import'
            )
        );
        $_XH_csrfProtection = $this->getMockBuilder(CSRFProtection::class)
            ->disableOriginalConstructor()->getMock();
        $this->_importer = $this->getMockBuilder(PgnImporter::class)
            ->disableOriginalConstructor()->getMock();
        $this->_importer->expects($this->any())->method('findAll')
            ->will($this->returnValue(array('foo', 'bar', 'baz')));
        $this->subject = new ImportView($this->_importer);
    }

    public function testFactory(): void
    {
        $this->assertInstanceOf(
            ImportView::class, ImportView::make($this->_importer)
        );
    }

    public function testRendersHtml(): void
    {
        global $_XH_csrfProtection;
        $_XH_csrfProtection->expects($this->once())->method('tokenInput');
        Approvals::verifyHtml($this->subject->render());
    }
}
