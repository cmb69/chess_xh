<?php

namespace Chess;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use XH\CSRFProtection;

class ImportViewTest extends TestCase
{
    /** @var ImportView */
    protected $subject;

    /** @var PgnImporter */
    private $importer;

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
        $this->importer = $this->getMockBuilder(PgnImporter::class)
            ->disableOriginalConstructor()->getMock();
        $this->importer->expects($this->any())->method('findAll')
            ->will($this->returnValue(array('foo', 'bar', 'baz')));
        $this->subject = new ImportView($this->importer);
    }

    public function testRendersHtml(): void
    {
        global $_XH_csrfProtection;
        $_XH_csrfProtection->expects($this->once())->method('tokenInput');
        Approvals::verifyHtml($this->subject->render());
    }
}
