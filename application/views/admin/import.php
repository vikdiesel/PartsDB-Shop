
<div class="tabbable <? if (!empty($is_embedded)): ?>tabs-left<? endif ?>">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#upload_routine" data-toggle="tab"><i class="icon-download-alt"></i> Загрузка файла</a></li>
		<li class="<? if ($is_show_guide): ?>help-guide destroyable<? endif ?>" data-title="В первый раз?" data-content="Прочитайте нашу пошаговую инструкцию по подготовке файла."><a href="#how-to" data-toggle="tab"><i class="icon-info-sign"></i> Инструкция</a></li>
	</ul>
	
	<div class="tab-content">
		<div class="tab-pane active" id="upload_routine">

			<div class="errors"><?=$errors?></div>

			<div id="messagePreUpload" class="alert alert-info visibleUtilityMessage utilityMessage">
				<strong>Выберите файл данных.</strong> После выбора файла, загрузка и обработка начнутся автоматически.<br>
				Максимальный размер загружаемого файла - <strong><?=$upload_max?> Mb</strong>. Формат файла - XML.
				(Файл большего размера? <abbr title="Вкладка с инструкцией чуть выше">Поможет инструкция</a>)
			</div>
			<div id="messageUploadStatus" class="alert alert-info hiddenUtilityMessage"><strong>Идет загрузка файла&hellip;</strong> Файлы большого объема могут загружаться до 20 минут.</div>
			<div id="messageUploadError" class="alert alert-error hiddenUtilityMessage"></div>
			<div id="ImportSuccessMessage" class="alert alert-success alert-block hiddenUtilityMessage">	
				<p>
					<? if (!empty($backlink)): ?><a href="<?=$backlink ?>" class="btn btn-mini">Готово</a><? endif ?>
					<a href="#report" class="btn btn-mini"><i class="icon-list"></i> Отчет</a>
				</p>
			</div>

			<?php echo form_open_multipart("admin/ajax_upload/$type/$id", array('id'=>'file_import_form', 'class'=>'upload_form form-inline well', 'target'=>'uploadTarget'));?>
				
					<input class="-help-guide" data-title="Вставлять файл сюда" data-content="Выберите файл с вашего компьютера." data-placement="bottom" type="file" name="userfile" id="userfile">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-primary">Загрузка</button>
				
			</form>

			<div id="goTo" class="hiddenUtilityMessage">
				<? if (!empty($backlink)): ?>
					<p><a href="<?=$backlink ?>" class="btn">Готово</a></p>
				<? endif ?>
			</div>
			
		</div>
		
		<div class="tab-pane" id="how-to">
			<?=$text_import_howto ?>
		</div>
	</div>
</div>

<iframe src="about:blank" id="uploadTarget" name="uploadTarget" class="hiddenUtilityMessage"></iframe>