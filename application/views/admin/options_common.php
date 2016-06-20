<div class="page-header">
	<div class="pull-right"><a href="<?=site_url('admin/options') ?>" class="btn"><i class="icon-wrench"></i> Все настройки</a></div>
	<h1>Общие настройки</h1>
</div>

<?=$errors?>
<?=$success?>

<?=form_open('admin/options_common', array('id'=>'options_common', 'class'=>'form-horizontal')) ?>
	
	<fieldset>
		<legend>Общие</legend>
		<div class="control-group">
			<label class="control-label" for="title">Заголовок*</label>
			<div class="controls">
				<input class="input-xxlarge" id="title" name="title" type="text" value="<?=setval('title', '', $form); ?>">
				<span class="help-block">Отображается в заголовке при печати счета и на странцах сайта<br>
				<em>Например: Название Компании.</em>
				</span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="subtitle">Подзаголовок*</label>
			<div class="controls">
				<input class="input-xxlarge" id="subtitle" name="subtitle" type="text" value="<?=setval('subtitle', '', $form); ?>">
				<span class="help-block">Отображается в заголовке при печати счета и на странцах сайта<br>
				<em>Например: Слоган Компании.</em></span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="footnote">Копирайт внизу</label>
			<div class="controls">
				<input class="input-xlarge" id="footnote" name="footnote" type="text" value="<?=setval('footnote', '', $form); ?>" placeholder="По умолчанию">
				<span class="help-block">Отображается в нижней части сайта. <i>Оставьте пустым для значения по умолчанию</i>.</span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="adminemail">E-mail администратора*</label>
			<div class="controls">
				<input class="input-xlarge" id="adminemail" name="adminemail" type="text" value="<?=setval('adminemail', '', $form); ?>">
				<span class="help-block">Используется для уведомлений о заказах и иных системных уведомлений</span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label">Параметры</label>
			<div class="controls">
				<label class="checkbox">
					<input type="checkbox" name="hideadmlink" value="1" <?=set_checkbox('hideadmlink','1', $form['hideadmlink'])?>> Спрятать ссылку &laquo;Вход администратора&raquo; на сайте
				</label>
			</div>
		</div>

		
	</fieldset>
	<fieldset>
		<legend>Региональные</legend>
		<div class="control-group">
			<label class="control-label" for="timezone">Часовой пояс</label>
			<div class="controls">
				<?=timezone_menu($form['timezone'], 'input-xlarge', 'timezone')?>
				<span class="help-block">Необходим для отображения времени. <i>Летнее/зимнее время не учитывается.</i></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="timezone">Валюта</label>
			<div class="controls">
				<select name="currency" class="input-xlarge">
				<? foreach ($currency_list as $curid=>$cur): ?>
					<? if (isset($cur[3]) and $cur[3]): ?>
						<option disabled>---</option>
					<? endif ?>
					<option value="<?=$curid?>" <? if ($curid == $form['currency']) echo "selected" ?>><?=$curid?> &mdash; <?=$cur[0]?></option>
				<? endforeach ?>
				</select>
				<span class="help-block">Основная валюта сайта (в ней отображаются все цены и принимаются все заказы)</span>
			</div>
		</div>
	</fieldset>
	
	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Сохранить" name="options_common_submit">&nbsp;<a href="<?=site_url('admin/options')?>" class="btn">Назад</a>
	</div>
</form>
