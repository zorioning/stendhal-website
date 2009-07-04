<?php
	if(getAdminLevel()<100) {
 		die("Ooops!");
	}
	$name=$_REQUEST["name"];
	startBox('Search Player');
?>
	<form method="get" action="">
		<input type="hidden" name="id" value="content/admin/playerhistory">
		<input type="text" name="name" maxlength="60" value="<?php if (isset($name)) {echo htmlspecialchars($name);}?>">
		<input type="submit" name="sublogin" value="Search">
	</form>
<?php
	endBox(); 

	if (isset($name)) {
		startBox('History for player '. htmlspecialchars($name));

		echo '<p>History for player <a href="/?id=content/scripts/character&amp;name='.htmlspecialchars($name).'">'.htmlspecialchars($name).'</a>.</p>';

		echo '<table class="prettytable"><tr><th>time</th><th>source</th><th>event</th><th>parameters</th></tr>';

		$history = PlayerHistoryEntry::getPlayerHistoryEntriesForPlayer($name);
		foreach ($history as $entry) {
			$timedate = htmlspecialchars($entry->timedate);
			$timedate = '<a href="/?id=content/admin/logs&amp;date='.substr($timedate, 0, 10).'">'.$timedate.'</a>';
			echo '<tr><td>'.$timedate.'</td><td>'.htmlspecialchars($entry->source)
				.'</td><td>'.htmlspecialchars($entry->event).'</td><td>';
			if (param1 != $name) {
				echo htmlspecialchars($entry->param1).' ';
			}
			echo htmlspecialchars($entry->param2).'</td></tr>';
		}
		echo '</table>';
		echo '<p><b>These logs are for administrators eyes ONLY and should not be copied or pasted to others.</b></p>';

		endBox(); 
	}
?>