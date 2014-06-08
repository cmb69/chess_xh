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
     * Tests the start position.
     *
     * @return void
     */
    public function testStartPosition()
    {
        $expected = array(
            'a1' => 'wr', 'b1' => 'wn', 'c1' => 'wb', 'd1' => 'wq',
            'e1' => 'wk', 'f1' => 'wb', 'g1' => 'wn', 'h1' => 'wr',
            'a2' => 'wp', 'b2' => 'wp', 'c2' => 'wp', 'd2' => 'wp',
            'e2' => 'wp', 'f2' => 'wp', 'g2' => 'wp', 'h2' => 'wp',
            'a7' => 'bp', 'b7' => 'bp', 'c7' => 'bp', 'd7' => 'bp',
            'e7' => 'bp', 'f7' => 'bp', 'g7' => 'bp', 'h7' => 'bp',
            'a8' => 'br', 'b8' => 'bn', 'c8' => 'bb', 'd8' => 'bq',
            'e8' => 'bk', 'f8' => 'bb', 'g8' => 'bn', 'h8' => 'br'
        );
        $this->assertEquals($expected, $this->_subject->getPosition(0));
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
        $expected = array(
            'a1' => 'wr', 'b1' => 'wn', 'c1' => 'wb', 'd1' => 'wq',
            'e1' => 'wk', 'f1' => 'wb', 'g1' => 'wn', 'h1' => 'wr',
            'a2' => 'wp', 'b2' => 'wp', 'c2' => 'wp', 'd2' => 'wp',
            'e4' => 'wp', 'f2' => 'wp', 'g2' => 'wp', 'h2' => 'wp',
            'a7' => 'bp', 'b7' => 'bp', 'c7' => 'bp', 'd7' => 'bp',
            'e7' => 'bp', 'f7' => 'bp', 'g7' => 'bp', 'h7' => 'bp',
            'a8' => 'br', 'b8' => 'bn', 'c8' => 'bb', 'd8' => 'bq',
            'e8' => 'bk', 'f8' => 'bb', 'g8' => 'bn', 'h8' => 'br'
        );
        $this->_subject->move('e2', 'e4');
        $this->assertEquals($expected, $this->_subject->getPosition(1));
    }

    /**
     * Tests a historic position.
     *
     * @return void
     */
    public function testHistoricPosition()
    {
        $expected = array(
            'a1' => 'wr', 'b1' => 'wn', 'c1' => 'wb', 'd1' => 'wq',
            'e1' => 'wk', 'f1' => 'wb', 'g1' => 'wn', 'h1' => 'wr',
            'a2' => 'wp', 'b2' => 'wp', 'c2' => 'wp', 'd2' => 'wp',
            'e4' => 'wp', 'f2' => 'wp', 'g2' => 'wp', 'h2' => 'wp',
            'a7' => 'bp', 'b7' => 'bp', 'c7' => 'bp', 'd7' => 'bp',
            'e7' => 'bp', 'f7' => 'bp', 'g7' => 'bp', 'h7' => 'bp',
            'a8' => 'br', 'b8' => 'bn', 'c8' => 'bb', 'd8' => 'bq',
            'e8' => 'bk', 'f8' => 'bb', 'g8' => 'bn', 'h8' => 'br'
        );
        $this->_subject->move('e2', 'e4');
        $this->_subject->move('e7', 'e5');
        $this->assertEquals($expected, $this->_subject->getPosition(1));
    }

    /**
     * Tests a capture.
     *
     * @return void
     */
    public function testCapture()
    {
        $this->_subject->move('e2', 'e4');
        $this->_subject->move('d7', 'd5');
        $this->_subject->move('e4', 'd5');
        $expected = array(
            'a1' => 'wr', 'b1' => 'wn', 'c1' => 'wb', 'd1' => 'wq',
            'e1' => 'wk', 'f1' => 'wb', 'g1' => 'wn', 'h1' => 'wr',
            'a2' => 'wp', 'b2' => 'wp', 'c2' => 'wp', 'd2' => 'wp',
            'd5' => 'wp', 'f2' => 'wp', 'g2' => 'wp', 'h2' => 'wp',
            'a7' => 'bp', 'b7' => 'bp', 'c7' => 'bp',
            'e7' => 'bp', 'f7' => 'bp', 'g7' => 'bp', 'h7' => 'bp',
            'a8' => 'br', 'b8' => 'bn', 'c8' => 'bb', 'd8' => 'bq',
            'e8' => 'bk', 'f8' => 'bb', 'g8' => 'bn', 'h8' => 'br'
        );
        $this->assertEquals($expected, $this->_subject->getPosition(3));
    }

    /**
     * Tests a king's side castling.
     *
     * @return void
     */
    public function testCastlingKingsSide()
    {
        $this->_subject->move('e2', 'e4');
        $this->_subject->move('e7', 'e5');
        $this->_subject->move('g1', 'f3');
        $this->_subject->move('b8', 'c6');
        $this->_subject->move('f1', 'c4');
        $this->_subject->move('g8', 'f6');
        $this->_subject->move('e1', 'g1');
        $expected = array(
            'a1' => 'wr', 'b1' => 'wn', 'c1' => 'wb', 'd1' => 'wq',
            'g1' => 'wk', 'c4' => 'wb', 'f3' => 'wn', 'f1' => 'wr',
            'a2' => 'wp', 'b2' => 'wp', 'c2' => 'wp', 'd2' => 'wp',
            'e4' => 'wp', 'f2' => 'wp', 'g2' => 'wp', 'h2' => 'wp',
            'a7' => 'bp', 'b7' => 'bp', 'c7' => 'bp', 'd7' => 'bp',
            'e5' => 'bp', 'f7' => 'bp', 'g7' => 'bp', 'h7' => 'bp',
            'a8' => 'br', 'c6' => 'bn', 'c8' => 'bb', 'd8' => 'bq',
            'e8' => 'bk', 'f8' => 'bb', 'f6' => 'bn', 'h8' => 'br'
        );
        $this->assertEquals($expected, $this->_subject->getPosition(7));
    }

    /**
     * Tests a queen's side castling.
     *
     * @return void
     */
    public function testCastlingQueensSide()
    {
        $this->_subject->move('d2', 'd4');
        $this->_subject->move('d7', 'd5');
        $this->_subject->move('b1', 'c3');
        $this->_subject->move('b8', 'c6');
        $this->_subject->move('c1', 'f4');
        $this->_subject->move('c8', 'f5');
        $this->_subject->move('d1', 'd2');
        $this->_subject->move('d8', 'd7');
        $this->_subject->move('e1', 'c1');
        $expected = array(
            'd1' => 'wr', 'c3' => 'wn', 'f4' => 'wb', 'd2' => 'wq',
            'c1' => 'wk', 'f1' => 'wb', 'g1' => 'wn', 'h1' => 'wr',
            'a2' => 'wp', 'b2' => 'wp', 'c2' => 'wp', 'd4' => 'wp',
            'e2' => 'wp', 'f2' => 'wp', 'g2' => 'wp', 'h2' => 'wp',
            'a7' => 'bp', 'b7' => 'bp', 'c7' => 'bp', 'd5' => 'bp',
            'e7' => 'bp', 'f7' => 'bp', 'g7' => 'bp', 'h7' => 'bp',
            'a8' => 'br', 'c6' => 'bn', 'f5' => 'bb', 'd7' => 'bq',
            'e8' => 'bk', 'f8' => 'bb', 'g8' => 'bn', 'h8' => 'br'
        );
        $this->assertEquals($expected, $this->_subject->getPosition(9));
    }

    /**
     * Tests an en passant.
     *
     * @return void
     */
    public function testEnPassant()
    {
        $this->_subject->move('e2', 'e4');
        $this->_subject->move('a7', 'a5');
        $this->_subject->move('e4', 'e5');
        $this->_subject->move('f7', 'f5');
        $this->_subject->move('e5', 'f6');
        $expected = array(
            'a1' => 'wr', 'b1' => 'wn', 'c1' => 'wb', 'd1' => 'wq',
            'e1' => 'wk', 'f1' => 'wb', 'g1' => 'wn', 'h1' => 'wr',
            'a2' => 'wp', 'b2' => 'wp', 'c2' => 'wp', 'd2' => 'wp',
            'f6' => 'wp', 'f2' => 'wp', 'g2' => 'wp', 'h2' => 'wp',
            'a5' => 'bp', 'b7' => 'bp', 'c7' => 'bp', 'd7' => 'bp',
            'e7' => 'bp',               'g7' => 'bp', 'h7' => 'bp',
            'a8' => 'br', 'b8' => 'bn', 'c8' => 'bb', 'd8' => 'bq',
            'e8' => 'bk', 'f8' => 'bb', 'g8' => 'bn', 'h8' => 'br'
        );
        $this->assertEquals($expected, $this->_subject->getPosition(5));
    }

    /**
     * Tests a promotion.
     *
     * @return void
     */
    public function testPromotion()
    {
        $this->_subject->move('e2', 'e4');
        $this->_subject->move('f7', 'f5');
        $this->_subject->move('e4', 'f5');
        $this->_subject->move('g7', 'g6');
        $this->_subject->move('f5', 'g6');
        $this->_subject->move('a7', 'a6');
        $this->_subject->move('g6', 'g7');
        $this->_subject->move('b7', 'b5');
        $this->_subject->move('g7', 'h8', 'q');
        $expected = array(
            'a1' => 'wr', 'b1' => 'wn', 'c1' => 'wb', 'd1' => 'wq',
            'e1' => 'wk', 'f1' => 'wb', 'g1' => 'wn', 'h1' => 'wr',
            'a2' => 'wp', 'b2' => 'wp', 'c2' => 'wp', 'd2' => 'wp',
            'h8' => 'wq', 'f2' => 'wp', 'g2' => 'wp', 'h2' => 'wp',
            'a6' => 'bp', 'b5' => 'bp', 'c7' => 'bp', 'd7' => 'bp',
            'e7' => 'bp',                             'h7' => 'bp',
            'a8' => 'br', 'b8' => 'bn', 'c8' => 'bb', 'd8' => 'bq',
            'e8' => 'bk', 'f8' => 'bb', 'g8' => 'bn'
        );
        $this->assertEquals($expected, $this->_subject->getPosition(9));
    }

    /**
     * Tests loading of a stored game.
     *
     * @return void
     */
    public function testLoad()
    {
        $this->_subject = Chess_Game::load('italian');
        $expected = array(
            'a1' => 'wr', 'b1' => 'wn', 'c1' => 'wb', 'd1' => 'wq',
            'e1' => 'wk', 'c4' => 'wb', 'f3' => 'wn', 'h1' => 'wr',
            'a2' => 'wp', 'b2' => 'wp', 'c2' => 'wp', 'd2' => 'wp',
            'e4' => 'wp', 'f2' => 'wp', 'g2' => 'wp', 'h2' => 'wp',
            'a7' => 'bp', 'b7' => 'bp', 'c7' => 'bp', 'd7' => 'bp',
            'e5' => 'bp', 'f7' => 'bp', 'g7' => 'bp', 'h7' => 'bp',
            'a8' => 'br', 'c6' => 'bn', 'c8' => 'bb', 'd8' => 'bq',
            'e8' => 'bk', 'c5' => 'bb', 'g8' => 'bn', 'h8' => 'br'
        );
        $this->assertEquals($expected, $this->_subject->getPosition(6));
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
}

?>
