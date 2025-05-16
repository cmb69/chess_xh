<?php

namespace Chess;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeSystemChecker;
use Plib\View;

class InfoViewTest extends TestCase
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
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["chess"]);
        $response = (new InfoView("./", new FakeSystemChecker(), $view))->render();
        Approvals::verifyHtml($response);
    }
}
