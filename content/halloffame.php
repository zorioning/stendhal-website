<?php 

define('TOTAL_HOF_PLAYERS', 10);

function getPlain($value) {
	return $value;
}

function getAge($value) {
	return round($value/60, 2);
}

function getAchievementScore($player) {
	return $player->getHallOfFameScore('@');
}

function printAge($minutes) {
	$h=$minutes;
	$m=$minutes%60;
	return round($h).':'.round($m);
}


class HallOfFamePage extends Page {
	private $filterFrom = '';
	private $filterWhere = '';

	private $filter;
	private $detail;

	private $loginRequired = false;
	
	public function __construct() {
		$this->setupFilter();
		$this->setupDetail();
	}


	// ------------------------------------------------------------------------
	// ------------------------------------------------------------------------


	function writeHttpHeader() {
		if ($this->loginRequired) {
			header('Location: '.STENDHAL_LOGIN_TARGET.'/index.php?id=content/account/login&url='.urlencode(rewriteURL('/world/hall-of-fame/'.urlencode($this->filter).'_'.urlencode($this->detail).'.html')));
			return false;
		}
		return true;
	}


	public function writeHtmlHeader() {
		echo '<title>Hall of Fame ('.htmlspecialchars($this->filter).')'.STENDHAL_TITLE.'</title>';
	}


	function writeContent() {
		$this->writeTabs();
		if ($this->detail == "overview") {
			$this->renderOverview();
		} else {
			$this->renderDetails($detail);
		}
		$this->closeTabs();
	}


	// ------------------------------------------------------------------------
	// ------------------------------------------------------------------------
	
	function setupFilter() {
		$this->filter = 'active';
		if (isset($_REQUEST['filter'])) {
			$this->filter = urlencode($_REQUEST['filter']);
		}
		if ($this->filter=="alltimes") {
			$this->filterWhere=' AND recent="0"';
		} else if ($this->filter=="active") {
			$this->filterWhere = ' AND recent="1"';
		} else if ($this->filter=="friends") {
			if (!isset($_SESSION['account'])) {
				$this->loginRequired = true;;
				return;
			}
			$this->filterFrom = ", characters, buddy ";
			$this->filterWhere = " AND recent='0' AND character_stats.name=buddy.buddy AND buddy.charname=characters.charname "
				. " AND characters.player_id='".mysql_real_escape_string($_SESSION['account']->id)."'";
		}
		// TODO: 404 on invalid filter variable
		return;
	}

	function setupDetail() {
		$this->detail = 'overview';
		if (isset($_REQUEST['detail'])) {
			$this->detail == urlencode($_REQUEST['detail']);
		}
		// TODO: 404 on invalid detail variable
	}


	// ------------------------------------------------------------------------
	// ------------------------------------------------------------------------

	function writeTabs() {
		?>
		<br>
		<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
		<td class="barTab" width="2%"> &nbsp;</td>
		<?php echo '<td class="'.$this->getTabClass('active').'" width="25%"><a class="'.$this->getTabClass('active').'A" href="'.htmlspecialchars(rewriteURL('/world/hall-of-fame/active_'.$this->detail.'.html')).'">Active</a></td>';?>
		<td class="barTab" width="2%"> &nbsp;</td>
		<?php echo '<td class="'.$this->getTabClass('alltimes').'" width="25%"><a class="'.$this->getTabClass('alltimes').'A" href="'.htmlspecialchars(rewriteURL('/world/hall-of-fame/alltimes_'.$this->detail.'.html')).'">All times</a></td>';?>
		<td class="barTab" width="2%">&nbsp;</td>
		<?php echo '<td class="'.$this->getTabClass('friends').'" width="25%"><a class="'.$this->getTabClass('friends').'A" href="'.htmlspecialchars(rewriteURL('/world/hall-of-fame/friends_'.$this->detail.'.html')).'">Me &amp; my friends</a></td>';?>
		<td class="barTab"> &nbsp;</td>
		</tr>
		<tr><td colspan="7" class="tabPageContent">
		<br>
		<?php
	}


	function closeTabs() {
		?></td></tr></table><?php 
	}

	function getTabClass($tab) {
		if ($this->filter == $tab) {
			return 'activeTab';
		} else {
			return 'backgroundTab';
		}
	}

	function renderListOfPlayers($list, $f, $postfix='') {
		$i=1;
		foreach($list as $entry) {
		?>
			<div class="row">
				<div class="position"><?php echo $entry['rank']; ?></div>
				<a href="<?php echo rewriteURL('/character/'.surlencode($entry['charname']).'.html'); ?>">
					<img class="small_image" src="<?php echo rewriteURL('/images/outfit/'.surlencode($entry['outfit']).'.png')?>" alt="" />
					<span class="block label"><?php echo htmlspecialchars($entry['charname']); ?></span>
					<span class="block data"><?php echo $f($entry['points']).$postfix; ?></span>
				</a>
				<div style="clear: left;"></div>
			</div>
	
			<?php
			$i++;
		}
	}


	function renderDetails($detail) {
		//TODO: add more
		startBox("Strongest players");
		?>
		<div class="bubble">XP, Achievements and Age</div>
		<?php
		$players= getHOFPlayers($this->filterFrom.REMOVE_ADMINS_AND_POSTMAN.' AND character_stats.level>=10 '.$this->filterWhere, 'R');
		$this->renderListOfPlayers($players, 'getPlain', " xp");
		endBox();
	}


	function renderOverview() {
		startBox("Best player"); 
		$choosen = getBestPlayer($this->filterFrom.REMOVE_ADMINS_AND_POSTMAN.$this->filterWhere);
		?>
		<div class="bubble">The best player is decided based on the relation between XP, age, and achievement score. The best players are those who spend time earning XP and achievements.</div>    
		<div class="best">
			<a href="<?php echo rewriteURL('/character/'.surlencode($choosen->name).'.html'); ?>">
				<span class="block statslabel">Name:</span><span class="block data"><?php echo htmlspecialchars($choosen->name); ?></span>
				<span class="block statslabel">Age:</span><span class="block data"><?php echo getAge($choosen); ?> hours</span>
				<span class="block statslabel">Level:</span><span class="block data"><?php echo $choosen->level; ?></span>
				<span class="block statslabel">XP:</span><span class="block data"><?php echo $choosen->xp; ?></span>
				<span class="block statslabel">Achievement score:</span><span class="block data"><?php echo getAchievementScore($choosen); ?></span>
			</a>
		</div>
		<a href="<?php echo rewriteURL('/character/'.surlencode($choosen->name).'.html'); ?>">
		<img class="bordered_image" src="<?php echo rewriteURL('/images/outfit/'.surlencode($choosen->outfit).'.png')?>" alt="">
		</a>
		<?php if ($choosen->sentence != '') {
			echo '<div class="sentence">'.htmlspecialchars($choosen->sentence).'</div>';
		}?>
		<?php endBox(); ?>

		<div style="float: left; width: 34%">
			<?php startBox("Best players"); ?>
			<div class="bubble">XP, Achievements and Age</div>
			<?php
			$players = getHOFPlayers($this->filterFrom.REMOVE_ADMINS_AND_POSTMAN.$this->filterWhere, 'X', 'limit '.TOTAL_HOF_PLAYERS);
			$this->renderListOfPlayers($players, 'getPlain', " points");
			##echo '<a href="'.rewriteURL('/world/hall-of-fame-strongest.html').'">More</a>';
			endBox();
			?>
		</div>

		<div style="float: left; width: 33%">
			<?php startBox("Richest players"); ?>
			<div class="bubble">Amount of money</div>
			<?php
			$players= getHOFPlayers($this->filterFrom.REMOVE_ADMINS_AND_POSTMAN.$this->filterWhere, 'W', 'limit '.TOTAL_HOF_PLAYERS);
			$this->renderListOfPlayers($players, 'getPlain', ' coins');
			endBox();
			?>
		</div>

		<div style="float: left; width: 33%">
			<?php startBox("Eldest players"); ?>
			<div class="bubble">Age in hours</div>
			<?php
			$players= getHOFPlayers($this->filterFrom.REMOVE_ADMINS_AND_POSTMAN.$this->filterWhere,'A', 'limit '.TOTAL_HOF_PLAYERS);
			$this->renderListOfPlayers($players, 'getAge', ' hours');
			endBox();
			?>
		</div>

		<div style="float: left; width: 33%">
			<?php startBox("Deathmatch heroes"); ?>
			<div class="bubble">Deathmatch score</div>
			<?php
			$players=getHOFPlayers($this->filterFrom.REMOVE_ADMINS_AND_POSTMAN.$this->filterWhere, 'D', 'limit '.TOTAL_HOF_PLAYERS);
			$this->renderListOfPlayers($players, 'getPlain',' points');
			endBox();
			?>
		</div>

		<div style="float: left; width: 33%">
			<?php startBox("Best attackers"); ?>
			<div class="bubble">Based on atk*(1+0.03*level)</div>
			<?php
			$players= getHOFPlayers($this->filterFrom.REMOVE_ADMINS_AND_POSTMAN.$this->filterWhere, 'T', 'limit '.TOTAL_HOF_PLAYERS);
			$this->renderListOfPlayers($players, 'getPlain', " total atk");
			endBox();
			?>
		</div>

		<div style="float: left; width: 33%">
			<?php startBox("Best defenders"); ?>
			<div class="bubble">Based on def*(1+0.03*level)</div>
			<?php
			$players= getHOFPlayers($this->filterFrom.REMOVE_ADMINS_AND_POSTMAN.$this->filterWhere, 'F', 'limit '.TOTAL_HOF_PLAYERS);
			$this->renderListOfPlayers($players, 'getPlain', " total def");
			endBox();
			?>
		</div>
<?php
	}
}


$page = new HallOfFamePage();
?>
