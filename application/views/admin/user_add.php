<div class="page-header">
	<? if ($edit_id): ?>
		<h1>Редактирование клиента</h1>
	<? else: ?>
		<h1>Добавление клиента</h1>
	<? endif ?>
</div>

<?=$errors?>

<?=form_open("", array('id'=>'user_add_form', 'class'=>'form-horizontal')) ?>

<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#general" data-toggle="tab"><i class="icon-folder-close"></i> Основные данные</a></li>
		<li class="-help-guide -destroyable" data-title="В первый раз?" data-content="Прочитайте нашу пошаговую инструкцию по подготовке файла."><a href="#corporate" data-toggle="tab"><i class="icon-file"></i> Дополнительные данные</a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="general">

			<div class="control-group">
				<? form__label('name', 'Имя/Наименование клиента') ?>
				<div class="controls">
					<input type="text" name="name" id="name" value="<?=setval('name','',$form)?>" class="input-xlarge">
					<p class="help-block"><strong>Обязательно.</strong> Например: <em>Егоров Олег</em> или <em>ООО &laquo;Хэмпстед Рус&raquo;</em>.</p>
				</div>
			</div>
			
			<div class="control-group">
				<? form__label('email2', 'E-mail') ?>
				<div class="controls">
					<input type="text" name="email" id="email2" placeholder="(необязательно)" value="<?=setval('email','',$form)?>">
					<p class="help-block">Например: <em>someone@example.com</em></p>
				</div>
			</div>
			
			
			
			<div class="control-group">
				<? form__label('phone', 'Телефон') ?>
				<div class="controls">
					<input type="text" name="phone" id="phone" placeholder="(необязательно)" value="<?=setval('phone','',$form)?>">
					<p class="help-block">Например: <em>(343) 777-777-1</em></p>
				</div>
			</div>
			
			<div class="control-group">
				<? form__label('address', 'Адрес') ?>
				<div class="controls">
					<input type="text" name="address" id="address" placeholder="(необязательно)" value="<?=setval('address','',$form)?>" class="input-xxlarge">
					<p class="help-block">Например: <em>123000, г. Москва, ул. Тверская, 12-22</em></p>
				</div>
			</div>
			
			<div class="control-group">
				<? form__label('discount', 'Скидка клиента') ?>
				<div class="controls">
					<div class="input-append">
						<input type="number" name="discount" id="discount" value="<?=setval('discount', '0', $form)?>" class="input-mini" min="0" max="100">
						<span class="add-on">%</span>
					</div>
					<p class="help-block"><strong>Обязательно.</strong> Например: <em>5</em> или <em>0</em>. Только цифры.</p>
				</div>
			</div>
		</div>
		
		<div class="tab-pane" id="corporate">
			<div class="control-group">
				<? form__label('corp_inn', 'ИНН / КПП') ?>
				<div class="controls">
					<input type="text" name="corp_inn" id="corp_inn" value="<?=setval('corp_inn','',$form)?>" class="input-large" placeholder="(необязательно)">
					<p class="help-block">Например: <em>7701000000 / 770101001</em></p>
				</div>
			</div>
			
			<div class="control-group">
				<? form__label('corp_ogrn', 'ОГРН / ОГРНИП') ?>
				<div class="controls">
					<input type="text" name="corp_ogrn" id="corp_ogrn" value="<?=setval('corp_ogrn','',$form)?>" class="input-large" placeholder="(необязательно)">
					<p class="help-block">Например: <em>107770100000</em></p>
				</div>
			</div>
			
			<div class="control-group">
				<? form__label('corp_rs', 'Р/с') ?>
				<div class="controls">
					<input type="text" name="corp_rs" id="corp_rs" value="<?=setval('corp_rs','',$form)?>" class="input-large" placeholder="(необязательно)">
					<p class="help-block">Например: <em>40702810000000000000</em></p>
				</div>
			</div>
			
			<div class="control-group">
				<? form__label('corp_bik', 'БИК') ?>
				<div class="controls">
					<input type="text" name="corp_bik" id="corp_bik" value="<?=setval('corp_bik','',$form)?>" class="input-large" placeholder="(необязательно)">
					<p class="help-block">Например: <em>040000000</em><br>Корр. счет, и данные банка будут загружены автоматически</p>
				</div>
			</div>
			
			
		</div>
		
	</div>
	
	<div class="form-actions">
		<? if ($attach_to_order): ?>
			
			<? if ($edit_id): ?>
				<button type="submit" class="btn btn-primary"><i class="icon-ok-sign icon-white"></i> Сохранить</button>
			<? else: ?>
				<button type="submit" class="btn btn-primary"><i class="icon-ok-sign icon-white"></i> Прикрепить клиента к заказу</button>
			<? endif ?>
			
			<a href="<?=site_url("admin/order_data/$attach_to_order")?>" class="btn">Вернуться к заказу</a>
		<? else: ?>
			
			<? if ($edit_id): ?>
				<button type="submit" class="btn btn-primary"><i class="icon-ok-sign icon-white"></i> Сохранить</button>
			<? else: ?>
				<button type="submit" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i> Добавить клиента</button>
			<? endif ?>
			
			<a href="<?=site_url("admin/users")?>" class="btn">Список клиентов</a>
		<? endif ?>
		
		<!--<button class="btn" type="reset">Сброс</button>-->
		
	</div>

</form>