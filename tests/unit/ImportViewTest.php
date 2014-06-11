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

require_once '../../cmsimple/functions.php';
require_once '../../cmsimple/classes/CSRFProtection.php';
require_once './classes/Service.php';
require_once './classes/Presentation.php';
require_once './tests/unit/TestBase.php';

/**
 * Testing the import views.
 *
 * @category Testing
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class ImportViewTest extends TestBase
{
    /**
     * The test subject.
     *
     * @var Chess_ImportView
     */
    protected $subject;

    /**
     * The PGN importer.
     *
     * @var Chess_PgnImporter
     */
    private $_importer;

    /**
     * Sets up the test fixture.
     *
     * @return void
     *
     * @global string The script name.
     * @global array  The localization of the plugins.
     */
    public function setUp()
    {
        global $sn, $plugin_tx, $_XH_csrfProtection;

        $sn = '/xh/';
        $plugin_tx = array(
            'chess' => array(
                'label_import' => 'Import',
                'menu_main' => 'Import'
            )
        );
        $_XH_csrfProtection = $this->getMockBuilder('XH_CSRFProtection')
            ->disableOriginalConstructor()->getMock();
        $this->_importer = $this->getMockBuilder('Chess_PgnImporter')
            ->disableOriginalConstructor()->getMock();
        $this->_importer->expects($this->any())->method('findAll')
            ->will($this->returnValue(array('foo', 'bar', 'baz')));
        $this->subject = new Chess_ImportView($this->_importer);
    }

    /**
     * Tests the factory.
     *
     * @return void
     */
    public function testFactory()
    {
        $this->assertInstanceOf(
            'Chess_ImportView', Chess_ImportView::make($this->_importer)
        );
    }

    /**
     * Tests that the heading is rendered.
     *
     * @return void
     */
    public function testRendersHeading()
    {
        $this->assertRenders(
            array(
                'tag' => 'h1',
                'content' => "Chess \xE2\x80\x93 Import"
            )
        );
    }

    /**
     * Tests that the form is rendered.
     *
     * @return void
     */
    public function testRendersForm()
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

    /**
     * Tests that the admin input field is rendered.
     *
     * @return void
     */
    public function testRendersAdminInput()
    {
        $this->_testRendersInput('admin', 'plugin_main');
    }

    /**
     * Tests that the action input field is rendered.
     *
     * @return void
     */
    public function testRendersActionInput()
    {
        $this->_testRendersInput('action', 'import');
    }

    /**
     * Tests that a hidden input field is rendered.
     *
     * @param string $name  A name attribute value.
     * @param string $value A value attribute value.
     *
     * @return void
     */
    private function _testRendersInput($name, $value)
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

    /**
     * Tests that the list is rendered.
     *
     * @return void
     */
    public function testRendersList()
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

    /**
     * Tests that list items with buttons are rendered.
     *
     * @return void
     */
    public function testRendersListItemWithButton()
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

    /**
     * Tests that the CSRF token input is rendered.
     *
     * @return void
     *
     * @global XH_CSRFProtection The CSRF protection.
     */
    public function testRendersCSRFTokenInput()
    {
        global $_XH_csrfProtection;

        $_XH_csrfProtection->expects($this->once())->method('tokenInput');
        $this->subject->render();
    }
}

?>
