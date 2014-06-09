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

require_once './classes/Domain.php';
require_once './classes/Service.php';
require_once './classes/Presentation.php';

/**
 * Testing the import commands.
 *
 * @category Testing
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class ImportCommandTest extends TestBase
{
    /**
     * The test subject.
     *
     * @var Chess_ImportCommand
     */
    private $_subject;

    /**
     * The PGN importer.
     *
     * @var Chess_PgnImporter
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
     * @var Chess_ImportView
     */
    private $_importView;

    /**
     * Sets up the test fixture.
     *
     * @return void
     *
     * @global string The value of the <var>admin</var> GP parameter.
     */
    public function setUp()
    {
        global $admin;

        $this->defineConstant('XH_ADM', true);
        $admin = 'plugin_main';
        $this->_importer = $this->getMockBuilder('Chess_PgnImporter')
            ->disableOriginalConstructor()->getMock();
        $this->_subject = new Chess_ImportCommand($this->_importer);
        $this->_importViewFactory = new PHPUnit_Extensions_MockStaticMethod(
            'Chess_ImportView::make', $this->_subject
        );
        $this->_importView = $this->getMockBuilder('Chess_ImportView')
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
            'Chess_ImportCommand', Chess_ImportCommand::make($this->_importer)
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
     * @global string The value of the <var>action</var> GP parameter.
     */
    public function testImport()
    {
        global $action;

        $action = 'import';
        $_POST['chess_game'] = 'foo';
        $this->_importer->expects($this->once())->method('import')->with('foo');
        $this->_importView->expects($this->once())->method('render');
        $this->_importViewFactory->expects($this->once())->with($this->anything())
            ->will($this->returnValue($this->_importView));
        $this->_subject->execute();
    }
}

?>
