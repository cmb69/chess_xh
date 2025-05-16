<?php

require_once "./vendor/autoload.php";

require_once '../../cmsimple/classes/CSRFProtection.php';
require_once '../../cmsimple/functions.php';
require_once '../../cmsimple/adminfuncs.php';

require_once "../plib/classes/CsrfProtector.php";
require_once "../plib/classes/Document.php";
require_once "../plib/classes/DocumentStore.php";
require_once "../plib/classes/Request.php";
require_once "../plib/classes/Response.php";
require_once "../plib/classes/SystemChecker.php";
require_once "../plib/classes/Url.php";
require_once "../plib/classes/View.php";
require_once "../plib/classes/FakeRequest.php";
require_once "../plib/classes/FakeSystemChecker.php";

require_once './classes/model/Game.php';
require_once './classes/model/Move.php';
require_once './classes/model/Position.php';
require_once "./classes/Dic.php";
require_once './classes/PgnImporter.php';
require_once './classes/ChessController.php';
require_once './classes/InfoView.php';
require_once './classes/ImportCommand.php';

const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.7.4";
