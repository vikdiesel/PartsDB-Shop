<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <title><?=_cfg('jb_domain_branded')?> &mdash; <?=_jb_sitedata('title')?></title>
    <meta name="description" content="">
    <meta name="author" content="">
	
    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le styles -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/2.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/e/admin.css/general.css?204">
		
	<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
	<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js'></script>
	<script type='text/javascript' src='https://maxcdn.bootstrapcdn.com/bootstrap/2.3.2/js/bootstrap.min.js'></script>
	<script type='text/javascript' src='/e/admin.js/general.js?203'></script>
	
	<?=$javascripts?>
	
  </head>

  <body class="<? if (isset($bodyclass)) echo $bodyclass ?>">

    <div id="navi" class="navbar navbar-fixed-top">
      
	  <div class="navbar-inner ">
			<a class="brand" href="<?=site_url('admin')?>"><?=_cfg('jb_domain_branded')?></a>
			<ul class="nav">
				<li class="divider-vertical"></li>
				<li class="<?=menu_selected('admin')?>"><a href="<?=site_url('admin')?>">Начало</a></li>
				<li class="<?=menu_selected('admin/order.*')?> hide-on-small"><a href="<?=site_url('admin/orders')?>">Заказы</a></li>
				<li class="<?=menu_selected(array('admin/vendor.*','admin/import_prices.*','admin/prices_lookup.*'))?> hide-on-small"><a href="<?=site_url('admin/vendors_list')?>">Импорт</a></li>
				<li class="<?=menu_selected(array('admin/terms/itemcats.*', 'admin/term_add/itemcats.*', 'admin/term_edit/itemcats.*', 'admin/posts/item.*', 'admin/post_add/item.*', 'admin/post_edit/item.*'))?> hide-on-small"><a href="<?=site_url('admin/posts/item')?>">Товары</a></li>
				<li class="<?=menu_selected(array('admin/posts/page.*', 'admin/post_add/page.*', 'admin/post_edit/page.*'))?> hide-on-small"><a href="<?=site_url('admin/posts/page')?>">Страницы</a></li>
				<li class="<?=menu_selected(array('admin/options.*', 'admin/posts/extcat.*', 'admin/post_add/extcat.*', 'admin/post_edit/extcat.*'))?> hide-on-small"><a href="<?=site_url('admin/options')?>">Настройки</a></li>
			</ul>
			<ul class="nav pull-right">
				<li class="divider-vertical"></li>
				<li><a href="<?=site_url('admin/logout')?>"><i class="icon-off"></i> Выход</a></li>
			</ul>
		</div>
        
    </div>

<div class="container-fluid">
    <div class="row-fluid">
      <div class="span3">
		<ul class="nav nav-list well">
		  <li class="<?=menu_selected('admin')?>"><a href="<?=site_url('admin')?>"><i class="icon-home"></i> Начало</a></li>
		  <li class="nav-header">Ваш магазин</li>
          <li><a href="<?=base_url()?>" target="_blank"><i class="icon-globe"></i> Перейти на главную магазина</a></li>

          <li class="nav-header">Работа с заказами</li>
          <li class="<?=menu_selected(array('admin/order.*', 'admin/status.*'))?>"><a href="<?=site_url('admin/orders')?>"><i class="icon-ok-circle"></i> Заказы</a></li>
          <!--<li class="<?=menu_selected('admin/orders_vndr')?>"><a href="<?=site_url('admin/orders_vndr')?>"><i class="icon-list"></i> Заказы по поставщикам</a></li>
          <li class="<?=menu_selected(array('admin/orders_archive'))?>"><a href="<?=site_url('admin/orders_archive')?>"><i class="icon-calendar"></i> Архив заказов</a></li>-->
          <li class="<?=menu_selected('admin/users.*')?>"><a href="<?=site_url('admin/users')?>"><i class="icon-user"></i> Клиенты и скидки</a></li>

          <li class="nav-header">Импорт наличия/цен</li>
		  <li class="<?=menu_selected(array('admin/vendors_list.*','admin/vendor_edit.*','admin/import_prices.*','admin/prices_lookup.*'))?>"><a href="<?=site_url('admin/vendors_list')?>"><i class="icon-download-alt"></i> Импорт наличия/цен</a></li>
          
		  <li class="nav-header">Дополнительно</li>
		  <li class="<?=menu_selected(array('admin/posts/page.*', 'admin/post_add/page.*', 'admin/post_edit/page.*'))?>"><a href="<?=site_url('admin/posts/page')?>"><i class="icon-file"></i> Страницы</a></li>
		  <li class="<?=menu_selected(array('admin/posts/slide.*', 'admin/post_add/slide.*', 'admin/post_edit/slide.*'))?>"><a href="<?=site_url('admin/posts/slide')?>"><i class="icon-film"></i> Слайды</a></li>
		  <li class="<?=menu_selected(array('admin/terms/itemcats.*', 'admin/term_add/itemcats.*', 'admin/term_edit/itemcats.*', 'admin/posts/item.*', 'admin/post_add/item.*', 'admin/post_edit/item.*'))?>"><a href="<?=site_url('admin/posts/item')?>"><i class="icon-list-alt"></i> Ваши товары</a></li>
		  <li class="<?=menu_selected(array('admin/vendor_crosses_list.*', 'admin/import_crosses.*', 'admin/crosses_lookup.*'))?>"><a href="<?=site_url('admin/vendor_crosses_list')?>"><i class="icon-download-alt"></i> Импорт кроссов</a></li>
      		<li class="<?=menu_selected('admin/stats.*')?>"><a href="<?=site_url('admin/stats')?>"><i class="icon-signal"></i> Статистика запросов</a></li>
			<li class="divider"></li>
			<li class="<?=menu_selected(array('admin/options.*', 'admin/posts/extcat.*', 'admin/post_add/extcat.*', 'admin/post_edit/extcat.*'))?>"><a href="<?=site_url('admin/options')?>"><i class="icon-wrench"></i> Настройки</a></li>
		</ul>
		
      </div>
      <div class="span9">
