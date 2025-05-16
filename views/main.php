<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $name
 * @var list<list<string>> $ranks
 * @var string $url
 * @var string $selected
 * @var int $ply
 * @var int $flipped
 * @var string $start_disabled
 * @var string $end_disabled
 * @var string $script
 */
?>

<script type="module" src="<?=$this->esc($script)?>"></script>
<div id="chess_view_<?=$this->esc($name)?>" class="chess_view">
  <table class="chess_board">
<?foreach ($ranks as $rank):?>
    <tr>
<?  foreach ($rank as $file):?>
<?=$this->raw($file)?>
<?  endforeach?>
    </tr>
<?endforeach?>
  </table>
  <form class="chess_control_panel" action="<?=$this->esc($url)?>" method="get">
    <input type="hidden" name="selected" value="<?=$this->esc($selected)?>">
    <input type="hidden" name="chess_game" value="<?=$this->esc($name)?>">
    <input type="hidden" name="chess_flipped" value="<?=$this->esc($flipped)?>">
    <button type="submit" name="chess_action" value="goto"><?=$this->text("label_goto")?></button>
    <button type="submit" name="chess_action" value="start" <?=$this->esc($start_disabled)?>><?=$this->text("label_start")?></button>
    <button type="submit" name="chess_action" value="previous" <?=$this->esc($start_disabled)?>><?=$this->text("label_previous")?></button>
    <input type="text" name="chess_ply" value="<?=$this->esc($ply)?>">
    <button type="submit" name="chess_action" value="next" <?=$this->esc($end_disabled)?>><?=$this->text("label_next")?></button>
    <button type="submit" name="chess_action" value="end" <?=$this->esc($end_disabled)?>><?=$this->text("label_end")?></button>
    <button type="submit" name="chess_action" value="flip"><?=$this->text("label_flip")?></button>
  </form>
</div>
