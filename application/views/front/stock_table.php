	<? if ($num_arts_in_stock == 0 and !$is_require_apis): ?>

		<div class="alert alert-error"><i class="icon-remove-sign"></i> <strong>Извините</strong>, запрошенных артикулов нет в наличии.</div>
		<p>
			<a class="btn btn-primary" href="<?=site_url('auto/search_inquiry')?>"><i class="icon-edit icon-white"></i> Заявка</a>
			<a class="btn" href="#similar" id="not-in-stock-toggle"><i class="icon-refresh"></i> Перечень артикулов</a>
			<span class="help-inline">Перечень артикулов, по которым проверялось наличие. <strong>Справочно.</strong></span>
		</p>

	<? else: ?>	
		
		<div id="alert_no_primarynr" class="alert utilityAlerts hiddenUtilityMessage"><i class="icon-remove-sign"></i> <strong>Запрошенного артикула нет в наличии</strong>, но, похоже, есть аналоги.</div>
		
		<? if (isset($primary_art_number)): ?>
			
			<table id="stock" class="table table-striped primary_number_present"  data-primarynr="<?=$primary_art_number?>">
			
		<? else: ?>
			
			<table id="stock" class="table table-striped primary_number_void">
			
		<? endif ?>
		<thead>
		<tr>
			
			<th data-sort="string">Артикул</th>
			<th data-sort="string">Бренд</th>
			<th>Наименование/Описание</th>
			<th data-sort="int">Налич</th>
			<th data-sort="int">Срок</th>
			<th data-sort="float" class="primary-sort">Цена</th>
			<th></th>

		</tr>
		</thead>
		<tbody>
		

		<? foreach ($arts_in_stock as $stock_art): ?>
				
			<tr class="article_info article_in_stock vendor_delivery_days_<?=$stock_art->vendor_delivery_days?> <? if ($stock_art->brands_match === FALSE) echo "brands_mismatch muted"; elseif ($stock_art->brands_match === 'neutral') echo "brands_match_neutral muted"; ?> <? if ($stock_art->primary) echo "primary_article" ?> <? if (!$stock_art->primary and $stock_art->brands_match === TRUE) echo "normal_article" ?>" data-art_number_clear="<?=$stock_art->number_clear?>" data-stock_brand_clear="<?=$stock_art->brand_clear?>" data-discountprice="<?=$stock_art->discount_price ?>">
			
				<td class="art_status article_number" data-sort-value="<?=$stock_art->number_prc_clear?>">
					<i class="icon-star"></i><i class="icon-star-empty" title=""></i><i class="icon-question-sign"></i><i class="icon-remove-circle"></i>
					<?=$stock_art->number_prc ?>
				</td>
				<td data-sort-value="<?=$stock_art->brand_prc_clear ?>"><strong><?=$stock_art->brand_prc ?></strong><? if (_tmplt_hdata('is_admin') === TRUE): ?><br><abbr title="Наименование поставщика (скрыто для пользователей)"><?=$stock_art->vendor_name ?></abbr><? endif?></td>
				<td>
					
					<?=$stock_art->name_prc ?><br>
					
					<? if ($stock_art->brands_match === FALSE): ?>
						<span class="label stock-label" title="Бренды в базе аналогов и в прайсе по данной позиции не совпадают">Несовпадение по бренду:</span> <?=$stock_art->brand ?> &ne; <?=$stock_art->brand_prc ?>
					<? elseif ($stock_art->brands_match === 'neutral'): ?>
						<span class="label stock-label" title="Найдено по кросс-номеру без проверки соответствия бренда">Найден по кросс-номеру</span> (бренд не указан)
					<? elseif ($stock_art->primary): ?>
						<span class="label label-success stock-label" title="Позиция соответствует искомой">Соответствует искомой</span>
					<? elseif (!isset($primary_art_number)): ?>
						<span class="label label-success stock-label" title="Позиция соответствует искомой">Соответствует искомой</span>
					<? else: ?>
						<span class="label label-info stock-label" title="Аналог искомой позиции">Аналог искомой</span>
					<? endif ?>
					
				</td>
				<td data-sort-value="<?=$stock_art->qty ?>"><abbr title="Обновлено <?=$stock_art->vendor_last_update_readable ?>"><?=$stock_art->qty?> шт.</abbr></td>
				<td data-sort-value="<?=$stock_art->vendor_delivery_days ?>" class="delivery_days"><abbr title="Средний срок поставки запчасти со склада"><?=$stock_art->vendor_delivery_days?> дн.</abbr></td>
				<td data-sort-value="<?=$stock_art->discount_price ?>" class="article_price">
					<?=price_format($stock_art->discount_price, $stock_art->discount)?>
					<br>
					<span class="muted price_update_date">на <?=$stock_art->vendor_last_update_readable?></span></td>
				<td class="stock-buttons">
					<? if (!$stock_art->primary): ?>
						<a href="<?=site_url( "autopart/{$stock_art->brand_prc_clear}/{$stock_art->number_prc_clear}") ?>" class="btn btn-small"><i class="icon-plus"></i> Аналоги</a>
					<? endif ?>
					<a href="<?=site_url( 'cart/add/' . $stock_art->prices_row_id )?>" class="add_to_cart_link btn btn-small btn-primary" title="В корзину"><i class="icon-shopping-cart icon-white"></i></a>
				</td>
			
			</tr>
		
			
		<? endforeach ?>
		
		</tbody>
		</table>
		
		<? if (!$all_brands_are_similar): ?>
			<div class="alert alert-block">
				<h4 class="alert-heading">Почему некоторые позиции отмечены бледным?</h4>
				<p>Один и тот же номер у разных производителей может обозначать абсолютно разные запасные части.</p>
				<p><strong>По позициям, отмеченным бледным сопоставление по бренду дало негативный результат</strong>, либо в некоторых базах аналогов бренд не указан вовсе, и поиск наличия производится исключительно по номеру.
				</p>
			</div>
		<? endif ?>
		
		<? if (!is_authorized()): ?>
			<div class="alert"><i class="icon-question-sign"></i> <strong>Цены без скидки</strong>. Чтобы увидеть цены с учетом вашей скидки, - <a href="<?=site_url('user/login/' . bcklnk_mask('user/login'))?>" title="Щелкните здесь для авторизации" class="btn btn-mini"><i class="icon-user"></i> авторизуйтесь</a></div>
		<? elseif ($discount > 0): ?>
			<div class="alert alert-info"><i class="icon-info-sign"></i> <strong>Цены со скидкой.</strong> Цены показываются с учетом вашей скидки <strong>(<?=$discount?>%)</strong>.</div>
		<? endif ?>
		
	<? endif ?>