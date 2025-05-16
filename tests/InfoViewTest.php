<?php

namespace Chess;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\DocumentStore;
use Plib\FakeSystemChecker;
use Plib\View;

class InfoViewTest extends TestCase
{
    /** @var DocumentStore */
    private $store;

    public function setUp(): void
    {
        $this->store = new DocumentStore("../../content/chess/");
    }

    public function testRendersInfo(): void
    {
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["chess"]);
        $response = (new InfoView("./", $this->store, new FakeSystemChecker(), $view))->render();
        Approvals::verifyHtml($response);
    }
}
