

<? if (!$order->is_archived): ?>
	<?=form_open('admin/order_update/' . $order->order_id, 'id="order_items_update_form" class="js-form js-form-button-hide"') ?>
<? endif ?>

<? if (empty($order_items)): ?>
	<div class="well">
		<button id="save-order-btn-2" type="submit" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i> Сохранить изменения</button>
		<div class="btn-group js-form-toggleable">
			<a href="<?=(!empty($order->user_vehicle_id))?site_url("find/{$order->vehicle_brand_id}/{$order->vehicle_model_id}/{$order->vehicle_type_id}"):site_url("") ?>" class="btn btn-primary order-add-line" data-orderid="<?=$order->order_id?>" data-orderhumanid="<?=$order->order_human_id?>"><i class="icon-plus-sign icon-white"></i> Добавить запчасти по каталогу</a>
			<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li><a href="<?=(!empty($vehicle_title))?site_url("find/$vehicle_brand_id/$vehicle_model_id/$vehicle_type_id"):site_url("") ?>" class="order-add-line" data-orderid="<?=$order->order_id?>" data-orderhumanid="<?=$order->order_human_id?>">Найти запчасти по каталогу</a></li>
				<li><a href="#add_item" class="" data-toggle="modal" data-target="#add_item" title="Добавить позицию к заказу">Указать данные вручную</a></li> 
			</ul>
		</div>

		<input type="hidden" name="data[new][art_number]" value="">
		<input type="hidden" name="data[new][sup_brand]" value="">
		<input type="hidden" name="data[new][description]" value="">
		<input type="hidden" name="data[new][qty]" value="1">
		<input type="hidden" name="data[new][price]" placeholder="">
		
	</div>
<? else: ?>
	<table id="orders_list" class="table <? if (!$order->is_archived): ?>table-striped<?endif?>">
		<thead>
			<tr>
				<th>
					<? if (!$order->is_archived): ?>
						<select name="status" class="input-small order_change_status" title="Смена статуса всех позиций">
							<? foreach ($order_statuses as $stid=>$st): ?>
								<option value="<?=$stid?>"><?=$st['name']?></option>
							<? endforeach ?>
						</select>
					<? endif ?>
				</th>
				<th>Склад</th>
				<th>Арт.</th>
				<th>Бренд</th>
				<th>Описание</th>
				<th>Кол.</th>
				<th>Цена</th>
				<th>Скидка (%)</th>
				<th>&sum; (-%)</th>
				
			</tr>
		</thead>
		<tbody>
		<? foreach ($order_items as $item): ?>
			<tr>
				<td>
					<? if (!$order->is_archived): ?>
						<select name="data[<?=$item->order_line_id?>][status]" class="input-small order_line_status">
							<? foreach ($order_statuses as $stid=>$st): ?>
								<option value="<?=$stid?>" <?=($item->status == $stid)?"selected":""?>><?=$st['name']?></option>
							<? endforeach ?>
						</select>
					<? else: ?>
						<?=$order_statuses[$item->status]['name']?>
					<? endif ?>
				</td>
				<td><?=$item->vendor_name?></td>
				<td><?=$item->art_number?></td>
				<td><?=$item->sup_brand?></td>
				<td><?=$item->description?></td>
				<td>
					<? if (!$is_archived): ?>
						<input type="text" min="0" class="input-mini" name="data[<?=$item->order_line_id?>][qty]" value="<?=$item->qty?>">
					<? else: ?>
						<?=$item->qty?>
					<? endif ?>
				</td>
				<td>
					<? if (!$is_archived): ?>
						<input type="text" class="input-mini" name="data[<?=$item->order_line_id?>][price]" value="<?=$item->price?>">
					<? else:?>
						<?=$item->price?>
					<? endif ?>
				</td>
				<td>
					<? if (!$is_archived): ?>
						<input type="number" min="0" max="100" class="input-mini" name="data[<?=$item->order_line_id?>][discount]" value="<?=$item->discount?>">
					<? else: ?>
						<?=$item->discount?>
					<? endif ?>
				</td>
				<td>
					<?=price_format($item->item_subtotal, $item->discount) ?>
					<input type="hidden" name="data[<?=$item->order_line_id?>][delivery_method]" value="<?=$item->delivery_method ?>" class="delivery-method-field delivery-method-field-<?=$item->order_line_id?>">
				</td>
				
			</tr>
		<? endforeach ?>
		
		<? if (!$order->is_archived): ?>
			<tr class="table-new-line">
				<td><span class="label">Новая</span><br><span class="label">Позиция <i class="icon-circle-arrow-right icon-white"></i></span></td>
				<td>
					<select name="data[new][vendor_name]" class="input-mini">
						<? foreach ($vendors as $vendor): ?>
							<option><?=$vendor ?></option>
						<? endforeach ?>
					</select>
				</td>
				<td>
					<input type="text"  class="input-mini" name="data[new][art_number]" placeholder="Арт.">
				</td>
				<td>
					<input type="text"  class="input-mini" name="data[new][sup_brand]" placeholder="Бренд">
				</td>
				<td>
					<input type="text"  class="input-small" name="data[new][description]" placeholder="Описание">
				</td>
				<td>
					<input type="number" min="1" class="input-mini" name="data[new][qty]" placeholder="Кол." value="1">
				</td>
				<td>
					<input type="text"  class="input-mini" name="data[new][price]" placeholder="Цена">
				</td>
				<td></td>
				<td></td>
			
			</tr>
		<? endif ?>
		
		</tbody>
		<caption>
			<tr>
				<td colspan="6">
					<? if (!$order->is_archived): ?>
						<button id="save-order-btn-2" type="submit" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i> Сохранить изменения</button>
						

						<div class="btn-group js-form-toggleable">
							<a href="#add_item" class="btn btn-default js-form-toggleable" data-toggle="modal" data-target="#add_item" title="Добавить позицию к заказу"><i class="icon-plus-sign"></i> Добавить к позицию заказу</a>
							<button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
							<ul class="dropdown-menu pull-right">
								<li><a href="#add_item" data-toggle="modal" data-target="#add_item"><i class="icon-plus-sign"></i> Добавить к позицию заказу</a></li>
								<li><a href="<?=site_url('admin/order_archive/' . $order->order_id)?>"><i class="icon-inbox"></i> Архивировать заказ</a></li>
							</ul>
						</div>

					
					<? elseif ($order->is_archived): ?>
						<a href="<?=site_url('admin/order_archive/' . $order->order_id . '/rollback')?>" class="btn"><i class="icon-inbox"></i> Разархивировать</a>
					<? endif ?>
				</td>
				<td colspan="2" class="order_total_key">
					ИТОГО:<br>
					ИТОГО (с доставкой):
				</td>
				<td class="order_total_val">
					<?=price_format($order->order_items_total_f)?><br>
					<?=price_format($order->order_items_total_dlvr) ?>
				</td>
			</tr>
			
		</caption>
	</table>
<? endif ?>

<? if (!$order->is_archived): ?>
	</form>
<? endif ?>
<div id="add_item" class="modal hide fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Добавить позицию</h3>
	</div>
	<div class="modal-body">
		<div class="alert alert-info">
			<i class="icon-info-sign"></i> Можно найти позицию по номеру <a href="<?=site_url("admin/order_add_item/{$order->order_id}")?>" class="btn btn-default btn-mini"><i class="icon-search"></i> Поиск по номеру</a>
		</div>
		<form class="form-horizontal" method="POST">
		
		<div class="control-group">
			<label class="control-label">Склад</label>
			<div class="controls">
			<select name="data[new][vendor_name]" class="input-medium">
				<? foreach ($vendors as $vendor): ?>
					<option><?=$vendor ?></option>
				<? endforeach ?>
			</select>
			</div>
		</div>
		<div class="control-group">
			<label for="add_item_number" class="control-label">Артикул</label>
			<div class="controls">
				<input id="add_item_number" type="text" class="input-medium" name="data[new][art_number]" placeholder="(опционально)">
			</div>
		</div>
		<div class="control-group">
			<label for="add_item_brand" class="control-label">Бренд</label>
			<div class="controls">
				<input id="add_item_brand" type="text"  class="input-medium" name="data[new][sup_brand]" placeholder="(опционально)">
			</div>
		</div>
		<div class="control-group">
			<label for="add_item_description" class="control-label"><b>Описание*</b></label>
			<div class="controls">
				<input id="add_item_description" type="text"  class="input-large" name="data[new][description]" placeholder="Описание">
			</div>
		</div>
		<div class="control-group">
			<label for="add_item_price" class="control-label">Цена</label>
			<div class="controls">
				<input id="add_item_price" type="text"  class="input-mini" name="data[new][price]" placeholder="Цена" value="0.00">
			</div>
		</div>
		</form>
	</div>
	<div class="modal-footer">
		<!--<button class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</button>-->
		<button class="btn btn-primary" data-dismiss="modal" type="submit"><i class="icon-chevron-down icon-white"></i> Окей</button>
	</div>
</div>