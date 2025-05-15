<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $url
 * @var string $token
 * @var list<string> $filenames
 */
?>

<h1>Chess – <?=$this->text("menu_main")?></h1>
<form class="chess_import_form" action="<?=$this->esc($url)?>" method="post">
  <input type="hidden" name="chess_token" value="<?=$token?>">
  <ul>
<?foreach ($filenames as $filename):?>
    <li>
      <span><?=$this->esc($filename)?></span>
      <button name="chess_game" value="<?=$this->esc($filename)?>"><?=$this->text("label_import")?></button>
    </li>
<?endforeach?>
  </ul>
</form>
