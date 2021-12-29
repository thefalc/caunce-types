<?php
 echo "First Name, Last Name, Nickname, Twitter, Occupation, Sex, Image, Wikia, Location, Season Name, Season Number, Character Type, Age, Placement, Days Lasted, Votes Against, Starting Tribe, Swapped Tribe, Med Evac, Quit, Tribe Wins, Individual Wins,\n";
 foreach ($players as $player):
	echo Util::escapeDoubleQuotes($player['Player']['fname']).",".
		 Util::escapeDoubleQuotes($player['Player']['lname']).",".
		 Util::escapeDoubleQuotes($player['Player']['nickname']).",".
		 Util::escapeDoubleQuotes($player['Player']['twitter']).",".
		 Util::escapeDoubleQuotes($player['Player']['occupation']).",".
		 Util::escapeDoubleQuotes($player['Player']['sex']).",".
		 Util::escapeDoubleQuotes($player['Player']['image_url']).",".
		 Util::escapeDoubleQuotes($player['Player']['wikia_url']).",".
		 Util::escapeDoubleQuotes($player['Player']['location']).",".
		 Util::escapeDoubleQuotes($player['Season']['season_name']).",".
		 Util::escapeDoubleQuotes($player['Season']['season_number']).",".
		 Util::escapeDoubleQuotes($player['CharacterType']['character_type']).",".
		 Util::escapeDoubleQuotes($player['PlayersSeason']['age_show']).",".
		 Util::escapeDoubleQuotes($player['PlayersSeason']['placement']).",".
		 Util::escapeDoubleQuotes($player['PlayersSeason']['day_voted_out']).",".
		 Util::escapeDoubleQuotes($player['PlayersSeason']['votes_against']).",".
		 Util::escapeDoubleQuotes($player['PlayersSeason']['starting_tribe']).",".
		 Util::escapeDoubleQuotes($player['PlayersSeason']['swapped_tribe']).",".
		 Util::escapeDoubleQuotes($player['PlayersSeason']['med_evac']).",".
		 Util::escapeDoubleQuotes($player['PlayersSeason']['quit']).",".
		 Util::escapeDoubleQuotes($player['PlayersSeason']['tribe_wins']).",".
		 Util::escapeDoubleQuotes($player['PlayersSeason']['individual_wins']).",".
		 "\n";
endforeach;
?>