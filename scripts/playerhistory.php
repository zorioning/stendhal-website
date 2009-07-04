<?php
/*
 Stendhal website - a website to manage and ease playing of Stendhal game
 Copyright (C) 2008  Miguel Angel Blanch Lardin

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
  * A class that represent a player history entry
  */
class PlayerHistoryEntry {
	/* date and time of event */
	public $timedate;
	/* name of admin */
	public $source;
	/* name of target */
	public $target;
	/* event */
	public $event;
	/* parameters */
	public $param;
  
	function __construct($timedate, $source, $event, $param1, $param2) {
		$this->timedate = $timedate;
		$this->source = $source;
		$this->target = $param1;
		$this->event = $event;
		$this->param = $param2;
	}


	/**
	  * Returns a list of history entries for players that meet the given condition.
	  * Note: Parmaters must be sql escaped.
	  */
	function getPlayerHistoryEntriesForPlayers($where='', $sortby='id', $cond='') {
	    return _getPlayers('select * from character_stats '.$where.' order by '.$sortby.' '.$cond, getGameDB());
		$query = "SELECT * FROM gameEvents WHERE event in ('adminlevel', 'adminnote', 'ban', 'gag', 'jail', 'support', 'supportanswer', 'teleport') ".$where." ORDER BY ".$sortby." ".$cond;
	    return PlayerHistoryEntry::_getPlayerHistoryEntries($query);
	}

	function getPlayerHistoryEntriesForPlayer($name) {
		$query = "SELECT * FROM gameEvents WHERE event in ('adminlevel', 'adminnote', 'alter', 'ban', 'gag', 'jail', 'summon', 'summonat', 'script', 'support', 'supportanswer', 'teleport', 'teleportto') AND (param1 = '".mysql_real_escape_string($name)."' OR source = '".mysql_real_escape_string($name)."' ) ORDER BY id";
	    return PlayerHistoryEntry::_getPlayerHistoryEntries($query);
	}

	function _getPlayerHistoryEntries($query) {
	    $result = mysql_query($query, getGameDB());
	    $list=array();

	    while($row=mysql_fetch_assoc($result)) {            
			$list[]=new PlayerHistoryEntry($row['timedate'],
	        	$row['source'], $row['event'], $row['param1'],$row['param2']);
	    }

	    mysql_free_result($result);

	    return $list;
	}
}
?>
