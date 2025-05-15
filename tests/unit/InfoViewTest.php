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

namespace Chess;

class InfoViewTest extends TestCase
{
    /** @var InfoView */
    protected $subject;

    public function setUp(): void
    {
        global $pth, $plugin_tx;

        $pth = array(
            'folder' => array('plugins' => './plugins/')
        );
        $plugin_tx = array(
            'chess' => array('alt_icon' => 'Knight on chess board fragment')
        );
        $this->setConstant('CHESS_VERSION', '1.0');
        $this->subject = new InfoView();
    }

    public function testFactory(): void
    {
        $this->assertInstanceOf(
            InfoView::class, InfoView::make()
        );
    }

    public function testRendersHeading(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'h1',
                'content' => 'Chess'
            )
        );
    }

    public function testRendersIcon(): void
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

    public function testRendersVersion(): void
    {
        $this->assertRenders(
            array(
                'tag' => 'p',
                'content' => 'Version: ' . CHESS_VERSION
            )
        );
    }

    public function testRendersCopyright(): void
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

    public function testRendersLicense(): void
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
