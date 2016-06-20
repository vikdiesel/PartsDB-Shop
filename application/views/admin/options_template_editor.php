<div class="page-header">
	<div class="pull-right"><a href="<?=site_url('admin/options') ?>" class="btn"><i class="icon-wrench"></i> Все настройки</a></div>
	<h1>Редактор шаблона сайта</h1>
</div>

<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#editor" data-toggle="tab"><i class="icon-edit"></i> Редактор</a></li>
		<li class="help-guide destroyable" <? if (!$action): ?>data-title="В первый раз?" data-content="Инструкция познакомит вас с назначением используемых в шаблоне переменных." <? endif?>><a href="#how-to" data-toggle="tab"><i class="icon-info-sign"></i> Инструкция</a></li>
	</ul>
	
	<div class="tab-content">
	
		<div class="tab-pane active" id="editor">

			<? if ($action == 'update'): ?>
				<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Успешно.</strong> Данные обновлены.</div>
			
			<? elseif ($action == 'reset'): ?>
				<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Успешно.</strong> Восстановлен шаблон по умолчанию.</div>	
			
			<? else: ?>
				<div class="alert alert-block alert-info">
					<h4>Для чего нужна эта форма?</h4>
					<p>Шаблон &mdash; повторяющиеся элементы страницы сайта (шапка, подвал, боковая колонка, блок меню и т.д.). Если вам хочется что-то добавить или изменить &mdash; это можно сделать здесь.</p>
					<p>В случае, если вам кажется, что вы что-то испортили, &mdash; внизу есть кнопка восстановления шаблона по-умолчанию.</p>
				</div>
				<div class="alert"><i class="icon-exclamation-sign"></i> <strong>Будьте осторожны!</strong> Необходимо как минимум базовое знание HTML/CSS.</div>
			<? endif ?>

			<?=form_open('admin/options_template_editor/update', 'class="form-inline"') ?>

				<div class="control-group">
					<div class="controls">
						<textarea id="template" name="template" class="input-block-level" rows="40" wrap="off"><?=setval('template','',$form); ?></textarea>
					</div>
				</div>
				
				<div class="form-actions">
					<button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> Сохранить</button>
					<a href="<?=site_url('admin/options_template_editor/reset')?>" class="btn btn-danger"><i class="icon-trash icon-white"></i> Восстановить шаблон по-умолчанию</a>
					<span class="help-inline"><a href="<?=site_url('admin/options')?>">Все настройки</a></span>
				</div>
				
			</form>
		
		</div>
		
		<div class="tab-pane" id="how-to">
			
			<p>Для редактирования шаблона необходимо как минимум базовое знание HTML/CSS. Ниже представлен список переменных, используемых в шаблоне.</p>
			
			<h3>{pagetitle}</h3>
			<p>Заголовок страницы. Предназначен для использования в теге &lt;title&gt;</p>

			<h3>{meta_author}</h3>
			<p>Генерирует мета-поле author.</p>

			<h3>{meta_timestamp}</h3>
			<p>Генерирует мета-поле с отметкой времени создания страницы.</p>

			<h3>{bootstrap_css}</h3>
			<p>Ссылка на css-файл Bootstrap</p>

			<h3>{general_css}</h3>
			<p>Ссылка на css-файл General</p>
			
			<h3>{jquery_js}</h3>
			<p>Ссылка на js-файл jQuery</p>

			<h3>{jquery_ui_js}</h3>
			<p>Ссылка на js-файл jQuery UI</p>

			<h3>{bootstrap_js}</h3>
			<p>Ссылка на js-файл Bootstrap</p>

			<h3>{general_js}</h3>
			<p>Ссылка на js-файл General</p>

			<h3>{head_snippet_include}</h3>
			<p>В настройках есть возможность <a href="<?=site_url('admin/options_header_footer')?>" target="_blank">вставки произвольного кода</a> на страницы сайта. Код, предназначенный для вставки в шапку будет вставлен вместо этой переменной.</p>

			<h3>{bg_head_snippet_include}</h3>
			<p>Настройки позволяют <a href="<?=site_url('admin/options_bgimage')?>" target="_blank">заменить фон сайта</a>. CSS-код делающий данную замену будет вставлен вместо этой переменной.</p>

			<h3>{bodyclass}</h3>
			<p>Некоторые страницы, для лучшего отображения, маркируются специальным классом тега &lt;body&gt;</p>

			<h3>{js_data}</h3>
			<p>Системная переменная для тега &lt;body&gt; - несет в себе динамические данные для JavaScript-библиотек.</p>

			<h3>{topbanner}</h3>
			<p>Шапка сайта. При наличии загруженной картинки &mdash; генерирует картинку, при отсутствии &mdash; генерирует стандартное изображение с названием и описанием сайта.</p>

			<h3>{homepage_selected_class}</h3>
			<p>CSS-класс обозначающий что ссылка на домашнюю страницу является активной.</p>

			<h3>{homepage_url}</h3>
			<p>Ссылка на домашнюю страницу.</p>

			<h3>{cart_selected_class}</h3>
			<p>CSS-класс обозначающий что ссылка на домашнюю страницу является активной.</p>

			<h3>{cart_url}</h3>
			<p>Ссылка на корзину</p>

			<h3>{topmenu}</h3>
			<p>Массив содержащий страницы помеченные для отображения в меню. Каждый элемент содержит следующие под-элементы:</p>

				<h5>{title}</h5>
				<p>Заголовок страницы</p>
				
				<h5>{title_attr}</h5>
				<p>Заголовок страницы предназначенный для вставки в аттрибут тега <i>Пример: &lt;a title=""&gt;</i></p>
				
				<h5>{selected_class}</h5>
				<p>CSS-класс обозначающий что ссылка на данную страницу является активной.</p>
				
				<h5>{url}</h5>
				<p>Ссылка на странцу</p>

			<h3>{search_form_action_default}</h3>
			<p>Аттрибут action по-умолчанию для формы поиска.</p>

			<h3>{search_mode}</h3>
			<p>Текущий режим поиска</p>

			<h3>{search_string}</h3>
			<p>Текщий поисковый запрос</p>

			<h3>{search_form_action_number}</h3>
			<p>Аттрибут action для формы поиска (при поиске по номеру).</p>

			<h3>{search_form_action_brand}</h3>
			<p>Аттрибут action для формы поиска (при поиске по бренду)</p>

			<h3>{auth_link_selected_class}</h3>
			<p>CSS-класс обозначающий что ссылка на страницу авторизации/личного кабинета является активной.</p>

			<h3>{auth_link_url}</h3>
			<p>Ссылка на страницу авторизации/личного кабинета.</p>

			<h3>{auth_link_title}</h3>
			<p>Заголовок ссылки авторизации/личного кабинета. Если пользователь авторизован, то переменная генерирует e-mail.</p>

			<h3>{cart_items}</h3>
			<p>Массив, содержащий элементы добавленные в коризну. Каждый элемент содержит следующие под-элементы:</p>

				<h5>{art_number}</h5>
				<p>Артикульный номер</p>

				<h5>{sup_brand}</h5>
				<p>Наименование бренда</p>

				<h5>{description}</h5>
				<p>Описание в прайс-листе</p>
				
				<h5>{price_formatted}</h5>
				<p>Цена в формате 100.00 руб. <i>(Валюта зависит от настроек сайта)</i></p>

				<h5>{qty}</h5>
				<p>Количество в корзине</p>

				<h5>{subtotal_formatted}</h5>
				<p>Сумма в формате 100.00 руб. (сумма = цена &times; количество). <i>(Валюта зависит от настроек сайта)</i></p>

			<h3>{cart_url}</h3>
			<p>Ссылка на корзину и страницу оформления заказа</p>

			<h3>{catalogues}</h3>
			<p>Массив, содержащий перечень доступных групп каталогов. Стандартно, включена одна группа под названием Общие Каталоги. Каждый элемент содержит следующие под-элементы:</p>

				<h5>{selected_class}</h5>
				<p>CSS-класс обозначающий что ссылка на страницу с данной группой является активной.</p>

				<h5>{url}</h5>
				<p>Ссылка на страницу с группой каталогов.</p>

				<h5>{title}</h5>
				<p>Название группы.</p>

				<h5>{title_attr}</h5>
				<p>Название группы предназначенное для вставки в аттрибут тега <i>Пример: &lt;a title=""&gt;</i>.</p>

			<h3>{custom_items}</h3>
			<p>Массив содержащий перечень категорий <a href="<?=site_url('admin/posts/item')?>" target="_blank">собственных товаров</a>.</p>

				<h3>{selected_class}</h3>
				<p>CSS-класс обозначающий что ссылка на данную категорию является активной.</p>

				<h3>{url}</h3>
				<p>Ссылка на категорию.</p>

				<h3>{title}</h3>
				<p>Название категории.</p>

				<h3>{title_attr}</h3>
				<p>Название категории предназначенное для вставки в аттрибут тега <i>Пример: &lt;a title=""&gt;</i>.</p>

			<h3>{auth_block_title}</h3>
			<p>Заголовок блока авторизации. Генерирует e-mail авторизованного пользователя, либо предложение об авторизации.</p>

			<h3>{auth_block_links}</h3>
			<p>Массив содержащий ссылки блока авторизации. Если авторизация не выполнена, &mdash; то генерируется ссылка на страницу авторизации. Если выполнена &mdash;
			то генерируются ссылки на страницу с перечнем заказов и на окончание сессии (Выход).
			Каждый элемент содержит следующие под-элементы:</p>

				<h3>{selected_class}</h3>
				<p>CSS-класс обозначающий что ссылка на данную страницу является активной.</p>

				<h3>{url}</h3>
				<p>Ссылка на страницу</p>

				<h3>{title}</h3>
				<p>Название.</p>

				<h3>{icon}</h3>
				<p>Иконка.</p>

				<h3>{title_attr}</h3>
				<p>Название предназначенное для вставки в аттрибут тега <i>Пример: &lt;a title=""&gt;</i>.</p>

			<h3>{admin_auth_block}</h3>
			<p>Блок авторизации администратора магазина (показывается если не запрещен в <a href="<?=site_url('admin/options_common')?>" target="_blank">настройках сайта</a>).</p>

			<h3>{sidebar_include_snippet}</h3>
			<p>В настройках есть возможность <a href="<?=site_url('admin/options_header_footer')?>" target="_blank">вставки произвольного кода</a> на страницы сайта. Код, предназначенный для вставки в боковую колонку будет вставлен вместо этой переменной.</p>

			<h3>{content}</h3>
			<p>Содержательная часть. Генерируется в зависимости от просматриваемой страницы/раздела сайта.</p>

			<h3>{footnote}</h3>
			<p>Информационный блок в нижней части сайта.</p>

			<h3>{foot_snippet_include}</h3>
			<p>В настройках есть возможность <a href="<?=site_url('admin/options_header_footer')?>" target="_blank">вставки произвольного кода</a> на страницы сайта. Код, предназначенный для вставки в подвал сайта будет вставлен вместо этой переменной.</p>

			<h3>{jb_domain_devbrand}</h3>
			<p>Значок разработчика.</p>
		
		</div>
	</div>
</div>

<script>
	var myCodeMirror = CodeMirror.fromTextArea(template, {mode: "htmlmixed", theme: "solarized", lineNumbers:true, tabSize:2  });
</script>