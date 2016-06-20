<div class="page-header">
	<div class="pull-right"><a href="<?=site_url('admin/options') ?>" class="btn"><i class="icon-wrench"></i> Все настройки</a></div>
	<h1>Вставка кода</h1>
</div>

<? if ($action == 'update'): ?>
	<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Успешно.</strong> Данные обновлены.</div>
<? else: ?>
	<div class="alert alert-block alert-info">
		<h4>Для чего нужна эта форма?</h4>
		<p>Данная форма позволяет вставить произвольный код HTML/JS/CSS на ваш сайт (код появится сразу на всех страницах).</p>
		<p>Это необходимо для подключения <a href="http://www.jivosite.ru/?pid=1806" target="_blank">Живого чата</a>, <a href="http://metrika.yandex.ru/" target="_blank">Яндекс.Метрики</a>, <a href="http://webmaster.yandex.ru/" target="_blank">Яндекс.Вебмастера</a>, <a href="https://www.google.com/analytics/" target="_blank">Google Analytics</a> или любой другой сторонней системы.</p>
	</div>
	<div class="alert"><i class="icon-exclamation-sign"></i> <strong>Будьте осторожны!</strong> Незакрытый тег может нарушить работу вашего сайта.</div>
<? endif ?>

<?=form_open('admin/options_header_footer/update', 'class="form-horizontal"') ?>

	<div class="control-group">
		<label class="control-label" for="js_header">Вставка кода между тегами &lt;head&gt;&lt;/head&gt;</label>
		<div class="controls">
			<textarea class="input-xxlarge" id="js_header" name="js_header" rows="10"><?=setval('js_header','',$form); ?></textarea>
			<span class="help-block"><i class="icon-info-sign"></i> Рекомендуется для Google Analytics и тегов верификации владельца Яндекс.Вебмастер</span>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="js_footer">Вставка кода внизу, практически перед тегом &lt;/body&gt;</label>
		<div class="controls">
			<textarea class="input-xxlarge" id="js_footer" name="js_footer" rows="10"><?=setval('js_footer','',$form); ?></textarea>
			<span class="help-block"><i class="icon-info-sign"></i> Рекомендуется для Яндекс.Метрики и Живого чата</span>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="js_sidebar">Вставка кода в левую колонку</label>
		<div class="controls">
			<textarea class="input-xxlarge" id="js_sidebar" name="js_sidebar" rows="10"><?=setval('js_sidebar','',$form); ?></textarea>
			<span class="help-block"><i class="icon-info-sign"></i> Счетчики, логотипы платежных систем, формы подписки, Вконтакте, Twitter и т.д.</span>
		</div>
	</div>
	
	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Сохранить</button>
		<a href="<?=site_url('admin/options')?>" class="btn">Назад</a>
	</div>
	
</form>