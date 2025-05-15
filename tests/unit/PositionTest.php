<?php

/**
 * Testing the positions.
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

class PositionTest extends TestCase
{
    public function setUp(): void
    {
        $this->_subject = new Position();
    }

    public function testMakeFromFen(): void
    {
        $fen = 'r1bq1rk1/pppp1ppp/2n5/2b1p3/2B1P3/5N2/PPPP1PPP/RNBQ1RK1';
        $position = Position::makeFromFen($fen);
        $this->assertEquals($fen, (string) $position);
    }

    public function testStartPosition(): void
    {
        $this->_assertPosition('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR');
    }

    public function testPositionAfterMove(): void
    {
        $this->_subject->applyMove(new Move('e2', 'e4'));
        $this->_assertPosition('rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR');
    }

    public function testPositionAfterCapture(): void
    {
        $this->_subject->applyMove(new Move('e2', 'e4'));
        $this->_subject->applyMove(new Move('d7', 'd5'));
        $this->_subject->applyMove(new Move('e4', 'd5'));
        $this->_assertPosition('rnbqkbnr/ppp1pppp/8/3P4/8/8/PPPP1PPP/RNBQKBNR');
    }

    public function testPositionAfterKingSideCastling(): void
    {
        $this->_subject->applyMove(new Move('e2', 'e4'));
        $this->_subject->applyMove(new Move('e7', 'e5'));
        $this->_subject->applyMove(new Move('g1', 'f3'));
        $this->_subject->applyMove(new Move('b8', 'c6'));
        $this->_subject->applyMove(new Move('f1', 'c4'));
        $this->_subject->applyMove(new Move('f8', 'c5'));
        $this->_subject->applyMove(new Move('e1', 'g1'));
        $this->_subject->applyMove(new Move('e8', 'g8'));
        $this->_assertPosition(
            'r1bq1rk1/pppp1ppp/2n5/2b1p3/2B1P3/5N2/PPPP1PPP/RNBQ1RK1'
        );
    }

    public function testPositionAfterQueenSideCastling(): void
    {
        $this->_subject->applyMove(new Move('d2', 'd4'));
        $this->_subject->applyMove(new Move('d7', 'd5'));
        $this->_subject->applyMove(new Move('b1', 'c3'));
        $this->_subject->applyMove(new Move('b8', 'c6'));
        $this->_subject->applyMove(new Move('c1', 'f4'));
        $this->_subject->applyMove(new Move('c8', 'f5'));
        $this->_subject->applyMove(new Move('d1', 'd2'));
        $this->_subject->applyMove(new Move('d8', 'd7'));
        $this->_subject->applyMove(new Move('e1', 'c1'));
        $this->_subject->applyMove(new Move('e8', 'c8'));
        $this->_assertPosition(
            '2kr1bnr/pppqpppp/2n5/3p1b2/3P1B2/2N5/PPPQPPPP/2KR1BNR'
        );
    }

    public function testPositionAfterEnPassant(): void
    {
        $this->_subject->applyMove(new Move('e2', 'e4'));
        $this->_subject->applyMove(new Move('a7', 'a5'));
        $this->_subject->applyMove(new Move('e4', 'e5'));
        $this->_subject->applyMove(new Move('f7', 'f5'));
        $this->_subject->applyMove(new Move('e5', 'f6'));
        $this->_assertPosition('rnbqkbnr/1pppp1pp/5P2/p7/8/8/PPPP1PPP/RNBQKBNR');
    }

    public function testPositionAfterPromotion(): void
    {
        $this->_subject->applyMove(new Move('e2', 'e4'));
        $this->_subject->applyMove(new Move('f7', 'f5'));
        $this->_subject->applyMove(new Move('e4', 'f5'));
        $this->_subject->applyMove(new Move('g7', 'g6'));
        $this->_subject->applyMove(new Move('f5', 'g6'));
        $this->_subject->applyMove(new Move('a7', 'a6'));
        $this->_subject->applyMove(new Move('g6', 'g7'));
        $this->_subject->applyMove(new Move('b7', 'b5'));
        $this->_subject->applyMove(new Move('g7', 'h8', 'q'));
        $this->_assertPosition('rnbqkbnQ/2ppp2p/p7/1p6/8/8/PPPP1PPP/RNBQKBNR');
    }

    public function testHasPieceOn(): void
    {
        $this->assertTrue($this->_subject->hasPieceOn('e1'));
        $this->assertFalse($this->_subject->hasPieceOn('e4'));
    }

    public function testGetPieceOn(): void
    {
        $this->assertEquals('wk', $this->_subject->getPieceOn('e1'));
    }

    /** @dataProvider isAttackingData */
    public function testIsAttacking(string $fen, string $source, string $destination): void
    {
        $subject = Position::makeFromFen($fen);
        $this->assertTrue($subject->isAttacking($source, $destination));
    }

    public function isAttackingData(): array
    {
        return array(
            array('8/8/8/3p4/4P3/8/8/8', 'e4', 'd5'),
            array('8/8/8/8/3pP3/4PN2/8/8', 'f3', 'd4'),
            array('8/8/8/3p4/4P3/8/8/8', 'd5', 'e4'),
            array('8/8/8/8/2n5/8/8/5B2', 'f1', 'c4'),
            array('3r4/8/8/8/8/8/8/3R4', 'd1', 'd8'),
            array('3q4/8/8/8/8/8/8/3Q4', 'd1', 'd8'),
            array('8/8/8/3r4/4K3/8/8/8', 'e4', 'd5'),
        );
    }

    /** @dataProvider isNotAttackingData */
    public function testIsNotAttacking(string $fen, string $source, string $destination): void
    {
        $subject = Position::makeFromFen($fen);
        $this->assertFalse($subject->isAttacking($source, $destination));
    }

    public function isNotAttackingData(): array
    {
        return array(
            array('8/8/8/3P4/4P3/8/8/8', 'e4', 'd5'),
            array('8/8/8/4p3/4P3/8/8/8', 'e4', 'e5'),
            array('8/8/8/8/2n5/3P4/8/5B2', 'f1', 'c4'),
            array('3r4/8/8/3P4/8/8/8/3R4', 'd1', 'd8'),
            array('3q4/8/8/3p4/8/8/8/3Q4', 'd1', 'd8'),
            array('8/8/3n4/8/4K3/8/8/8', 'e4', 'd6'),
        );
    }

    private function _assertPosition(string $expected): void
    {
        $this->assertEquals($expected, (string) $this->_subject);
    }
}
