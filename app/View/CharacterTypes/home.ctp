<div id="character-types">
	<div class="container" style="padding-bottom: 60px;">
        <div class="row">
            <div class="text-center col-sm-10 col-md-10 col-lg-8 col-lg-offset-2 col-md-offset-1 col-sm-offset-1" style="padding:0px;">
              <h1>Caunce Character Type Definitions</h1>
            </div>
        </div>

        <div class="row">
        	<div class="col-sm-12">
        		<h2>Men</h2>
        		<? foreach($male_character_types as $character_type): ?>
        			<div style="margin-bottom: 20px;">
	    				<h4><a href="/players/home/character_type:<?= $character_type['CharacterType']['id']; ?>" title="<?= $character_type['CharacterType']['character_type']; ?>"><?= $character_type['CharacterType']['character_type']; ?></a></h4>
						<p class="text-muted"><?= $character_type['CharacterType']['description']; ?></p>
					</div>
	    		<? endforeach; ?>

			   	<h2 style="margin-top: 30px;">Women</h2>
        		<? foreach($female_character_types as $character_type): ?>
        			<div style="margin-bottom: 20px;">
	    				<h4><a href="/players/home/character_type:<?= $character_type['CharacterType']['id']; ?>" title="<?= $character_type['CharacterType']['character_type']; ?>"><?= $character_type['CharacterType']['character_type']; ?></a></h4>
						<p class="text-muted"><?= $character_type['CharacterType']['description']; ?></p>
					</div>
	    		<? endforeach; ?>
        	</div>
        </div>
    </div>
</div>

