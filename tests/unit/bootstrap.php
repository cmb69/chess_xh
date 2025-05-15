<?php

require_once "./vendor/autoload.php";

require_once '../../cmsimple/classes/CSRFProtection.php';
require_once '../../cmsimple/functions.php';
require_once '../../cmsimple/adminfuncs.php';

require_once "./classes/Dic.php";
require_once "./classes/Factory.php";
require_once './classes/Game.php';
require_once './classes/Position.php';
require_once './classes/Move.php';
require_once './classes/PgnImporter.php';
require_once './classes/Presenter.php';
require_once './classes/Controller.php';
require_once './classes/GameView.php';
require_once './classes/InfoView.php';
require_once './classes/ImportCommand.php';
require_once './classes/ImportView.php';

require_once './tests/unit/FunctionMock.php';
require_once './tests/unit/UopzFunctionMock.php';
require_once './tests/unit/TestCase.php';

const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.7.4";
const CHESS_VERSION = "1.0beta2";
