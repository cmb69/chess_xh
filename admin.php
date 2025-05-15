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

use Chess\Dic;
use Plib\Request;

if (!defined("CMSIMPLE_XH_VERSION")) {
    http_response_code(403);
    exit;
}

/**
 * @var string $o
 * @var string $admin
 */

if (XH_wantsPluginAdministration("chess")) {
    $o .= print_plugin_admin("on");
    switch ($admin) {
        case "":
            $o .= Dic::infoView()->render();
            break;
        case "plugin_main":
            $o .= Dic::importCommand()->execute(Request::current())();
            break;
        default:
            $o .= plugin_admin_common();
    }
}
