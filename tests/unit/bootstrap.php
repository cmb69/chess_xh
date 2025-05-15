<?php

require_once "./vendor/autoload.php";

require_once '../../cmsimple/classes/CSRFProtection.php';
require_once '../../cmsimple/functions.php';
require_once '../../cmsimple/adminfuncs.php';

require_once "../plib/classes/Response.php";
require_once "../plib/classes/View.php";

require_once "./classes/Dic.php";
require_once "./classes/Factory.php";
require_once './classes/Game.php';
require_once './classes/Position.php';
require_once './classes/Move.php';
require_once './classes/PgnImporter.php';
require_once './classes/ChessController.php';
require_once './classes/GameView.php';
require_once './classes/InfoView.php';
require_once './classes/ImportCommand.php';
require_once './classes/ImportView.php';

const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.7.4";
