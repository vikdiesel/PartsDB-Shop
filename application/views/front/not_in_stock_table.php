<? if ($num_not_in_stock > 0): ?>

	<p><strong>Справочно.</strong> Список артикульных номеров по которым проверялось наличие.</p>
	
	<table class="table table-striped table-condensed not_in_stock" data-not_in_stock="">
		
		<thead>
		<tr>
			
			<th>Артикул</th>
			<th>Производитель</th>
			<th>Наименование/Описание</th>
			<th>Статус</th>

		</tr>
		</thead>
		<tbody>
		
	<? foreach ($arts_not_in_stock as $art): ?>

		<tr class="article_not_in_stock article article_<?=$art->number_clear?>_<?=$art->brand_clear?>" data-art_number_clear="<?=$art->number_clear?>" data-sup_brand_clear="<?=$art->brand_clear?>">
		
			<td><?=$art->number ?></td>
			<td><?=$art->brand ?></td>
			<td><?=$art->name ?></td>
			<td>
				<? if (isset($art->cross_article)): ?>
					Кросс
				<? else: ?>
					<?=$art->status ?>
				<? endif ?>
			</td>
			
			<td class="stock-buttons">
				<? if (!$art->primary): ?>
					<a href="<?=site_url( "autopart/{$art->brand_clear}/{$art->number_clear}" ) ?>" class="btn btn-small article-more-button"><i class="icon-file"></i> Аналоги</a>
				<? endif ?>
			</td>
		
		</tr>
		
	<? endforeach ?>
	
	</tbody>
	</table>
	
<? endif ?>