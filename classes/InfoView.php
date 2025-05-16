<?php

/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Chess_XH.
 *
 * Chess_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Chess_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Chess_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Chess;

use Plib\SystemChecker;
use Plib\View;

class InfoView
{
    /** @var string */
    private $pluginFolder;

    /** @var SystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    public function __construct(
        string $pluginFolder,
        SystemChecker $systemChecker,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->systemChecker = $systemChecker;
        $this->view = $view;
    }

    public function render(): string
    {
        return "<h1>Chess " . $this->view->esc(Dic::VERSION) . "</h1>\n"
            . "<h2>" . $this->view->text("syscheck_title") . "</h2>\n"
            . $this->systemChecks();
    }

    private function systemChecks(): string
    {
        $checks = [];

        $phpVersion = "7.1.0";
        $okay = $this->systemChecker->checkVersion(PHP_VERSION, $phpVersion);
        $type = $okay ? "success" : "fail";
        $checks[] = $this->view->message($type, "syscheck_phpversion", $phpVersion, $this->success($okay));

        $xhVersion = "1.7.0";
        $okay = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $xhVersion");
        $type = $okay ? "success" : "fail";
        $checks[] = $this->view->message($type, "syscheck_xhversion", $xhVersion, $this->success($okay));

        $plibVersion = "1.8";
        $okay = $this->systemChecker->checkPlugin("plib", $plibVersion);
        $type = $okay ? "success" : "fail";
        $checks[] = $this->view->message($type, "syscheck_plibversion", $plibVersion, $this->success($okay));

        foreach (["css/", "languages/"] as $folder) {
            $folder = $this->pluginFolder . $folder;
            $okay = $this->systemChecker->checkWritability($folder);
            $type = $okay ? "success" : "warning";
            $checks[] = $this->view->message($type, "syscheck_writable", $folder, $this->success($okay));
        }

        return implode("", $checks);
    }

    private function success(bool $okay): string
    {
        return $okay ? $this->view->plain("syscheck_good") : $this->view->plain("syscheck_bad");
    }
}
