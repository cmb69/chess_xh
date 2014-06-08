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

require_once './classes/Domain.php';
require_once './tests/unit/TestBase.php';

/**
 * Testing the positions.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class PositionTest extends TestBase
{
    /**
     * Sets up the test fixture.
     *
     * @return void
     */
    public function setUp()
    {
        $this->_subject = new Chess_Position();
    }

    /**
     * Tests the start position.
     *
     * @return void
     */
    public function testStartPosition()
    {
        $this->_assertPosition('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR');
    }

    /**
     * Tests the position after a move.
     *
     * @return void
     */
    public function testPositionAfterMove()
    {
        $this->_subject->applyMove(new Chess_Move('e2', 'e4'));
        $this->_assertPosition('rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR');
    }

    /**
     * Tests the position after a capture.
     *
     * @return void
     */
    public function testPositionAfterCapture()
    {
        $this->_subject->applyMove(new Chess_Move('e2', 'e4'));
        $this->_subject->applyMove(new Chess_Move('d7', 'd5'));
        $this->_subject->applyMove(new Chess_Move('e4', 'd5'));
        $this->_assertPosition('rnbqkbnr/ppp1pppp/8/3P4/8/8/PPPP1PPP/RNBQKBNR');
    }

    /**
     * Tests the position after a king's side castling.
     *
     * @return void
     */
    public function testPositionAfterKingSideCastling()
    {
        $this->_subject->applyMove(new Chess_Move('e2', 'e4'));
        $this->_subject->applyMove(new Chess_Move('e7', 'e5'));
        $this->_subject->applyMove(new Chess_Move('g1', 'f3'));
        $this->_subject->applyMove(new Chess_Move('b8', 'c6'));
        $this->_subject->applyMove(new Chess_Move('f1', 'c4'));
        $this->_subject->applyMove(new Chess_Move('f8', 'c5'));
        $this->_subject->applyMove(new Chess_Move('e1', 'g1'));
        $this->_subject->applyMove(new Chess_Move('e8', 'g8'));
        $this->_assertPosition(
            'r1bq1rk1/pppp1ppp/2n5/2b1p3/2B1P3/5N2/PPPP1PPP/RNBQ1RK1'
        );
    }

    /**
     * Tests the position after a queen's side castling.
     *
     * @return void
     */
    public function testPositionAfterQueenSideCastling()
    {
        $this->_subject->applyMove(new Chess_Move('d2', 'd4'));
        $this->_subject->applyMove(new Chess_Move('d7', 'd5'));
        $this->_subject->applyMove(new Chess_Move('b1', 'c3'));
        $this->_subject->applyMove(new Chess_Move('b8', 'c6'));
        $this->_subject->applyMove(new Chess_Move('c1', 'f4'));
        $this->_subject->applyMove(new Chess_Move('c8', 'f5'));
        $this->_subject->applyMove(new Chess_Move('d1', 'd2'));
        $this->_subject->applyMove(new Chess_Move('d8', 'd7'));
        $this->_subject->applyMove(new Chess_Move('e1', 'c1'));
        $this->_subject->applyMove(new Chess_Move('e8', 'c8'));
        $this->_assertPosition(
            '2kr1bnr/pppqpppp/2n5/3p1b2/3P1B2/2N5/PPPQPPPP/2KR1BNR'
        );
    }

    /**
     * Tests the position after an en passant.
     *
     * @return void
     */
    public function testPositionAfterEnPassant()
    {
        $this->_subject->applyMove(new Chess_Move('e2', 'e4'));
        $this->_subject->applyMove(new Chess_Move('a7', 'a5'));
        $this->_subject->applyMove(new Chess_Move('e4', 'e5'));
        $this->_subject->applyMove(new Chess_Move('f7', 'f5'));
        $this->_subject->applyMove(new Chess_Move('e5', 'f6'));
        $this->_assertPosition('rnbqkbnr/1pppp1pp/5P2/p7/8/8/PPPP1PPP/RNBQKBNR');
    }

    /**
     * Tests the position after a promotion.
     *
     * @return void
     */
    public function testPositionAfterPromotion()
    {
        $this->_subject->applyMove(new Chess_Move('e2', 'e4'));
        $this->_subject->applyMove(new Chess_Move('f7', 'f5'));
        $this->_subject->applyMove(new Chess_Move('e4', 'f5'));
        $this->_subject->applyMove(new Chess_Move('g7', 'g6'));
        $this->_subject->applyMove(new Chess_Move('f5', 'g6'));
        $this->_subject->applyMove(new Chess_Move('a7', 'a6'));
        $this->_subject->applyMove(new Chess_Move('g6', 'g7'));
        $this->_subject->applyMove(new Chess_Move('b7', 'b5'));
        $this->_subject->applyMove(new Chess_Move('g7', 'h8', 'q'));
        $this->_assertPosition('rnbqkbnQ/2ppp2p/p7/1p6/8/8/PPPP1PPP/RNBQKBNR');
    }

    /**
     * Tests hasPieceOn().
     *
     * @return void
     */
    public function testHasPieceOn()
    {
        $this->assertTrue($this->_subject->hasPieceOn('e1'));
        $this->assertFalse($this->_subject->hasPieceOn('e4'));
    }

    /**
     * Tests getPieceOn().
     *
     * @return void
     */
    public function testGetPieceOn()
    {
        $this->assertEquals('wk', $this->_subject->getPieceOn('e1'));
    }

    /**
     * Asserts a certain position.
     *
     * @param string $expected FEN piece placement.
     *
     * @return void
     */
    private function _assertPosition($expected)
    {
        $this->assertEquals($expected, (string) $this->_subject);
    }
}

?>
