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

namespace Chess;

class MoveTest extends TestCase
{
    /** @var Move */
    private $_subject;

    public function setUp(): void
    {
        $this->_subject = new Move('e7', 'd8', 'q');
    }

    public function testGetSource(): void
    {
        $this->assertEquals('e7', $this->_subject->getSource());
    }

    public function testGetSourceFile(): void
    {
        $this->assertEquals('e', $this->_subject->getSourceFile());
    }

    public function testGetSourceRank(): void
    {
        $this->assertEquals('7', $this->_subject->getSourceRank());
    }

    public function testGetDestination(): void
    {
        $this->assertEquals('d8', $this->_subject->getDestination());
    }

    public function testGetDestinationFile(): void
    {
        $this->assertEquals('d', $this->_subject->getDestinationFile());
    }

    public function testGetFileDistance(): void
    {
        $this->assertEquals(1, $this->_subject->getFileDistance());
    }

    public function testGetPromotion(): void
    {
        $this->assertEquals('q', $this->_subject->getPromotion());
    }

    /** @dataProvider dataForGetSan */
    public function testGetSan(Move $move, string $fen, string $expected): void
    {
        $this->assertEquals(
            $expected, $move->getSan(Position::makeFromFen($fen))
        );
    }

    /** @todo Test for ambiguous moves */
    public function dataForGetSan(): array
    {
        return array(
            array(new Move('e2', 'e4'), '8/8/8/8/8/8/4p3/8', 'e4'),
            array(new Move('g1', 'f3'), '8/8/8/8/8/8/8/6N1', 'Nf3'),
            array(new Move('d4', 'e5'), '8/8/8/4p3/3P4/8/8/8', 'dxe5'),
            array(new Move('d4', 'e5'), '8/8/8/4b3/3B4/8/8/8', 'Bxe5'),
            array(new Move('e1', 'g1'), '8/8/8/8/8/8/8/4K2R', 'O-O'),
            array(new Move('e1', 'c1'), '8/8/8/8/8/8/8/R3K3', 'O-O-O'),
            array(new Move('e5', 'f6'), '8/8/8/4Pp2/8/8/8/8', 'exf6'),
            array(new Move('e7', 'e8', 'q'), '8/4P3/8/8/8/8/8/8', 'e8=Q'),
            array(new Move('d1', 'e1'), '4k3/8/8/8/8/8/8/3Q4', 'Qe1+'),
            array(new Move('e1', 'e7'), '4k3/4q3/8/8/8/8/8/4Q3', 'Qxe7+'),
            array(new Move('e1', 'e7'), '4k3/4q3/8/6B1/8/8/8/4Q3', 'Qxe7#')
        );
    }
}
