	<div class="page-header">
		<h1><?=$title?></h1>
	</div>
	
	<?=$in_stock?>
	
	<hr>
	
	<ul id="article-data-tabs" class="nav nav-tabs">
		<li class="active"><a href="#article-info" data-toggle="tab">Информация об изделии</a></li>
		<li><a href="#compatibility" data-toggle="tab">Применимость</a></li>
		<li><a href="#similar" data-toggle="tab">Перечень искомых артикулов</a></li>
	</ul>
	
	<div id="article-data-tabs-content" class="tab-content">
		
		<div class="tab-pane active" id="article-info">
		
			<?=$artinfo ?>
		
		</div>
		
		<div class="tab-pane " id="similar">

			<?=$not_in_stock?>
			
		</div>
		
		<div class="tab-pane" id="compatibility">
			<h4>Применимость к автомобилям</h4>
			<p>Таблица данных по применимости содержит данные об автомобилях для которых пригодна данная запасная часть.</p>
			<p><a href="<?=site_url('front/auto_compatibility_ajax/'.$art_id)?>" class="btn auto_compatibility_trigger"><i class="icon-random"></i> Просмотр данных</a></p>
		</div>
		
	</div>
	
	