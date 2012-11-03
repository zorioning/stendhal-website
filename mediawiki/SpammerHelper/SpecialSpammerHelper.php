<?php
class SpecialSpammerHelper extends SpecialPage {
        function __construct() {
                parent::__construct( 'SpammerHelper' );
        }
 
        function execute( $par ) {
                $this->setHeaders();
                global $wgOut; // this is where we can put our output
        		$wgOut->addWikiText('==Special Page to deal with notorious spammers==');
        		$wgOut->addWikiText('<p>The following table shows accounts that have more than one deleted contribution:</p>');
        		$dbr = wfGetDB( DB_SLAVE );
        		$tables = array('user', 'archive', 'ipblocks');
        		$vars = array('user_name', 'COUNT(ar_title) archived', 'user_editcount edits');
        		$conds = array('ipb_user IS NULL', "ar_timestamp > '20120101000000'");
        		$options = array('GROUP BY' => 'user_name', 'HAVING' => 'archived > 1 AND NOT edits > archived',
        						'ORDER BY' => 'user_name');
        		$join_conds = array('archive' => array('JOIN', 'user_id = ar_user'), 
        						'ipblocks' => array('LEFT JOIN', 'user_id = ipb_user'));
        		$limit = 50;
        		$res = $dbr->select($tables, $vars, $conds, __METHOD__, $options, $join_conds);
        		foreach ($res as $row) {
        			$wgOut->addWikiText('<p>');
        			$display = '<b>'.$row->user_name.'</b>';
        			$wgOut->addWikiText($display);
        		}
        }
}