<div class="page-header">
	<div class="pull-right"><a href="<?=site_url("admin/order_data/$orderid") ?>" class="btn"><i class="icon-align-justify"></i> Назад к заказу</a></div>
	<h2>Добавление в заказ</h2>
</div>

<div class="alert alert-info alert-fadeout"><strong>Введите артикульный номер</strong>.</div>

<?=$errors?>

<?=form_open('', 'class="form-inline well"') ?>
	
	<input class="input-xlarge" id="search" name="search" type="text" value="" placeholder="Артикульный номер">
	<button type="submit" class="btn btn-primary" name="vendor_add_new">Поиск <i class="icon-circle-arrow-right icon-white"></i></button>
	
</form>