	<? if (count($results) > 0): ?>

		<div class="alert alert-info sliding-alert"><i class="icon-ok-circle"></i> <strong>Найдено</strong>. Вариантов по запросу: <strong><?=count($results)?></strong></div>
		
		<table class="table table-striped">
			
			<thead>
			<tr>
				<th>Артикул</th>
				<th>Производитель</th>
				<th>Наименование / Описание</th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			
			
			
		<? foreach ($results as $r): ?>

			<tr>
				
				<td class="article_number">
					<?=$r->number ?>
				</td>
				<td><strong><?=$r->brand ?></strong></td>
				<td>
					<?=$r->name ?>
				</td>
				<td class="table-buttons">
					<a href="<?=site_url( 'autopart/' . $r->brand_clear . '/' . $r->number_clear)?>" class="btn btn-small"><i class="icon icon-file"></i> Подробнее</a>
				</td>
			
			</tr>
			
		<? endforeach ?>
		</tbody>
		</table>
		
	<? elseif (isset($is_query_too_short) and $is_query_too_short): ?>
		
		<div class="alert alert-error">
			<i class="icon-info-sign"></i> <strong>Минимальная длина запроса - 3 символа.</strong> Увы.
		</div>
		
	<? else: ?>
		
		<p>Увы, но по вашему запросу ничего не найдено&hellip;</p>
	
	<? endif ?>
	

<div class="clearfix"></div>
