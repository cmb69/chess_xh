<?php

/**
 * Testing the games.
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

require_once './vendor/autoload.php';
require_once './classes/Domain.php';
require_once './tests/unit/TestBase.php';

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

/**
 * Testing the games.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class GameTest extends TestBase
{
    /**
     * The test subject.
     *
     * @var Chess_Game
     */
    private $_subject;

    /**
     * Sets up the test fixture.
     *
     * @return void
     *
     * @global array The paths of system files and folders.
     */
    public function setUp()
    {
        global $pth;

        $pth = array(
            'folder' => array('plugins' => '../')
        );
        $this->_subject = new Chess_Game();
    }

    /**
     * Tests loading of a stored game.
     *
     * @return void
     */
    public function testLoad()
    {
        $this->_subject = Chess_Game::load('italian');
        $this->assertEquals(
            'r1bqk1nr/pppp1ppp/2n5/2b1p3/2B1P3/5N2/PPPP1PPP/RNBQK2R',
            (string) $this->_subject->getPosition($this->_subject->getPlyCount())
        );
    }

    /**
     * Tests that loading of a non existing game returns null.
     *
     * @return void
     */
    public function testLoadNotExistingReturnsNull()
    {
        $this->assertNull(Chess_Game::load('doesntexist'));
    }

    /**
     * Tests that loading of an empty file returns null.
     *
     * @return void
     */
    public function testLoadEmptyFileReturnsNull()
    {
        global $pth;

        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
        $pth['folder']['plugins'] = vfsStream::url('test/');
        $dataFolder = $pth['folder']['plugins'] . 'chess/data/';
        mkdir($dataFolder, 0777, true);
        touch($dataFolder . 'foo.dat');
        $this->assertNull(Chess_Game::load('foo'));
    }

    /**
     * Tests that getPosition() returns a Chess_Position.
     *
     * @return void
     */
    public function testgetPositionReturnsChessPosition()
    {
        $this->assertInstanceOf('Chess_Position', $this->_subject->getPosition(0));
    }

    /**
     * Tests the start position.
     *
     * @return void
     */
    public function testStartPosition()
    {
        $this->assertEquals(
            'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR',
            $this->_subject->getPosition(0)
        );
    }

    /**
     * Tests the ply count.
     *
     * @return void
     */
    public function testPlyCount()
    {
        $this->assertEquals(0, $this->_subject->getPlyCount());
    }

    /**
     * Tests that a move changes the position.
     *
     * @return void
     */
    public function testMoveChangesPosition()
    {
        $this->_subject->move('e2', 'e4');
        $this->assertEquals(
            'rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR',
            (string) $this->_subject->getPosition($this->_subject->getPlyCount())
        );
    }

    /**
     * Tests a historic position.
     *
     * @return void
     */
    public function testHistoricPosition()
    {
        $this->_subject->move('e2', 'e4');
        $this->_subject->move('e7', 'e5');
        $this->assertEquals(
            'rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR',
            (string) $this->_subject->getPosition(1)
        );
    }

    /**
     * Tests that __toString() returns PGN.
     *
     * @return void
     */
    public function testToStringReturnsPGN()
    {
        $expected = <<<EOT
[Event "?"]
[Site "?"]
[Date "??.??.??"]
[Round "?"]
[White "?"]
[Black "?"]
[Result "*"]

1. e4 e5 3. Nf3 Nc6 5. Bc4 Bc5 *
EOT;
        $this->assertEquals($expected, (string) Chess_Game::load('italian'));
    }
}

?>
