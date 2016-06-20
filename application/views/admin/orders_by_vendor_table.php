<table id="orders_list" class="table <? if (!$is_archived): ?>table-striped<?endif?>">
	<thead>
		<tr>
			<th>№</th>
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
	
	<? foreach ($order as $item): ?>
		<tr class="<? if ($item->vendor_name != $prev_vendor_name): ?>divider<? endif ?>">
			<td>№<?=$item->order_human_id ?></td>
			<td><?=$item->vendor_name?></td>
			<td><?=$item->art_number?></td>
			<td><?=$item->sup_brand?></td>
			<td><?=$item->description?></td>
			<td><?=$item->qty?></td>
			<td><?=$item->price?></td>
			<td><?=$item->discount?></td>
			<td><?=price_format($item->item_subtotal, $item->discount) ?></td>
			<td class="table-row-actions">
				<? if ($item->vendor_name != $prev_vendor_name): ?>
					
					<? if ($item->is_orderemail_available): ?>
						<a href="<?=site_url('admin/orders_email_to_vendor/' . $item->vendor_id)?>" class="btn btn-primary btn-sm btn-small"><i class="icon-envelope icon-white"></i> E-mail</a>
					<? endif ?>
					<a href="<?=site_url('admin/orders_by_vendor_csv/' .  $item->vendor_id)?>" class="btn btn-default btn-sm btn-small"><i class="icon-list-alt"></i> CSV</a>
					
				<? endif ?>
			</td>
		</tr>
		
		
		
		
		<? $prev_vendor_name = $item->vendor_name; ?>
		<? $this_vendor_textual_order = ''; ?>
		
	<?endforeach?>
	
	</tbody>
	
</table>