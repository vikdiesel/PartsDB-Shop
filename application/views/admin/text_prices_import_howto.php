<div class="alert alert-info alert-block">
<h4>Пример файла с наличием и ценами</h4>
<p>Вы можете скачать его и загрузить к себе:&nbsp;<a class="btn btn-small" href="<?=site_url('welcome/sample_xml')?>">Скачать пример файла</a></p>
</div>


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
<p>Подготовьте в Microsoft Excel файл по следующему образцу (файл данного образца может также быть выгружен из программы 1С, притом сразу в формат XML — <a title="Как подготовить файл с наличием и ценами с помощью 1С?" href="#1c-accounting">информация в самом низу</a>):</p>
<ul>
<li><strong>Колонка 1. Артикульный номер запчасти.</strong> Можно использовать любые символы.</li>
<li><strong>Колонка 2. Наименование производителя запчасти.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 3. Наименование запчасти (например: Фильтр масляный).</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 4. Количество единиц на складе.</strong> Только целые числа, без пробелов, запятых, точек и т.д.</li>
<li><strong>Колонка 5. Цена за единицу.</strong> Можно использовать только цифры и запятую для разделения десятичных значений. Пробелы, точки и иные символы недопустимы.</li>
</ul>

<div class="alert alert-info"><i class="icon-info-sign"></i> <b>Порядок может быть иным</b>. Достаточно задать соответствующие номера колонок в Параметрах склада.</div>
<p>Первая строчка файла содержит наименования колонок и игнорируется при импорте.<em> Пример таблицы показан внизу данной странцы.</em></p>


<a name="step2"></a>
<p>&nbsp;</p>
<h3>Шаг 2. Сохранение подготовленного файла в фомате XML.</h3>
<p>По окончании подготовки данных в программе Microsoft Excel, выберите пункт меню <strong>Файл -&gt; Сохранить как</strong>. В открывшемся диалоговом окне выберите место для сохранения файла, укажите имя, а в списке <strong>Тип файла</strong> (находится под полем Имя файла) выберите тип <strong>Таблица XML 2003</strong> (<em>в Microsoft Excel 2003 данный тип назвывается просто Таблица XML</em>), далее нажмите <strong>Сохранить</strong>.</p>
<p><em>Скриншот окна сохранения файла</em></p>
<p class="-thumbnail"><img title="Окно сохранения файла Microsoft Excel" src="/e/images/xml-spreadsheet-2003.png" alt="" width="447" height="291"></p>


<a name="step3"></a>
<p>&nbsp;</p>
<h3>Шаг 3. Загрузка файла.</h3>
<p>Выберите пункт меню Загрузка прайса (в колонке слева). Вы увидите список ваших складов (по умолчанию в данном списке присутствует склад с названием «Наш склад» и пара складов партнеров, которые можно удалить). Щелкните по синей кнопке «Импорт» расположенной напротив нужного вам склада. Далее, на открывшейся странице, выберите сохраненный ранее файл с наличием и ценами в формате XML.</p>
<p>Загрузка файла начнется автоматически. Дождитесь сообщения об успешном завершении процесса импорта.</p>


<a name="faq_large_files"></a>
<p>&nbsp;</p>
<hr>
<h3>Вопрос. Если мой файл больше разрешенного лимита?</h3>
<p>Загрузка слишком больших файлов часто прерывается из-за разрыва соединения, поэтому мы поставили лимит. Большинство прайсов великолепно в этот
лимит укладываются. Если размер файла не превышает установленный лимит, то все будет хорошо.</p>
<p>А если превышает? Нужно действовать так: 
<ul>
	<li>Добавьте склад с нужными параметрами (название &mdash; на ваше усмотрение)</li>
	<li>До загрузки, откройте большой файл в Microsoft Excel.</li>
	<li>Скопируйте первые 50 тыс строк и сохраните их в отдельный файл.</li>
	<li>Файл с первыми 50 тыс строками загрузите согласно инструкции выше (Шаг 1, Шаг 2, Шаг 3)</li>
	<li>&hellip;повторите процедуру для следующих 50 тыс строк (точно так же, создав <i>еще один склад</i> со всеми требуемыми параметрами)</li>
</ul>
<p>В результате, вместо одного склада, у Вас будет, скажем, пять с одинаковыми параметрами (такимим как <i>корректировка цены</i> и <i>срок поставки</i>) и содержащими разные части одного большого файла.</p>


<a name="sample_table"></a>
<p>&nbsp;</p>
<hr>
<h3>Пример таблицы с наличием и ценами.</h3>
<table class="table table-striped" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>Номер</th>
<th>Производитель</th>
<th>Наименование</th>
<th>Количество</th>
<th>Цена</th>
</tr>
</thead>
<tbody>
<tr>
<td>NSS009</td>
<td>Febest</td>
<td>Опора пер. стойки Ниссан A33</td>
<td>2</td>
<td>699,2</td>
</tr>
<tr>
<td>94951</td>
<td>Dayco</td>
<td>146SP280HT Ремень ГРМ</td>
<td>1</td>
<td>876</td>
</tr>
<tr>
<td>94470</td>
<td>Dayco</td>
<td>151STP8M190 Ремень ГРМ 90-151-19 VAG/Nissan</td>
<td>1</td>
<td>489,6</td>
</tr>
<tr>
<td>94407</td>
<td>Dayco</td>
<td>152STP8M254 Ремень ГРМ 90-152-25 Mazda</td>
<td>1</td>
<td>662,4</td>
</tr>
<tr>
<td>AU06032LLX</td>
<td>NTN</td>
<td>42300S2L008 Подш. ступ. пер. Honda HR-V</td>
<td>1</td>
<td>1152</td>
</tr>
<tr>
<td>G92005</td>
<td>Japan Cars</td>
<td>GMB Крестовина рул. вала. 39×16.5 Toyota</td>
<td>3</td>
<td>320</td>
</tr>
<tr>
<td>619025906</td>
<td>LUK</td>
<td>KT Сцепление (кт) Ford</td>
<td>1</td>
<td>3100</td>
</tr>
<tr>
<td>618000106</td>
<td>LUK</td>
<td>KT Сцепление (кт) Golf2 1.3</td>
<td>1</td>
<td>3689,6</td>
</tr>
<tr>
<td>J43004C</td>
<td>Japan Cars</td>
<td>RBI Подушка рычага пер. ниж. пр. Mazda 323 1,3-1,8 8</td>
<td>1</td>
<td>150</td>
</tr>
<tr>
<td>J43000В</td>
<td>Japan Cars</td>
<td>RBI Сайлентблок пер. рычага Мазда 323</td>
<td>2</td>
<td>150</td>
</tr>
<tr>
<td>4604010370</td>
<td>Mercedes</td>
<td>Болт</td>
<td>2</td>
<td>316,8</td>
</tr>
<tr>
<td>1404000170</td>
<td>Mercedes</td>
<td>Болт</td>
<td>2</td>
<td>316,8</td>
</tr>
<tr>
<td>55226-50Y00</td>
<td>Nissan</td>
<td>Болт</td>
<td>2</td>
<td>224</td>
</tr>
<tr>
<td>CFD452</td>
<td>Borg Warner</td>
<td>Вискомуфта MB Sprinter</td>
<td>2</td>
<td>3740</td>
</tr>
<tr>
<td>CFD702</td>
<td>Borg Warner</td>
<td>Вискомуфта VAG 2.3-2.5TDi</td>
<td>1</td>
<td>3141,6</td>
</tr>
<tr>
<td>CFD701</td>
<td>Borg Warner</td>
<td>Вискомуфта VAG 2.4-2.8</td>
<td>1</td>
<td>2965,6</td>
</tr>
<tr>
<td>CFD703</td>
<td>Borg Warner</td>
<td>Вискомуфта VAG 3,7-4,2</td>
<td>1</td>
<td>3476</td>
</tr>
<tr>
<td>CFD704</td>
<td>Borg Warner</td>
<td>Вискомуфта VAG Diesel</td>
<td>1</td>
<td>2772</td>
</tr>
<tr>
<td>2100012131</td>
<td>Sachs</td>
<td>Вискомуфта вент. BMW (4отв)</td>
<td>1</td>
<td>3071,2</td>
</tr>
<tr>
<td>17849</td>
<td>Febi</td>
<td>Вискомуфта вент. радиатора M112/113</td>
<td>1</td>
<td>3599,2</td>
</tr>
<tr>
<td>881112000422</td>
<td>JP</td>
<td>Вискомуфта вент. радиатора W124/202/210</td>
<td>1</td>
<td>1596,8</td>
</tr>
<tr>
<td>MSBEA3F</td>
<td>Febest</td>
<td>Втулка пер. стаб. GALANT EA3A/EA8A USA 1996-2003</td>
<td>2</td>
<td>89,6</td>
</tr>
<tr>
<td>HSBREF</td>
<td>Febest</td>
<td>Втулка пер. стабилизатора HONDA CR-V RE3/RE4 2007-</td>
<td>2</td>
<td>105,6</td>
</tr>
<tr>
<td>SSBR2</td>
<td>Febest</td>
<td>Втулка пер. стабилизатора Subaru IMPREZA G11 2000-2007</td>
<td>2</td>
<td>157,6</td>
</tr>
<tr>
<td>TSB798</td>
<td>Febest</td>
<td>Втулка пер.стабилизатора RAV4 SXA1 1993-2000</td>
<td>2</td>
<td>74,4</td>
</tr>
<tr>
<td>TSB796</td>
<td>Febest</td>
<td>Втулка стаб.пер. AE100/EE100 Corolla/Levin</td>
<td>2</td>
<td>75,2</td>
</tr>
<tr>
<td>0K2NA34156</td>
<td>KIA</td>
<td>Втулка стабилизатора</td>
<td>1</td>
<td>88</td>
</tr>
<tr>
<td>030.790</td>
<td>Elring</td>
<td>Герметик-прокладка 80 грамм</td>
<td>10</td>
<td>202,4</td>
</tr>
<tr>
<td>92144600</td>
<td>Texstar</td>
<td>Диск торм. зад. Honda</td>
<td>2</td>
<td>1017,6</td>
</tr>
<tr>
<td>08.6897.14</td>
<td>Brembo</td>
<td>Диск торм. зад. Subaru</td>
<td>2</td>
<td>1035,2</td>
</tr>
<tr>
<td>92107400</td>
<td>Texstar</td>
<td>Диск торм. задн. BMW X5 00-&gt;</td>
<td>2</td>
<td>1713,6</td>
</tr>
<tr>
<td>BG3296</td>
<td>Delphi</td>
<td>Диск торм. пер. MB Vito</td>
<td>2</td>
<td>949,6</td>
</tr>
<tr>
<td>27-008-F</td>
<td>Boge</td>
<td>ЗА BMW E30</td>
<td>2</td>
<td>1500</td>
</tr>
<tr>
<td>27-168-0</td>
<td>Boge</td>
<td>ЗА BMW E30</td>
<td>2</td>
<td>1360</td>
</tr>
<tr>
<td>27-379-0</td>
<td>Boge</td>
<td>ЗА Ford Scorpio 85-92</td>
<td>2</td>
<td>1200</td>
</tr>
<tr>
<td>27-256-0</td>
<td>Boge</td>
<td>ЗА Ford Sierra</td>
<td>2</td>
<td>1400</td>
</tr>
<tr>
<td>27-311-0</td>
<td>Boge</td>
<td>ЗА VW Passat/Santana 83-88</td>
<td>2</td>
<td>1200</td>
</tr>
<tr>
<td>27-982-F</td>
<td>Boge</td>
<td>ЗА VW-Passat 88-97</td>
<td>2</td>
<td>1500</td>
</tr>
<tr>
<td>27-982-1</td>
<td>Boge</td>
<td>ЗА VW-Passat 88-97 масл.</td>
<td>2</td>
<td>1350</td>
</tr>
<tr>
<td>EX553101E300</td>
<td>Mando</td>
<td>ЗС New Verna/Accent 05- RR</td>
<td>2</td>
<td>1297,6</td>
</tr>
<tr>
<td>EX553002H000</td>
<td>Mando</td>
<td>ЗС HY Elantra HD 06- RR</td>
<td>2</td>
<td>1503,2</td>
</tr>
<tr>
<td>EX553512E202</td>
<td>Mando</td>
<td>ЗС Hyundai Tucson лев</td>
<td>1</td>
<td>1872,8</td>
</tr>
<tr>
<td>EX553612E202</td>
<td>Mando</td>
<td>ЗС Hyundai Tucson прав</td>
<td>1</td>
<td>1872,8</td>
</tr>
<tr>
<td>8125</td>
<td>A.B.S.</td>
<td>Колодки торм. MB100/Renault/Daewoo</td>
<td>1</td>
<td>1040,8</td>
</tr>
<tr>
<td>7105</td>
<td>LPR</td>
<td>Колодки торм. MB100/Renault/Daewoo</td>
<td>1</td>
<td>1028</td>
</tr>
<tr>
<td>362422J</td>
<td>Jurid</td>
<td>Колодки торм. бараб. MB Vito</td>
<td>1</td>
<td>992</td>
</tr>
<tr>
<td>50-W0-004</td>
<td>Ashika</td>
<td>Колодки торм. пер. Daewoo Matiz/Lanos</td>
<td>1</td>
<td>274,4</td>
</tr>
<tr>
<td>50-05-599</td>
<td>Ashika</td>
<td>Колодки торм. пер. Mitsubishi Pajero/Outlander</td>
<td>1</td>
<td>476</td>
</tr>
<tr>
<td>05P690</td>
<td>LPR</td>
<td>Колодки торм. пер. Opel Astra G/H/Zafira</td>
<td>1</td>
<td>955,2</td>
</tr>
<tr>
<td>GDB1500</td>
<td>TRW</td>
<td>Колодки торм. пер. Peugeot 206 2007=&gt;</td>
<td>1</td>
<td>1204,8</td>
</tr>
<tr>
<td>GDB1550</td>
<td>TRW</td>
<td>Колодки торм. пер. А3/Touran</td>
<td>1</td>
<td>1902,4</td>
</tr>
<tr>
<td>GDB1616</td>
<td>TRW</td>
<td>Колодки торм. пер. А3/Touran</td>
<td>2</td>
<td>1931,2</td>
</tr>
<tr>
<td>37270</td>
<td>A.B.S.</td>
<td>Колодки торм.пер.</td>
<td>1</td>
<td>1272,8</td>
</tr>
<tr>
<td>37274</td>
<td>A.B.S.</td>
<td>Колодки торм.пер. VW T4 96=&gt; с датчиком</td>
<td>1</td>
<td>1428</td>
</tr>
<tr>
<td>05P984</td>
<td>LPR</td>
<td>Колодки торм.пер. VW T4 99-03</td>
<td>1</td>
<td>1107,2</td>
</tr>
<tr>
<td>4604920682</td>
<td>Mercedes</td>
<td>Кольцо глушителя</td>
<td>1</td>
<td>224</td>
</tr>
<tr>
<td>2024920181</td>
<td>Mercedes</td>
<td>Кольцо глушителя MB</td>
<td>1</td>
<td>156,8</td>
</tr>
<tr>
<td>2024920381</td>
<td>Mercedes</td>
<td>Кольцо глушителя MB</td>
<td>1</td>
<td>156,8</td>
</tr>
<tr>
<td>19906036</td>
<td>Mercedes</td>
<td>Крепеж MB W210</td>
<td>4</td>
<td>86,4</td>
</tr>
<tr>
<td>49941845</td>
<td>Mercedes</td>
<td>Крепеж MB W210</td>
<td>4</td>
<td>48</td>
</tr>
<tr>
<td>4615010015</td>
<td>Mercedes</td>
<td>Крышка радиатора охл.</td>
<td>1</td>
<td>537,6</td>
</tr>
<tr>
<td>SE-6381L</td>
<td>555</td>
<td>НРТ Хонда Civic FD 05-</td>
<td>1</td>
<td>408,8</td>
</tr>
<tr>
<td>SE-6381R</td>
<td>555</td>
<td>НРТ Хонда Civic FD 05-</td>
<td>1</td>
<td>408,8</td>
</tr>
<tr>
<td>291953</td>
<td>Ocap</td>
<td>НРТ левый MB W163 ML</td>
<td>1</td>
<td>512</td>
</tr>
<tr>
<td>CET-119</td>
<td>CTR</td>
<td>НРТ прав. Toyota LC 100 98-02</td>
<td>1</td>
<td>675,2</td>
</tr>
</tbody>
</table>


<p>&nbsp;</p>

<a name="1c-accounting"></a>
<h4>[Справочно] Как подготовить файл с наличием и ценами с помощью 1С?</h4>

<p>Попросите вашего специалиста по программе 1С настроить возможность экспорта информации о наличии/ценах в XML файл по формату указанному ниже.</p>
<ul>
<li><strong>Колонка 1. Артикульный номер запчасти.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 2. Наименование производителя запчасти.</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 3. Наименование запчасти (например: Фильтр масляный).</strong>&nbsp;Можно использовать любые символы.</li>
<li><strong>Колонка 4. Количество единиц на складе.</strong>&nbsp;Только целые числа, без пробелов, запятых, точек и т.д.</li>
<li><strong>Колонка 5. Цена за единицу.</strong>&nbsp;Можно использовать только цифры и точку для разделения десятичных значений. Пробелы, запятые и иные символы недопустимы.</li>
</ul>
<p>&nbsp;</p>
<pre class="prettyprint linenums">&lt;?xml version="1.0"?&gt;
&lt;Table&gt;
&lt;Row&gt;
&lt;Cell&gt;Номер&lt;/Cell&gt;
&lt;Cell&gt;Производитель&lt;/Cell&gt;
&lt;Cell&gt;Наименование&lt;/Cell&gt;
&lt;Cell&gt;Количество&lt;/Cell&gt;
&lt;Cell&gt;Цена&lt;/Cell&gt;
&lt;/Row&gt;
&lt;Row&gt;
&lt;Cell&gt;4292552&lt;/Cell&gt;
&lt;Cell&gt;Lesjofors&lt;/Cell&gt;
&lt;Cell&gt;Пружина задн. Toyota Camry 86-91 HD&lt;/Cell&gt;
&lt;Cell&gt;2&lt;/Cell&gt;
&lt;Cell&gt;1647.2&lt;/Cell&gt;
&lt;/Row&gt;
&lt;Row&gt;
&lt;Cell&gt;4292529&lt;/Cell&gt;
&lt;Cell&gt;Lesjofors&lt;/Cell&gt;
&lt;Cell&gt;Пружина задн. Toyota Carina E 93&lt;/Cell&gt;
&lt;Cell&gt;2&lt;/Cell&gt;
&lt;Cell&gt;1684.3&lt;/Cell&gt;
&lt;/Table&gt;</pre>
<p>Далее, загрузка подготовленного файла производится по общей схеме (начиная с <a href="#step3">шага №3</a>)</p>