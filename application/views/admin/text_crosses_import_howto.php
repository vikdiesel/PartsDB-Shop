<div class="alert alert-info alert-block">
<h4>Пример файла с кроссами</h4>
<p>Вы можете скачать его и загрузить к себе:&nbsp;<a class="btn btn-small" href="<?=site_url('welcome/sample_crosses_xml')?>">Скачать пример файла</a></p>
</div>

<a name="step1"></a>

<h3>Шаг 1. Подготовка файла.</h3>
<p>Подготовьте в Microsoft Excel файл по одному из представленных образцов. В качестве разделителя аналогов можно использовать: <i>точку с запятой без пробела, точку с запятой + пробел, запятую без пробела, запятую + пробел</i>.
	Остальные разделители не действуют.</p>

<table class="table table-striped table-bordered">
	<thead>
	<tr>
		<th>Перечень артикулов</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td>RZ-XXXX1; RZ-XXXX2; RZ-XXXX3; RZ-XXXX4; RZ-XXXX5; RZ-XXXX6;</td>
	</tr>
	<tr>
		<td>ZD-XXXX1; ZD-XXXX2; ZD-XXXX3; ZD-XXXX4; ZD-XXXX5; </td>
	</tr>
	</tbody>
</table>

<p>&hellip;или&hellip;</p>

<table class="table table-striped table-bordered">
	<thead>
	<tr>
		<th>Артикул_1</th>
		<th>Артикул_2</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td>J43004C</td>
		<td>RZ-XXXXX</td>
	</tr>
	<tr>
		<td>55226-50Y00</td>
		<td>ZD-XXXXX</td>
	</tr>
	</tbody>
</table>

<p>&hellip;или&hellip;</p>

<table class="table table-striped table-bordered">
	<thead>
	<tr>
		<th>Артикул_1</th>
		<th>Перечень артикулов</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td>J43004C</td>
		<td>RZ-XXXX1; RZ-XXXX2; RZ-XXXX3; RZ-XXXX4; RZ-XXXX5; RZ-XXXX6;</td>
	</tr>
	<tr>
		<td>55226-50Y00</td>
		<td>ZD-XXXX1; ZD-XXXX2; ZD-XXXX3; ZD-XXXX4; ZD-XXXX5; </td>
	</tr>
	</tbody>
</table>


<div class="alert alert-info">
	В качестве разделителя аналогов можно использовать: <strong>точку с запятой без пробела, точку с запятой + пробел, запятую без пробела, запятую + пробел</strong>.
	Остальные разделители не действуют.
</div>
<div class="alert alert-info">
	Первая строчка файла содержит наименования колонок и игнорируется при импорте.
</div>

<a name="step2"></a>

<h3>Шаг 2. Сохранение подготовленного файла в фомате XML</h3>
<p>По окончании подготовки данных в программе Microsoft Excel, выберите пункт меню <strong>Файл -&gt; Сохранить как</strong>. В открывшемся диалоговом окне выберите место для сохранения файла, укажите имя, а в списке <strong>Тип файла</strong> (находится под полем Имя файла) выберите тип <strong>Таблица XML 2003</strong> (<em>в Microsoft Excel 2003 данный тип назвывается просто Таблица XML</em>), далее нажмите <strong>Сохранить</strong>.</p>
<p><em>Скриншот окна сохранения файла</em></p>
<p class="-thumbnail"><img title="Окно сохранения файла Microsoft Excel" src="/e/images/xml-spreadsheet-2003.png" alt="" width="447" height="291"></p>
<p>&nbsp;</p>

<a name="step3"></a>

<h3>Шаг 3. Загрузка файла.</h3>
<p>Щелкните по пункту меню Кроссы (в колонке слева). 
Здесь вы можете выбрать или создать новую группу (задача групп сводится к исключительно внутреннему использованию и призвана помочь вам разделить кроссы разных поставщиков). 
Щелкните по синей кнопке «Импорт» расположенной напротив нужной вам группы. Далее, на открывшейся странице, выберите сохраненный ранее файл в формате XML.</p>
<p>Загрузка файла начнется автоматически. Дождитесь сообщения об успешном завершении процесса импорта.</p>

