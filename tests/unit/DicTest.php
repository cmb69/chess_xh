<?php

namespace Chess;

class DicTest extends TestCase
{
    public function testMakesInfoView(): void
    {
        $this->assertInstanceOf(InfoView::class, Dic::infoView());
    }
}
