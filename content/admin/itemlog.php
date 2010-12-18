<?php
class AdminItemlogPage extends Page {
	public function writeHtmlHeader() {
		?>
		<style type="text/css">
			#container {width:99%;}
			.important {font-weight: bold}
			.highlight {background-color: #FAA}
			.hide {display: none}
		</style>
		<?php
	}

	public function writeContent() {

	if(getAdminLevel() < 5000) {
		die("Ooops!");
	}
	$itemid=$_REQUEST["itemid"];
	$highlightTimestamp = $_REQUEST['timestamp'];
	startBox('Search itemlog');
?>
	<form method="get" action="" accept-charset="iso-8859-1">
		<input type="hidden" name="id" value="content/admin/itemlog">
		<label for="itemid">itemid: </label><input type="text" id="itemid" name="itemid" value="<?php if (isset($itemid)) {echo htmlspecialchars(utf8_encode($itemid));}?>">
		<label for="timestamp">timestamp: </label><input type="text" id="timestamp" name="timestamp" value="<?php if (isset($highlightTimestamp)) {echo htmlspecialchars(utf8_encode($highlightTimestamp));}?>">
		<input type="submit" name="sublogin" value="Search">
	</form>
<?php
	endBox(); 

	if (isset($itemid)) {
		startBox('Item History');

		echo '<p>History for item '.htmlspecialchars(utf8_encode($itemid)).'</a>.</p>';

		echo '<table class="prettytable"><tr><th>id</th><th>time</th><th>source</th><th>event</th><th colspan="4">parameters</th></tr>';

		$history = ItemLog::getLogEntriesForItem($itemid);
		foreach ($history as $entry) {
			$timedate = htmlspecialchars($entry->timedate);
			$timedate = '<a href="/?id=content/admin/logs&amp;date='.substr($timedate, 0, 10).'">'.$timedate.'</a>';
			$class = '';
			if (isset($highlightTimestamp) && $entry->timedate < $highlightTimestamp) {
				$class = 'hide ';
			}
			if ($entry->event == 'register') {
				$class = 'important ';
			}
			echo '<tr class="'.$class.'"><td>'.htmlspecialchars($entry->itemid).'</td>'
				.'<td>'.$timedate.'</td>'
				.'<td>'.htmlspecialchars($entry->source).'</td>'
				.'<td>'.htmlspecialchars($entry->event).'</td>'
				.'<td>'.htmlspecialchars($entry->param1).'</td>'
				.'<td>'.htmlspecialchars($entry->param2).'</td>'
				.'<td>'.htmlspecialchars($entry->param3).'</td>'
				.'<td>'.htmlspecialchars($entry->param4).'</td></tr>';
		}
		echo '</table>';
		echo '<p><b>These logs are for administrators eyes ONLY and should not be copied or pasted to others.</b></p>';

		endBox(); 
	}


	}
}
$page = new AdminItemlogPage();
?>