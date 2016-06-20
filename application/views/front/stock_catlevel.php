<div class="page-header">
	<h1><?=$title ?></h1>
</div>

<?=$breadcrumbs ?>

<p>&nbsp;</p>

<ul class="nav nav-tabs">
	<li class="active">
		<a href="#in_stock" data-toggle="tab"><i class="icon-align-justify"></i> Наличие</a>
	</li>
	<li><a href="#similar" data-toggle="tab">Перечень искомых артикулов</a></li>
</ul>

<div class="tab-content">
	
	<div class="tab-pane active" id="in_stock">
		
		<? if ($num_arts_in_stock > 0): ?>
			
			<div class="alert alert-success"><strong>Показаны некоторые аналоги</strong>. Для полного перечня щелкните &laquo;Подробнее&raquo; напротив нужной позиции.</div>
			
		<? endif ?>
		
		<?=$in_stock?>
	
	</div>		
	
	<div class="tab-pane" id="similar">

		<?=$not_in_stock?>
		
	</div>
	
</div>
