<?php

namespace Chess;

use ApprovalTests\Approvals;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Plib\CsrfProtector;
use XH\CSRFProtection;

class ImportViewTest extends TestCase
{
    /** @var ImportView */
    protected $subject;

    /** @var PgnImporter */
    private $importer;

    /** @var CsrfProtector&Stub */
    private $csrfProtector;

    public function setUp(): void
    {
        global $sn, $plugin_tx;

        $sn = '/xh/';
        $plugin_tx = array(
            'chess' => array(
                'label_import' => 'Import',
                'menu_main' => 'Import'
            )
        );
        $this->csrfProtector = $this->createStub(CsrfProtector::class);
        $this->importer = $this->getMockBuilder(PgnImporter::class)
            ->disableOriginalConstructor()->getMock();
        $this->importer->expects($this->any())->method('findAll')
            ->will($this->returnValue(array('foo', 'bar', 'baz')));
        $this->subject = new ImportView($this->importer, $this->csrfProtector);
    }

    public function testRendersHtml(): void
    {
        Approvals::verifyHtml($this->subject->render());
    }
}
