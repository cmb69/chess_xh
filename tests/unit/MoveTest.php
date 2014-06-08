<?php

/**
 * Testing the moves.
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
require_once './tests/unit/TestBase.php';

/**
 * Testing the moves.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class MoveTest extends TestBase
{
    /**
     * The test subject.
     *
     * @var Chess_Move
     */
    private $_subject;

    /**
     * Sets up the test fixture.
     *
     * @return void
     */
    public function setUp()
    {
        $this->_subject = new Chess_Move('e7', 'd8', 'q');
    }

    /**
     * Tests getSource().
     *
     * @return void
     */
    public function testGetSource()
    {
        $this->assertEquals('e7', $this->_subject->getSource());
    }

    /**
     * Tests getSourceFile().
     *
     * @return void.
     */
    public function testGetSourceFile()
    {
        $this->assertEquals('e', $this->_subject->getSourceFile());
    }

    /**
     * Tests getSourceRank().
     *
     * @return void
     */
    public function testGetSourceRank()
    {
        $this->assertEquals('7', $this->_subject->getSourceRank());
    }

    /**
     * Tests getDestination().
     *
     * @return void
     */
    public function testGetDestination()
    {
        $this->assertEquals('d8', $this->_subject->getDestination());
    }

    /**
     * Tests getDestinationFile().
     *
     * @return void
     */
    public function testGetDestinationFile()
    {
        $this->assertEquals('d', $this->_subject->getDestinationFile());
    }

    /**
     * Tests getFileDistance().
     *
     * @return void
     */
    public function testGetFileDistance()
    {
        $this->assertEquals(1, $this->_subject->getFileDistance());
    }

    /**
     * Tests getPromotion().
     *
     * @return void
     */
    public function testGetPromotion()
    {
        $this->assertEquals('q', $this->_subject->getPromotion());
    }

    /**
     * Tests getSan().
     *
     * @param Chess_Move $move     A move.
     * @param string     $fen      A FEN like piece placement string.
     * @param string     $expected A move in SAN format.
     *
     * @return void
     *
     * @dataProvider dataForGetSan
     */
    public function testGetSan($move, $fen, $expected)
    {
        $this->assertEquals(
            $expected, $move->getSan(Chess_Position::makeFromFen($fen))
        );
    }

    /**
     * Returns data for testGetSan().
     *
     * @return void
     *
     * @todo Test for ambiguous moves.
     */
    public function dataForGetSan()
    {
        return array(
            array(new Chess_Move('e2', 'e4'), '8/8/8/8/8/8/4p3/8', 'e4'),
            array(new Chess_Move('g1', 'f3'), '8/8/8/8/8/8/8/6N1', 'Nf3'),
            array(new Chess_Move('d4', 'e5'), '8/8/8/4p3/3P4/8/8/8', 'dxe5'),
            array(new Chess_Move('d4', 'e5'), '8/8/8/4b3/3B4/8/8/8', 'Bxe5'),
            array(new Chess_Move('e1', 'g1'), '8/8/8/8/8/8/8/4K2R', 'O-O'),
            array(new Chess_Move('e1', 'c1'), '8/8/8/8/8/8/8/R3K3', 'O-O-O'),
            array(new Chess_Move('e5', 'f6'), '8/8/8/4Pp2/8/8/8/8', 'exf6'),
            array(new Chess_Move('e7', 'e8', 'q'), '8/4P3/8/8/8/8/8/8', 'e8=Q'),
            array(new Chess_Move('d1', 'e1'), '4k3/8/8/8/8/8/8/3Q4', 'Qe1+'),
            array(new Chess_Move('e1', 'e7'), '4k3/4q3/8/8/8/8/8/4Q3', 'Qxe7+'),
            array(new Chess_Move('e1', 'e7'), '4k3/4q3/8/6B1/8/8/8/4Q3', 'Qxe7#')
        );
    }
}

?>
