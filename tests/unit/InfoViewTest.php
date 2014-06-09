<?php

/**
 * Testing the info views.
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

require_once './vendor/autoload.php';
require_once './classes/Presentation.php';
require_once './tests/unit/TestBase.php';

/**
 * Testing the info views.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class InfoViewTest extends TestBase
{
    /**
     * The subject under test.
     *
     * @var Chess_InfoView
     */
    protected $subject;

    /**
     * Sets up the test fixture.
     *
     * @return void
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the plugins.
     */
    public function setUp()
    {
        global $pth, $plugin_tx;

        $pth = array(
            'folder' => array('plugins' => './plugins/')
        );
        $plugin_tx = array(
            'chess' => array('alt_icon' => 'Knight on chess board fragment')
        );
        $this->defineConstant('CHESS_VERSION', '1.0');
        $this->subject = new Chess_InfoView();
    }

    /**
     * Tests the factory.
     *
     * @return void
     */
    public function testFactory()
    {
        $this->assertInstanceOf(
            'Chess_InfoView', Chess_InfoView::make()
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
                'content' => 'Chess'
            )
        );
    }

    /**
     * Tests that the plugin icon is rendered.
     *
     * @return void
     */
    public function testRendersIcon()
    {
        $this->assertRenders(
            array(
                'tag' => 'img',
                'attributes' => array(
                    'src' => './plugins/chess/chess.png',
                    'class' => 'chess_icon',
                    'alt' => 'Knight on chess board fragment'
                )
            )
        );
    }

    /**
     * Tests that the version info is rendered.
     *
     * @return void
     */
    public function testRendersVersion()
    {
        $this->assertRenders(
            array(
                'tag' => 'p',
                'content' => 'Version: ' . CHESS_VERSION
            )
        );
    }

    /**
     * Tests that the copyright info is rendered.
     *
     * @return void
     */
    public function testRendersCopyright()
    {
        $this->assertRenders(
            array(
                'tag' => 'p',
                'content' => "Copyright \xC2\xA9 2014",
                'child' => array(
                    'tag' => 'a',
                    'attributes' => array(
                        'href' => 'http://3-magi.net/',
                        'target' => '_blank'
                    ),
                    'content' => 'Christoph M. Becker'
                )
            )
        );
    }

    /**
     * Tests that the license info is rendered.
     *
     * @return void
     */
    public function testRendersLicense()
    {
        $this->assertRenders(
            array(
                'tag' => 'p',
                'attributes' => array('class' => 'chess_license'),
                'content' => 'This program is free software:'
            )
        );
    }
}

?>
