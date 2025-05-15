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

use XH\CSRFProtection;

class ImportViewTest extends TestCase
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

    public function testRendersHeading(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'h1',
                'content' => "Chess \xE2\x80\x93 Import"
            )
        );
    }

    public function testRendersForm(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'form',
                'attributes' => array(
                    'action' => '/xh/?chess',
                    'method' => 'post'
                ),
                'class' => 'chess_import_form'
            )
        );
    }

    public function testRendersAdminInput(): void
    {
        $this->_testRendersInput('admin', 'plugin_main');
    }

    public function testRendersActionInput(): void
    {
        $this->_testRendersInput('action', 'import');
    }

    private function _testRendersInput(string $name, string $value): void
    {
        $this->assertRenders(
            array(
                'tag' => 'input',
                'attributes' => array(
                    'type' => 'hidden',
                    'name' => $name,
                    'value' => $value
                ),
                'parent' => array('tag' => 'form')
            )
        );
    }

    public function testRendersList(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'ul',
                'children' => array(
                    'only' => array('tag' => 'li'),
                    'count' => 3
                ),
                'parent' => array('tag' => 'form')
            )
        );
    }

    public function testRendersListItemWithButton(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'button',
                'attributes' => array(
                    'name' => 'chess_game',
                    'value' => 'foo'
                ),
                'content' => 'Import',
                'parent' => array('tag' => 'li')
            )
        );
    }

    public function testRendersCSRFTokenInput(): void
    {
        global $_XH_csrfProtection;

        $_XH_csrfProtection->expects($this->once())->method('tokenInput');
        $this->subject->render();
    }
}
