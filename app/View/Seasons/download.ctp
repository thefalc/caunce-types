<?php
 echo "Season Number, Season Name, Premiere Date, Finale Date, Starting Players, Starting Tribes, Tribe 1, Tribe 2, Tribe 3, Tribe 4, Swap Day 1, Swap Day 2, Merge Tribe, Merge Players, FTC, Jury,\n";
 foreach ($seasons as $season):
	echo Util::escapeDoubleQuotes($season['Season']['season_number']).",".
		 Util::escapeDoubleQuotes($season['Season']['season_name']).",".
		 Util::escapeDoubleQuotes($season['Season']['premiere_date']).",".
		 Util::escapeDoubleQuotes($season['Season']['finale_date']).",".
		 Util::escapeDoubleQuotes($season['Season']['starting_players']).",".
		 Util::escapeDoubleQuotes($season['Season']['starting_tribes']).",".
		 Util::escapeDoubleQuotes($season['Season']['tribe1']).",".
		 Util::escapeDoubleQuotes($season['Season']['tribe2']).",".
		 Util::escapeDoubleQuotes($season['Season']['tribe3']).",".
		 Util::escapeDoubleQuotes($season['Season']['tribe4']).",".
		 Util::escapeDoubleQuotes($season['Season']['swap_day1']).",".
		 Util::escapeDoubleQuotes($season['Season']['swap_day2']).",".
		 Util::escapeDoubleQuotes($season['Season']['merge_tribe']).",".
		 Util::escapeDoubleQuotes($season['Season']['merge_players']).",".
		 Util::escapeDoubleQuotes($season['Season']['ftc_count']).",".
		 Util::escapeDoubleQuotes($season['Season']['jury_count']).",".
		 "\n";
endforeach;
?>