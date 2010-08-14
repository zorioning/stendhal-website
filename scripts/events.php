<?php
/*
 Stendhal website - a website to manage and ease playing of Stendhal game
 Copyright (C) 2008-2010  The Arianne Project
 

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

class Event {
	public $source;
	public $timedate;
	
	function __construct($source, $timedate) {
		$this->source=$source; 	  	  	
		$this->timedate=$timedate;
	}
	
	function getURL($type) {
		if ($type == 'P') {
			$url = 'character';
		} else if ($type == 'C') {
			$url = 'creature';
		} else {
			$url = '';
		}
		return $url;
	} 
	
	function getCharacterHtml($character) {
		return '<a href="'.rewriteURL('/character/'.surlencode($character).'.html').'">'.htmlspecialchars($character).'</a>';
	}
	
	public function getHtml() {
		return '';
	}
	
	function getPrefix($string,$type) {
		if ($type == 'C') {
			$prefix = a_an($string);
		} else {
			$prefix = '';
		}
		return $prefix;
	}
	
	function getItemPrefix($string,$amount) {
		if ($amount>1) {
			$prefix = 'some';
		} else {
			$prefix = a_an($string);
		}
		return $prefix;
	}
}

class KillEvent extends Event  {
  public $victim;
  public $sourcetype;  
  public $victimtype;  
  
  function __construct($source, $victim, $sourcetype, $victimtype, $timedate) {
  	parent::__construct($source, $timedate); 
  	$this->victim=$victim;
  	$this->sourcetype=$sourcetype;
  	$this->victimtype=$victimtype;	  	  	  	
  }
  
  function getHtml() {
  	// known issue with urls of baby dragon, cat and sheep which are down as type 'C'
	// cheat and create pages for them?
  	return '<p>'.ucfirst($this->getPrefix($this->source,$this->sourcetype)).' <a href="'.rewriteURL('/'.$this->getURL($this->sourcetype).'/'.surlencode($this->source).'.html').'">'.htmlspecialchars($this->source).'</a> ' .
    		'killed '.$this->getPrefix($this->victim,$this->victimtype).' <a href="'.rewriteURL('/'.$this->getURL($this->victimtype).'/'.surlencode($this->victim).'.html').'">'.htmlspecialchars($this->victim).'</a>  at '.date('H:i',strtoTime($this->timedate));
  }
  
}

function getKillEvents() {
    $result = mysql_query('SELECT source, param1 as victim, left(param2,1) as sourcetype, right(trim(param2),1) as victimtype,  timedate ' .
    		'			 FROM gameEvents WHERE event=\'killed\' and source <> \'baby_dragon\' and timedate > subtime(now(), \'00:05:00\') limit 5', getGameDB());
    $killevents=array();
    while($row=mysql_fetch_assoc($result)) {      
      $killevents[]=new KillEvent($row['source'],$row['victim'],$row['sourcetype'],$row['victimtype'],$row['timedate']);
    }
    
    mysql_free_result($result);
	
    return $killevents;
}


class OutfitEvent extends Event  {
  
  function __construct($source, $timedate) {
  	parent::__construct($source, $timedate);  	  	  	
  }
  
  function getHtml() {
  	return '<p>'.$this->getCharacterHtml($this->source).' changed outfit at '.date('H:i',strtoTime($this->timedate));
  }
  
}
 
 function getOutfitEvents() {
 	// consider adding a distinct or group by so we don't get lots from same player
    $result = mysql_query('SELECT source,  timedate ' .
    					  'FROM gameEvents WHERE event=\'outfit\' and timedate > subtime(now(), \'01:00:00\') limit 2', getGameDB());
    $outfitevents=array();
    while($row=mysql_fetch_assoc($result)) {      
      $outfitevents[]=new OutfitEvent($row['source'],$row['timedate']);
    }
    
    mysql_free_result($result);
	
    return $outfitevents;
}
  
  
class QuestEvent extends Event  {
  public $quest;
  
  function __construct($source, $quest, $timedate) {
  	parent::__construct($source, $timedate); 
  	$this->quest=$quest;	  	  	  	
  }
  
  function getHtml() {
  	return '<p>'.$this->getCharacterHtml($this->source).' completed the '.htmlspecialchars(ucfirst(str_replace('_',' ',$this->quest))).' quest at '.date('H:i',strtoTime($this->timedate));
  }
  
}
 function getQuestEvents() {
 	// distinct needed as for the daily item quest there are 3 updates per single quest
    $result = mysql_query('SELECT distinct source, param1 as quest, timedate ' .
    					  'FROM gameEvents WHERE event=\'quest\' and param1 IN (\'daily\',\'weekly_item\',\'daily_item\',\'deathmatch\',\'zoo_food\') and timedate > subtime(now(), \'01:00:00\') and left(param2,4)=\'done\'  limit 10', getGameDB());
    $questevents=array();
    while($row=mysql_fetch_assoc($result)) {      
      $questevents[]=new QuestEvent($row['source'],$row['quest'],$row['timedate']);
    }
    
    mysql_free_result($result);
	
    return $questevents;
}

class LevelEvent extends Event  {
  public $level;

  function __construct($source, $level, $timedate) {
  	parent::__construct($source, $timedate); 
  	$this->level=$level;  	  	  	
  }
  
  function getHtml() {
  	return '<p>'.$this->getCharacterHtml($this->source).' reached level '.htmlspecialchars($this->level).' at '.date('H:i',strtoTime($this->timedate));
  }
  
}
 function getLevelEvents() {
 
    $result = mysql_query('SELECT source, param1 as level,  timedate ' .
    					  'FROM gameEvents WHERE event=\'level\'  and timedate > subtime(now(), \'01:00:00\')  limit 10', getGameDB());
    $levelevents=array();
    while($row=mysql_fetch_assoc($result)) {      
      $levelevents[]=new LevelEvent($row['source'],$row['level'],$row['timedate']);
    }
    
    mysql_free_result($result);
	
    return $levelevents;
}


class SignEvent extends Event  {
  public $text;

  function __construct($source, $text, $timedate) {
  	parent::__construct($source, $timedate); 
  	$this->text=$text;  	  	  	
  }
  
  function getHtml(){
  	return '<p>'.$this->getCharacterHtml($this->source).' rented a sign saying: "'.htmlspecialchars($this->text).'" at '.date('H:i',strtoTime($this->timedate));
  }
  
}
 function getSignEvents() {
 
    $result = mysql_query('SELECT source, trim(param2) as text,  timedate ' .
    					  'FROM gameEvents WHERE event=\'sign\'  and timedate > subtime(now(), \'01:00:00\')  limit 10', getGameDB());
    $signevents=array();
    while($row=mysql_fetch_assoc($result)) {      
      $signevents[]=new SignEvent($row['source'],$row['text'],$row['timedate']);
    }
    
    mysql_free_result($result);
	
    return $signevents;
}


class PoisonEvent extends Event  {
  public $victim; 
  
  function __construct($source, $victim, $timedate) {
  	parent::__construct($source, $timedate); 
  	$this->victim=$victim; 	  	  	
  }
  
  function getHtml() {
  	return '<p>'.ucfirst(a_an($this->source)).' <a href="'.rewriteURL('/creature/'.surlencode($this->source).'.html').'">'.htmlspecialchars($this->source).'</a> ' .
    		'poisoned '.$this->getCharacterHtml($this->victim).' at '.date('H:i',strtoTime($this->timedate));
  }
  
}

function getPoisonEvents() {
    $result = mysql_query('SELECT source, param1 as victim,  timedate ' .
    				      'FROM gameEvents WHERE event=\'poison\' and timedate > subtime(now(), \'00:05:00\') limit 3', getGameDB());
    $events=array();
    while($row=mysql_fetch_assoc($result)) {      
      $events[]=new PoisonEvent($row['source'],$row['victim'],$row['timedate']);
    }
    
    mysql_free_result($result);
	
    return $events;
}

class ChangeZoneEvent extends Event  {
  public $zone; 
  
  function __construct($source, $zone, $timedate) {
  	parent::__construct($source, $timedate); 
  	$this->zone=$zone; 	  	  	
  }
  
  function getHtml() {
  	return '<p>'.$this->getCharacterHtml($this->source).' visited '.htmlspecialchars(ucfirst(str_replace('_',' ',$this->zone))).' at '.date('H:i',strtoTime($this->timedate));
  }
  
}

function getChangeZoneEvents() {
    $result = mysql_query('SELECT source, substring(param1,locate(\'_\',param1)+1) as zone,  timedate ' .
    					  'FROM gameEvents WHERE event=\'change zone\' and timedate > subtime(now(), \'00:05:00\') limit 3', getGameDB());
    $events=array();
    while($row=mysql_fetch_assoc($result)) {      
      $events[]=new ChangeZoneEvent($row['source'],$row['zone'],$row['timedate']);
    }
    
    mysql_free_result($result);
	
    return $events;
}

class EquipEvent extends Event  {
  public $zone; 
  
  function __construct($source, $item, $amount, $timedate) {
  	parent::__construct($source, $timedate); 
  	$this->item=$item; 	 
  	$this->amount=$amount; 
  }
  
  function getHtml() {
  	return '<p>'.$this->getCharacterHtml($this->source).' picked up '.$this->getItemPrefix($this->item,$this->amount).' '.htmlspecialchars($this->item).' at '.date('H:i',strtoTime($this->timedate));
  }
  
}
function getEquipEvents() {
    $result = mysql_query('SELECT  source, param1 as item, substring_index(trim(param2),\' \',-1) as amount, timedate       ' .
    					  'FROM gameEvents WHERE event=\'equip\'  and timedate > subtime(now(), \'00:10:00\') and (left(param2,7)=\'content\' or left(param2,4)=\'null\') limit 5', getGameDB());
    $events=array();
    while($row=mysql_fetch_assoc($result)) {      
      $events[]=new EquipEvent($row['source'],$row['item'],$row['amount'],$row['timedate']);
    }
    
    mysql_free_result($result);
	
    return $events;
}
?>