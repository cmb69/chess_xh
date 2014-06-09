<?php

/**
 * Testing the PGN importers.
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
require_once './classes/Service.php';
require_once './tests/unit/TestBase.php';

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

/**
 * Testing the PGN importers.
 *
 * @category Testing
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class PgnImporterTest extends TestBase
{
    /**
     * Test PGN.
     */
    const PGN = <<<'EOT'
[Event "Ch World (match)"]
[Site "Chennai (India)"]
[Date "2013.11.09"]
[Round "1"]
[White "Carlsen Magnus (NOR)"]
[Black "Anand Viswanathan (IND)"]
[Result "1/2-1/2"]
[ECO "D02"]
[WhiteElo "2870"]
[BlackElo "2775"]
[ID ""]
[FileName ""]
[Annotator ""]
[Source ""]
[Remark ""]

1.Nf3 d5 2.g3 g6 3.Bg2 Bg7 4.d4 c6 5.O-O Nf6 6.b3 O-O 7.Bb2 Bf5
8.c4 Nbd7 9.Nc3 dxc4 10.bxc4 Nb6 11.c5 Nc4 12.Bc1 Nd5 13.Qb3
Na5 14.Qa3 Nc4 15.Qb3 Na5 16.Qa3 Nc4 1/2-1/2

[Event "?"]
[Site "?"]
[Date "??.??.??"]
[Round "?"]
[White "?"]
[Black "?"]
[Result "*"]

1.e4 d5 2.exd5 e5 3.d6 Qf6 4.d7 Ke7 5.d8=Q *
EOT;

    /**
     * The test subject.
     *
     * @var Chess_PgnImporter
     */
    private $_subject;

    /**
     * The path of the data folder.
     *
     * @var string
     */
    private $_dataFolder;

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

        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
        $pth['folder']['plugins'] = '../';
        $this->_dataFolder = vfsStream::url('test/chess/data/');
        mkdir($this->_dataFolder, 0777, true);
        file_put_contents($this->_dataFolder . 'test.pgn', self::PGN);
        $this->_subject = new Chess_PgnImporter($this->_dataFolder);
    }

    /**
     * Tests findAll().
     *
     * @return void
     */
    public function testFindAll()
    {
        $this->assertEquals(array('test'), $this->_subject->findAll());
    }

    /**
     * Tests import().
     *
     * @return void
     */
    public function testImport()
    {
        $this->_subject->import('test');
        $this->assertFileExists($this->_dataFolder . 'test_0.dat');
        $this->assertFileExists($this->_dataFolder . 'test_1.dat');
    }
}

?>
