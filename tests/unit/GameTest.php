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

namespace Chess;

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    /** @var Game */
    private $subject;

    public function setUp(): void
    {
        global $pth;

        $pth = array(
            'folder' => array('plugins' => '../')
        );
        $this->subject = new Game();
    }

    public function testLoad(): void
    {
        $this->subject = Game::load('italian');
        $this->assertEquals('italian', $this->subject->getName());
        $this->assertEquals(
            'r1bqk1nr/pppp1ppp/2n5/2b1p3/2B1P3/5N2/PPPP1PPP/RNBQK2R',
            (string) $this->subject->getPosition($this->subject->getPlyCount())
        );
    }

    public function testLoadNotExistingReturnsNull(): void
    {
        $this->assertNull(Game::load('doesntexist'));
    }

    public function testLoadEmptyFileReturnsNull(): void
    {
        global $pth;

        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
        $pth['folder']['plugins'] = vfsStream::url('test/');
        $dataFolder = $pth['folder']['plugins'] . 'chess/data/';
        mkdir($dataFolder, 0777, true);
        touch($dataFolder . 'foo.dat');
        $this->assertNull(Game::load('foo'));
    }

    public function testgetPositionReturnsChessPosition(): void
    {
        $this->assertInstanceOf(Position::class, $this->subject->getPosition(0));
    }

    public function testStartPosition(): void
    {
        $this->assertEquals(
            'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR',
            $this->subject->getPosition(0)
        );
    }

    public function testGetName(): void
    {
        $this->assertEmpty($this->subject->getName());
    }

    public function testPlyCount(): void
    {
        $this->assertEquals(0, $this->subject->getPlyCount());
    }

    public function testGetMove(): void
    {
        $this->subject->move('e2', 'e4');
        $this->assertEquals(new Move('e2', 'e4'), $this->subject->getMove(0));
    }

    public function testGetInvalidMove(): void
    {
        $this->assertNull($this->subject->getMove(42));
    }

    public function testMoveChangesPosition(): void
    {
        $this->subject->move('e2', 'e4');
        $this->assertEquals(
            'rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR',
            (string) $this->subject->getPosition($this->subject->getPlyCount())
        );
    }

    public function testHistoricPosition(): void
    {
        $this->subject->move('e2', 'e4');
        $this->subject->move('e7', 'e5');
        $this->assertEquals(
            'rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR',
            (string) $this->subject->getPosition(1)
        );
    }

    public function testToStringReturnsPGN(): void
    {
        $expected = <<<EOT
[Event "?"]
[Site "?"]
[Date "??.??.??"]
[Round "?"]
[White "?"]
[Black "?"]
[Result "*"]

1. e4 e5 2. Nf3 Nc6 3. Bc4 Bc5 *
EOT;
        $this->assertEquals($expected, (string) Game::load('italian'));
    }
}
