<?php 

define('TOTAL_HOF_PLAYERS',10);

function getXP($player) {
  return $player->xp;
}

function getWealth($player) {
  return $player->money;
}

function getAge($player) {
  return round($player->age/60,2);
}

function printAge($minutes) {
  $h=$minutes;
  $m=$minutes%60;
  
  return round($h).':'.round($m);
}

function getDMScore($player) {
  return $player->getDMScore();
}

function getTotalAtk($player) {
  return ($player->attributes['atk'])*(1+0.03*($player->level));
}

function getTotalDef($player) {
  return ($player->attributes['def'])*(1+0.03*($player->level));
}

class HallOfFamePage extends Page {

	public function writeHtmlHeader() {
		echo '<title>Hall of Fame'.STENDHAL_TITLE.'</title>';
	}

	
function renderListOfPlayers($list, $f, $postfix='') {
  $i=1;
  foreach($list as $player) {
    ?>
    <div class="row">
      <div class="position"><?php echo $i; ?></div>
      <a href="<?php echo rewriteURL('/character/'.urlencode($player->name).'.html'); ?>">
      <img class="small_image" src="<?php echo rewriteURL('/images/outfit/'.urlencode($player->outfit).'.png')?>" alt="Player outfit"/>
      <span style="display: block" class="label"><?php echo htmlspecialchars(utf8_encode($player->name)); ?></span>
      <?php
      $var=$f($player);
      ?>
      <span style="display: block" class="data"><?php echo $var.$postfix; ?></span>    
      </a>
      <div style="clear: left;"></div>
    </div>

    <?php
    $i++;
  }
}


function writeContent() {

startBox("Best player"); 
$choosen=getBestPlayer(REMOVE_ADMINS_AND_POSTMAN);
 ?>
  <div class="bubble">The best player is decided based on the relation between XP and age, so the best players are those the spend most time earning XP instead of being idle around in game.</div>    
  <div class="best">
    <a href="<?php echo rewriteURL('/character/'.urlencode($choosen->name).'.html'); ?>">
    <span style="display: block" class="statslabel">Name:</span><span style="display: block" class="data"><?php echo htmlspecialchars(utf8_encode($choosen->name)); ?></span>
    <span style="display: block" class="statslabel">Age:</span><span style="display: block" class="data"><?php echo getAge($choosen); ?> hours</span>
    <span style="display: block" class="statslabel">Level:</span><span style="display: block" class="data"><?php echo $choosen->level; ?></span>
    <span style="display: block" class="statslabel">XP:</span><span style="display: block" class="data"><?php echo $choosen->xp; ?></span>
    <span style="display: block" class="sentence"><?php echo $choosen->sentence; ?></span>
    </a>
  </div> 
  <img class="bordered_image" src="<?php echo rewriteURL('/images/outfit/'.urlencode($choosen->outfit).'.png')?>" alt="">
 <?php endBox(); ?>

<div style="float: left; width: 34%">
<?php
	
startBox("Strongest players");
  ?>
  <div class="bubble">Based on XP and Karma</div>
  <?php
  $players= getPlayers(REMOVE_ADMINS_AND_POSTMAN,'xp DESC, karma DESC', 'limit '.TOTAL_HOF_PLAYERS);
  $this->renderListOfPlayers($players, 'getXP', " xp");
endBox();

?>
</div>
<div style="float: left; width: 33%">
<?php


startBox("Richest players");
  ?>
  <div class="bubble">Based on the amount of money</div>
  <?php
  $players= getPlayers(REMOVE_ADMINS_AND_POSTMAN,'money desc', 'limit '.TOTAL_HOF_PLAYERS);
  $this->renderListOfPlayers($players, 'getWealth', ' coins');
endBox();

?>
</div>
<div style="float: left; width: 33%">
<?php

startBox("Eldest players");
  ?>
  <div class="bubble">Based on the age in hours</div>
  <?php
  $players= getPlayers(REMOVE_ADMINS_AND_POSTMAN,'age desc', 'limit '.TOTAL_HOF_PLAYERS);
  $this->renderListOfPlayers($players, 'getAge', ' hours');
endBox();
?>
</div>
<div style="float: left; width: 33%">
<?php
startBox("Deathmatch heroes");
  ?>
  <div class="bubble">Based on the deathmatch score</div>
  <?php
  $players= getDMHeroes('limit '.TOTAL_HOF_PLAYERS);
  $this->renderListOfPlayers($players, 'getDMScore',' points');
endBox();

?>
</div>
<div style="float: left; width: 33%">
<?php
startBox("Best attackers");
  ?>
<div class="bubble">Based on atk*(1+0.03*level)</div>
  <?php
    $players= getPlayers(REMOVE_ADMINS_AND_POSTMAN,'atk*(1+0.03*level) desc', 'limit '.TOTAL_HOF_PLAYERS);
  $this->renderListOfPlayers($players, 'getTotalAtk', " total atk");
endBox();

?>
</div>
<div style="float: left; width: 33%">
<?php
startBox("Best defenders");
  ?>
<div class="bubble">Based on def*(1+0.03*level)</div>
  <?php
   $players= getPlayers(REMOVE_ADMINS_AND_POSTMAN,'def*(1+0.03*level) desc', 'limit '.TOTAL_HOF_PLAYERS);
  $this->renderListOfPlayers($players, 'getTotalDef', " total def");
endBox();

?>
</div>
<?php
	}
}
$page = new HallOfFamePage();
?>