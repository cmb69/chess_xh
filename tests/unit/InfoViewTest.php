<?php

namespace Chess;

use ApprovalTests\Approvals;

class InfoViewTest
{
    public function setUp(): void
    {
        global $pth, $plugin_tx;

        $pth = array(
            'folder' => array('plugins' => './plugins/')
        );
        $plugin_tx = array(
            'chess' => array('alt_icon' => 'Knight on chess board fragment')
        );
    }

    public function testRendersInfo(): void
    {
        $response = (new InfoView())->render();
        Approvals::verifyHtml($response);
    }
}
