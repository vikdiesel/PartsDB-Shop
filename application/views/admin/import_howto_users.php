<h2>Содержание</h2>
<ul>
	<li><a href="#step1">Шаг 1. Подготовка файла.</a></li>
	<li><a href="#step2">Шаг 2. Сохранение подготовленного файла в фомате XML.</a></li>
	<li><a href="#step3">Шаг 3. Загрузка файла.</a></li>
	<li><a href="#faq_large_files">Если мой файл больше разрешенного лимита?</a></li>
	<li><a href="#sample_table">Пример таблицы с наличием и ценами</a></li>
</ul>
<p>&nbsp;</p>
<hr>

<a name="step1"></a>

<h3>Шаг 1. Подготовка файла.</h3>
<p>Подготовьте в Microsoft Excel файл по следующему образцу (файл данного образца может также быть выгружен из программы 1С, притом сразу в формат XML — <a title="Как подготовить файл с помощью 1С?" href="#1c-accounting">информация в самом низу</a>):</p>
<ul>
<li><strong>Колонка 1. E-mail клиента.</strong> Корректный e-mail.</li>
<li><strong>Колонка 2. Наименование или Имя клиента.</strong>&nbsp;Обязательное поле. Можно использовать любые символы.</li>
<li><strong>Колонка 3. Телефон клиента.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 4. Адрес клиента.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 5. ИНН/КПП клиента.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 6. ОГРН/ОГРНИП клиента.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 7. Расчетный счет клиента.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 8. БИК банка клиента.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 9. Пароль клиента.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 10. Скидка клиента.</strong>&nbsp;Скидка в процентах. Только цифры.</li>
</ul>

<p>Обязательной является только <b>Колонка 2. Наименование или Имя клиента</b> &mdash; остальные колонки могут быть пустыми. Первая строчка файла содержит наименования колонок и игнорируется при импорте.<em> Пример таблицы показан внизу данной странцы.</em></p>

<a name="step2"></a>
<p>&nbsp;</p>
<h3>Шаг 2. Сохранение подготовленного файла в фомате XML.</h3>
<p>По окончании подготовки данных в программе Microsoft Excel, выберите пункт меню <strong>Файл -&gt; Сохранить как</strong>. В открывшемся диалоговом окне выберите место для сохранения файла, укажите имя, а в списке <strong>Тип файла</strong> (находится под полем Имя файла) выберите тип <strong>Таблица XML 2003</strong> (<em>в Microsoft Excel 2003 данный тип назвывается просто Таблица XML</em>), далее нажмите <strong>Сохранить</strong>.</p>
<p><em>Скриншот окна сохранения файла</em></p>
<p class="-thumbnail"><img title="Окно сохранения файла Microsoft Excel" src="/e/images/xml-spreadsheet-2003.png" alt="" width="447" height="291"></p>


<a name="step3"></a>
<p>&nbsp;</p>
<h3>Шаг 3. Загрузка файла.</h3>
<p>Выберите пункт меню Загрузка файла (в колонке слева). Далее, на открывшейся странице, выберите сохраненный ранее файл в формате XML.</p>
<p>Загрузка файла начнется автоматически. Дождитесь сообщения об успешном завершении процесса импорта.</p>


<a name="faq_large_files"></a>
<p>&nbsp;</p>
<hr>
<h3>Вопрос. Если мой файл больше разрешенного лимита?</h3>
<p>Загрузка слишком больших файлов часто прерывается из-за разрыва соединения, поэтому мы поставили лимит. Большинство файлов великолепно в этот
лимит укладываются. Если размер файла не превышает установленный лимит, то все будет хорошо.</p>
<p>А если превышает, то необходимо разделить один большой файл на более маленькие и загрузить их поэтапно.</p>

<a name="sample_table"></a>
<p>&nbsp;</p>
<hr>
<h3>Пример таблицы с клиентами.</h3>
<div style="overflow-x:scroll; max-width:100%;">
<table class="table table-striped table-bordered table-compact" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>E-mail</th>
<th>Имя/Наименование</th>
<th>Телефон</th>
<th>Адрес</th>
<th>ИНН/КПП</th>
<th>ОГРН/ОГРНИП</th>
<th>Расчетный счет</th>
<th>БИК</th>
<th>Пароль</th>
<th>Скидка</th>
</tr>
</thead>
<tbody>
<tr>
<td>hampstead@example.com</td>
<td>ООО &laquo;Хемпстед Рус&raquo;</td>
<td>(495) 888-00-00</td>
<td>123000, г. Москва, ул. Тверская, 12-22</td>
<td>7700000000/77001001</td>
<td>31107700000000</td>
<td>40802810200000000000</td>
<td>040000000</td>
<td>123456</td>
<td>1</td>
</tr>
<tr>
<td>oleg@example.com</td>
<td>Иванов Олег</td>
<td>(495) 888-11-11</td>
<td>123000, г. Москва, ул. Моховая, 8-78</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td>123456</td>
<td>0</td>
</tr>
<tr>
<td></td>
<td>Бажов Петр</td>
<td>(495) 888-11-11</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td>0</td>
</tr>
</tbody>
</table>
</div>


<p>&nbsp;</p>

<a name="1c-accounting"></a>
<h4>[Справочно] Как подготовить файл с наличием и ценами с помощью 1С?</h4>

<p>Попросите вашего специалиста по программе 1С настроить возможность экспорта информации о наличии/ценах в XML файл по формату указанному ниже.</p>
<ul>
<li><strong>Колонка 1. E-mail клиента.</strong> Корректный e-mail.</li>
<li><strong>Колонка 2. Наименование или Имя клиента.</strong>&nbsp;Обязательное поле. Можно использовать любые символы.</li>
<li><strong>Колонка 3. Телефон клиента.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 4. Адрес клиента.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 5. ИНН/КПП клиента.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 6. ОГРН/ОГРНИП клиента.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 7. Расчетный счет клиента.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 8. БИК банка клиента.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 9. Пароль клиента.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 10. Скидка клиента.</strong>&nbsp;Скидка в процентах. Только цифры.</li>
</ul>
<p>&nbsp;</p>
<pre class="prettyprint linenums">&lt;?xml version="1.0"?&gt;
&lt;Table&gt;
&lt;Row&gt;
&lt;Cell&gt;E-mail&lt;/Cell&gt;
&lt;Cell&gt;Наименование&lt;/Cell&gt;
&lt;Cell&gt;Телефон&lt;/Cell&gt;
&lt;Cell&gt;Адрес&lt;/Cell&gt;
&lt;Cell&gt;ИНН/КПП&lt;/Cell&gt;
&lt;Cell&gt;ОГРН/ОГРНИП&lt;/Cell&gt;
&lt;Cell&gt;Расчетный счет&lt;/Cell&gt;
&lt;Cell&gt;БИК&lt;/Cell&gt;
&lt;Cell&gt;Пароль&lt;/Cell&gt;
&lt;Cell&gt;Скидка&lt;/Cell&gt;
&lt;/Row&gt;
&lt;Row&gt;
&lt;Cell&gt;hampstead@example.com&lt;/Cell&gt;
&lt;Cell&gt;ООО &laquo;Хемпстед Рус&raquo;&lt;/Cell&gt;
&lt;Cell&gt;(495) 888-00-00&lt;/Cell&gt;
&lt;Cell&gt;123000, г. Москва, ул. Тверская, 12-22&lt;/Cell&gt;
&lt;Cell&gt;7700000000/77001001&lt;/Cell&gt;
&lt;Cell&gt;31107700000000&lt;/Cell&gt;
&lt;Cell&gt;40802810200000000000&lt;/Cell&gt;
&lt;Cell&gt;040000000&lt;/Cell&gt;
&lt;Cell&gt;123456&lt;/Cell&gt;
&lt;Cell&gt;1&lt;/Cell&gt;
&lt;/Row&gt;
&lt;/Table&gt;</pre>
<p>Далее, загрузка подготовленного файла производится по общей схеме (начиная с <a href="#step3">шага №3</a>)</p>