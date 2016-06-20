<a name="form"></a>
<div class="page-header">
	<h1>Корзина <small>Содержимое корзины и оформление заказа</small></h1>
</div>

<div class="cart_forms">

	<?=form_open('cart/update', 'class="form-inline" id="twof_cart_form"')?>

	<div class="alert alert-info update_cart_hide_slide">
		<i class="icon-info-sign"></i> <strong>Укажите количество и выберите способ доставки.</strong> После обновления можно будет заполнить форму ниже.
	</div>
	
	<div id="update_cart_alert" class="alert alert-warning hiddenUtilityMessage update_cart_slide">
		<i class="icon-exclamation-sign"></i> <strong>Не забудьте обновить корзину</strong> для сохранения изменений. <button type="submit" class="btn btn-warning btn-mini btn-sm">Обновить</button>
	</div>

	<table id="cart_list" class="table">
		<thead>
		<tr>
			<th>Наименование товара</th>
			<th>Наличие</th>
			<th>Кол-во</th>
			<th>Поставка</th>
			<th>Цена</th>
			<th>Сумма</th>
		</tr>
		</thead>
		<tbody>
	<? foreach ($items as $item): ?>
		<tr class="cart_item">
			<td class="item_title"><label for="qty_<?=$item->id?>"><?=$item->art_number?> <?=$item->sup_brand?> <?=$item->description?></label></td>
			<td class="prices_qty"><abbr title="Количество в наличии"><?=$item->qty_limit ?> шт.</abbr></td>
			<td class="qty"><input type="number" min="0" max="<?=$item->qty_limit?>" name="qty[<?=$item->id?>]" value="<?=$item->qty?>" id="qty_<?=$item->id?>" maxlength="4" class="input-mini"><span class="help-inline"><a href="#delete-item" class="btn disabled cart-delete-item" title="Пометить на удаление"><i class="icon-trash"></i></a></span></td>
			<td class="delivery_days"><abbr title="Средний срок поставки"><?=$item->delivery_days?> дн.</abbr></td>
            <td class="price" data-price="<?=$item->price?>"><?=price_format($item->price)?></td>
			<td class="subtotal">
				<strong>
				<? if ($discount > 0): ?>
					<?=price_format($item->subtotal*$dm, $discount) ?>
				<? else: ?>
					<?=price_format($item->subtotal) ?>
				<? endif ?>
				</strong>
			</td>
		</tr>
	<? endforeach ?>
	
	
	
	<? foreach ($d_mthds as $d_mthd): ?>
		<tr class="delivery_method <? if ($d_mthd->is_checked):?>delivery_method_active<?endif?>">
			<td class="d_mthd_title" colspan="5"><label for="delivery_<?=$d_mthd->id?>" class="radio">&nbsp;<?=form_radio('delivery', $d_mthd->id, $d_mthd->is_checked, 'id="delivery_'.$d_mthd->id.'"')?><?=$d_mthd->title ?></label></td>
			<td class="d_mthd_price" data-price="<?=$d_mthd->price?>"><?=price_format($d_mthd->price) ?></td>
		</tr>
	<? endforeach ?>
	
	
	
		<tr>
			<td colspan="5" class="total_label">Итого (с учетом доставки):</td>
			<td class="total">
				<?=price_format($total)?>
			</td>
		</tr>
		</tbody>
	</table>

    <a href="#order_add_comment" class="btn update_cart_hide_fade hiddenUtilityMessage" data-toggle="modal"><i class="icon-comment"></i> Добавить комментарий к заказу</a>

    <span id="update_cart_block" class="update_cart_fade">
        <button type="submit" id="update_cart_btn" class="btn btn-primary">Обновить корзину</button> <span class="help-inline">Щелкните для изменения количества и/или способа доставки.</span>
    </span>
	

	
</form>
</div>

<!-- order_add_comment -->
<div id="order_add_comment" class="modal hide fade" tabindex="-1" role="dialog">
    <form id="order_add_comment_form">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Комментарий к заказу</h3>
    </div>

    <div class="modal-body">
        <div class="control-group">
            <? form__label('form_order_comment', 'Комментарий к заказу') ?>
            <div class="controls">
                <textarea class="input-block-level" id="form_order_comment"></textarea>
                <p class="help-block">Например: <em>Прошу позвонить с 9.00 до 12.00</em></p>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Отмена</button>
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </div>
    </form>
</div>

<hr>