<div class="page-header">
	<h1><?=$title ?></h1>
</div>

<?=$breadcrumbs ?>

<div class="row-fluid">
	
	<div class="span8 col-sm-8">
	
		<ul>
		<? foreach ($tree as $cat): ?>
			<li><a href="<?=site_url( "find/$brand_id/$model_id/$type_id/{$cat->id}" )?>" class="cat_tree_link" title="<?=$cat->name ?>"><?=$cat->name ?></a></li>
		<? endforeach ?>
		</ul>
		
	</div>
	<div class="span4 col-sm-4">
		
		<div class="hide-xs">
			<div id="jbsmBox" class="alert alert-block" data-jbsm_tid="<?=$type_id?>">
				<div id="jbsmSaveCar" class="hiddenUtilityMessage">
					<h4><?=lang('jb_tree_savecar_ttl') ?></h4>
					<p><?=lang('jb_tree_savecar_txt') ?></p>
					<a href='#' data-jbsm_tid="<?=$type_id?>" data-jbsm_lnk="<?=$jbSM_lnk?>" data-jbsm_ttl="<?=$jbSM_ttl?>" class="btn btn-small btn-sm btn-primary jbsm-trigger jbsm-save-model"><i class="icon-briefcase icon-white fa fa-briefcase"></i> <?=lang('jb_tree_savecar_btn') ?></a>
				</div>
				<div id="jbsmDeleteCar" class="hiddenUtilityMessage">
					<h4><?=lang('jb_tree_unsavecar_ttl') ?></h4>
					<p><?=lang('jb_tree_unsavecar_txt') ?></p>
					<a href="#" data-jbsm_tid="<?=$type_id?>" class="btn btn-default btn-sm btn-small jbsm-trigger jbsm-delete-model"><i class="icon-trash fa fa-trash"></i> <?=lang('jb_tree_unsavecar_btn') ?></a>
				</div>
			</div>
		</div>
	</div>
</div>