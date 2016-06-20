

	<div class="page-header">
		<h1><?=$title?> <small></small></h1>
	</div>
	
	<ul class="nav nav-tabs">
		<li class="active">
			<a href="#in_stock" data-toggle="tab"><i class="icon-align-justify"></i> Наличие</a>
		</li>
		<li><a href="#similar" data-toggle="tab">Перечень искомых артикулов</a></li>
	</ul>
	
	<div class="tab-content">
		
		<div class="tab-pane active" id="in_stock">
			
			<?=$in_stock?>
		
		</div>		
		
		<div class="tab-pane" id="similar">

			<?=$not_in_stock?>
			
		</div>
		
	</div>
	
	