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

/**
 * Testing the moves.
 *
 * @category CMSimple_XH
 * @package  Chess
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chess_XH
 */
class MoveTest extends PHPUnit_Framework_TestCase
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
}

?>
