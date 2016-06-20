<div id="frontCarousel" class="carousel slide">
  <ol class="carousel-indicators">
    <? for ($x=0; $x<$num_slides; $x++): ?>
		<li data-target="#frontCarousel" data-slide-to="<?=$x?>" class="<? if ($x==0): ?>active<? endif ?>"></li>
	<? endfor ?>
  </ol>
  <!-- Carousel items -->
  <div class="carousel-inner">
	<? $x = 0; ?>
	<? foreach ($slides as $slide): ?>
    <div class="<? if ($x == 0): ?>active<? endif ?> item">
		<? if (isset($slide->meta)): ?><a href="<?=htmlspecialchars($slide->meta) ?>"><? endif ?>
		<img src="/e/files/<?=$slide->thumbnail?>">
		<? if (isset($slide->meta)): ?></a><? endif ?>
	</div>
	<? $x++; endforeach; ?>
  </div>
  <!-- Carousel nav -->
  <a class="carousel-control left" href="#frontCarousel" data-slide="prev">&lsaquo;</a>
  <a class="carousel-control right" href="#frontCarousel" data-slide="next">&rsaquo;</a>
</div>
