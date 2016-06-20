<ul class="breadcrumb">
    <li><a href="/"><?=lang('jb_breadcrumbs_home')?></a> <span class="divider">/</span></li>
    <? foreach ($breadcrumb_array as $brc): ?>

        <li><a href="<?=$brc->link ?>"><?=$brc->title ?></a> <span class="divider">/</span></li>
    <? endforeach ?>
</ul>