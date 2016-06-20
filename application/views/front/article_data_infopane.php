<? if (!empty($jpegs)): ?>
	
	<? foreach ($jpegs as $jpeg): ?>
	
		<p class="thumbnail"><img src="<?=$jpeg ?>" alt="<?=$title ?>"></p>
		
	<? endforeach ?>
	
	<hr>

<? endif ?>


<? if (!empty($characteristics)): ?>
	
	<div class="well">
	
	<ul>
	
		<? foreach ($characteristics as $ch): ?>

			<li><strong><?=$ch->key ?>:</strong> <?=$ch->value ?></li>

		<? endforeach ?>

	</ul>
	</div>

<? endif ?>

<? if (!empty($pdfs)): ?>
<p>PDF Files</p>
	<ul>
	
	<? foreach ($pdfs as $pdf): ?>
	
		<li><a href="<?=$pdf ?>" target="_blank">PDF-file <?=$pdf ?></a></li>
		
	<? endforeach ?>
	
	</ul>

<? endif ?>