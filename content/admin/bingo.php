<?php
class BingoPage extends Page {
	
	public function __construct() {
		$result = mysql_query("SELECT id, param1 FROM gameEvents WHERE id<'".mysql_real_escape_string($this->id)."' ORDER BY id DESC LIMIT 1", getGameDB());

        $row=mysql_fetch_assoc($result);
		$this->id = $row['id'];
		$this->name = $row['param1'];

        mysql_free_result($result);
	}
	
	public function writeHtmlHeader() {
		echo '<title>Bingo'.STENDHAL_TITLE.'</title>';
		echo '<meta http-equiv="refresh" content="30; URL=/index.php?id=content/admin/bingo&lastid='.$this->id.'">';
	}

	function writeContent() {
		startBox('Bingo');

		foreach($this->monsters as $m) {
			if($m->name==$this->name) {
			?>
	<div class="monster">
		<div class="name"><?php echo ucfirst($m->name); ?></div>
		<img class="monster" src="<?php echo $m->gfx; ?>" alt="">
		<div class="level">Level <?php echo $m->level; ?></div>
		<div class="xp">Killing it will give you <?php echo $m->xp; ?> XP.</div>
		<div class="respawn">Respawns on average in <?php echo printRespawn($m->respawn); ?> minutes.</div>
		<div class="description">
		<?php 
		if($m->description=="") {
			echo 'No description. Would you like to <a href="http://sourceforge.net/tracker/?func=add&amp;group_id=1111&amp;atid=301111">write one</a>?';
		} else {
			echo $m->description;
		}
		?>
	</div>
	<?php 
			}		
		endBox();
	}
}

$page = new BingoPage();
?>