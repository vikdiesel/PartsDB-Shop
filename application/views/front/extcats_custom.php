<? if (count($brands_txt) > 0): ?>
	<? foreach ($brands_txt as $cat): ?><a href="<?=$cat->meta ?>" class="btn btn-large genucat_btn ttp" title="Оригинальный каталог <?=$cat->title?>"><?=$cat->title?></a><? endforeach ?>
<? endif ?>