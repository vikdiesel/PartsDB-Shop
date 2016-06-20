<div class="<? if (isset($class)) echo $class ?>">
	<div class="page-header">
		<h1><?=$title ?></h1>
	</div>
	
	<ul class="breadcrumb">
	
		<li><a href="<?=base_url() ?>">Главная</a> <span class="divider">/</span></li>
		
		<? if (count($breadcrumbs) > 0): ?>
		
			<li><a href="<?=site_url('terms/' . $term_typedata->id) ?>"><?=$term_typedata->title ?></a>
		
		<? else: ?>
		
			<li class="active"><?=$term_typedata->title ?>
			
		<? endif ?>
		
		<? foreach ($breadcrumbs as $crumb): ?>
			
			<span class="divider">/</span></li>
			
			<li><a href="<?=site_url('terms/' . $term_typedata->id . '/' . $crumb->id) ?>"><?=$crumb->title ?></a>
		
		<? endforeach?>
		
		<span class="divider">/</span> </li>
		<li class="active"><?=$title ?></li>
	</ul>
	
	<?=$text ?>
	
</div>
<div class="clearfix"></div>