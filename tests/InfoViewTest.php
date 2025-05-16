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

    /** @var View */
    private $view;

    public function setUp(): void
    {
        $this->store = new DocumentStore("../../content/chess/");
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["chess"]);
    }

    private function sut(): InfoView
    {
        return new InfoView("./", $this->store, new FakeSystemChecker(), $this->view);
    }

    public function testRendersInfo(): void
    {
        $response = $this->sut()->render();
        Approvals::verifyHtml($response);
    }
}
