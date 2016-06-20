	<? if ($mode == 'number'): ?>
		
		<div class="page-header">
			<h1><?=sprintf(lang('jb_search_ttl_n'), $clear_search_string) ?></h1>
		</div>
	
	<? else: ?>
	
		<div class="page-header">
			<h1><?=sprintf(lang('jb_search_ttl_c'), $clear_search_string) ?></h1>
		</div>
		
	<? endif ?>
	
	<?=form_open('search', array('class'=>'well well-sm form-inline')) ?>
		
		<input type="text" class="input-large form-control" placeholder="<?=lang('jb_search_plc') ?>" name="search" value="<? if (isset($search_string)) echo $search_string; ?>">
	
		<button type="submit" class="btn"><?=lang('jb_search_btn') ?></button> 
		
		&nbsp;&nbsp;
		
		<label class="radio -inline"><input type="radio" name="search_type" value="number" <? if ($mode == 'number') echo 'checked';?>> По номеру</label>&nbsp;&nbsp;&nbsp;
		<label class="radio -inline"><input type="radio" name="search_type" value="brand" <? if ($mode == 'brand') echo 'checked';?>> По бренду</label>&nbsp;&nbsp;&nbsp;
		<!--<label class="radio -inline "><input type="radio" name="search_type" value="text" <? if ($mode == 'text') echo 'checked';?>> По наименованию</label>-->
		
	</form>