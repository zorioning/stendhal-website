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
 * There is a Quest that request the player to identify a fish.
 * So to avoid revealing the quest the fish image is replaced 
 * with a generic one.
 *
 * @param string $resource
 */
function hideFishes($resource) {
  $shouldHide=false;
  
  $listOfFishes=array(
    '/arctic_char.png',
    '/clown-fish.png',
    '/cod.png',
    '/mackerel.png',
    '/perch.png',
    '/roach.png',
    '/surgeonfish.png',
    '/trout.png',
  );

  foreach($listOfFishes as $fish) {
    if(!(strpos($resource,$fish)===false)) {
      $shouldHide=true;
      break;
    }
  }
  
  $result=$resource;
  if($shouldHide) {
    $result="images/game/generic_fish.png";
  }
  
  return $result;
}

$url = $_GET['url'];

/*
 * We want to hide the fishes so we don't spoil the fisherman quest.
 */
$url = hideFishes($url);

$result=imagecreate(32,32);

$white=imagecolorallocate($result,255,255,255);
imagefilledrectangle($result, 0,0,32,32,$white);

$baseIm=imagecreatefrompng($url);
imagecopy($result,$baseIm,0,0,0,0,32,32);

header("Content-type: image/png");
imagepng($result);

?>