<?php

/**
 * Testing the import commands.
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

class ImportCommandTest extends TestCase
{
    /**
     * The test subject.
     *
     * @var ImportCommand
     */
    private $_subject;

    /**
     * The PGN importer.
     *
     * @var PgnImporter
     */
    private $_importer;

    /**
     * The view factory.
     *
     * @var object
     */
    private $_importViewFactory;

    /**
     * The view.
     *
     * @var ImportView
     */
    private $_importView;

    /**
     * Sets up the test fixture.
     *
     * @return void
     *
     * @global string            The value of the <var>admin</var> GP parameter.
     * @global XH_CSRFProtection The CSRF protector.
     */
    public function setUp(): void
    {
        global $admin, $_XH_csrfProtection, $plugin_tx;

        $this->setConstant('XH_ADM', true);
        $admin = 'plugin_main';
        $_XH_csrfProtection = $this->getMockBuilder(XH\CSRFProtection::class)
            ->disableOriginalConstructor()->getMock();
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
        $this->_importer = $this->getMockBuilder(PgnImporter::class)
            ->disableOriginalConstructor()->getMock();
        $this->_subject = new ImportCommand($this->_importer);
        $this->_importViewFactory = $this->createFunctionMock(
            '\Chess\ImportView::make'
        );
        $this->_importViewFactory = $this->createFunctionMock('Chess\ImportView::make');
        $this->_importView = $this->getMockBuilder(ImportView::class)
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * Tests the factory method.
     *
     * @return void
     */
    public function testFactory()
    {
        $this->assertInstanceOf(
            ImportCommand::class, ImportCommand::make($this->_importer)
        );
    }

    /**
     * Tests displaying the view only.
     *
     * @return void
     *
     * @global string The value of the <var>action</var> GP parameter.
     */
    public function testViewOnly()
    {
        global $action;

        $this->markTestSkipped();
        $action = 'plugin_text';
        $this->_importer->expects($this->never())->method('import');
        $this->_importView->expects($this->once())->method('render');
        $this->_importViewFactory->expects($this->once())->with($this->anything())
            ->will($this->returnValue($this->_importView));
        $this->_subject->execute();
    }

    /**
     * Tests the import.
     *
     * @return void
     *
     * @global string            The value of the <var>action</var> GP parameter.
     * @global XH_CSRFProtection The CSRF protector.
     */
    public function testImport()
    {
        global $action, $_XH_csrfProtection;

        $this->markTestSkipped();
        $action = 'import';
        $_POST['chess_game'] = 'foo';
        $_XH_csrfProtection->expects($this->once())->method('check');
        $this->_importer->expects($this->once())->method('import')->with('foo');
        $this->_importView->expects($this->once())->method('render');
        $this->_importViewFactory->expects($this->once())->with($this->anything())
            ->will($this->returnValue($this->_importView));
        $this->_subject->execute();
    }

    /**
     * Tests the import.
     *
     * @return void
     *
     * @global string            The value of the <var>action</var> GP parameter.
     * @global XH_CSRFProtection The CSRF protector.
     */
    public function testImportFailsForInvalidName()
    {
        global $o, $action, $_XH_csrfProtection;

        $this->markTestSkipped();
        $o = '';
        $action = 'import';
        $_POST['chess_game'] = 'foo!';
        $_XH_csrfProtection->expects($this->once())->method('check');
        $this->_importView->expects($this->once())->method('render');
        $this->_importViewFactory->expects($this->once())->with($this->anything())
            ->will($this->returnValue($this->_importView));
        $this->_subject->execute();
        $this->assertTag(
            array(
                'tag' => 'p',

            ),
            $o
        );
    }
}

?>
