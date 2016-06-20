<div class="page-header">
	<h1><?=$thistitle?></h1>
</div>

<ul class="breadcrumb">
	
	<li><a href="<?=base_url() ?>">Главная</a> <span class="divider">/</span></li>
	
	<? if (count($breadcrumbs) > 0): ?>
	
		<li><a href="<?=site_url('terms/' . $typedata->id) ?>"><?=$typedata->title ?></a>
	
	<? else: ?>
	
		<li class="active"><?=$typedata->title ?>
		
	<? endif ?>
	
	<? foreach ($breadcrumbs as $crumb): ?>
		
		<span class="divider">/</span></li>
		
		<? if(!$crumb->is_current): ?>
			<li><a href="<?=site_url('terms/' . $typedata->id . '/' . $crumb->id) ?>"><?=$crumb->title ?></a>
		<? else: ?>
			<li class="active"><?=$crumb->title ?>
		<? endif?>
	
	<? endforeach?>
	
	</li>
</ul>


<? if ($num_terms == 0 and $num_posts == 0): ?>
	
	<div class="alert alert-info"><i class="icon-info-sign"></i> <strong>Пусто... увы...</strong> Можно начать <a href="<?=site_url('terms/' . $typedata->id)?>">сначала</a>.</div>

<? endif ?>
	
<? if ($num_terms > 0): ?>
	
	<ul class="nav nav-tabs nav-stacked">
			
		<? foreach ($terms as $term): ?>
			
			<li><a href="<?=site_url('terms/' . $typedata->id . '/' . $term->id)?>"><?=$term->title?></a></li>
			
		<? endforeach ?>
			
	</ul>

<? endif ?>


<? if ($num_posts > 0): ?>
	
	<table class="table table-striped">
		
		<tbody>
		
		<? foreach ($posts as $post): ?>
		
		<tr>
			<td><i class="icon-list-alt"></i> <?=$post->title?></td>
			<td class="table-buttons"><a href="<?=site_url('post/' . $typedata->posttypeid . '/' . $post->permalink)?>" class="btn btn-small btn-primary"><i class="icon-check icon-white"></i> Цены</a></td>
		</tr>
		
		<? endforeach ?>
		
		</tbody>
		
	</table>

<? endif ?>