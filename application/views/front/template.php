<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <title>{pagetitle}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Интернет-магазин автозапчастей">
    {meta_author}
    {meta_timestamp}

    <!-- Le styles -->
    <link href="{bootstrap_css}" rel="stylesheet">
    <!--<link href="{bootstrap_responsive_css}" rel="stylesheet">-->

    <link href="{general_css}" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->

    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons 
    <link rel="shortcut icon" href="../assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">-->

    <!-- Le Scripts -->
    <script type='text/javascript' src='{jquery_js}'></script>
    <script type='text/javascript' src='{jquery_ui_js}'></script>

    <script type='text/javascript' src='{bootstrap_js}'></script>
    <script type='text/javascript' src='{general_js}'></script>

    {head_snippet_include}

    {bg_head_snippet_include}

  </head>
  <body class="{bodyclass}"  {js_data}>
    <div id="utilityMessage" class="hiddenUtilityMessage utilityMessage utilityMessageFixed"></div>
    <div id="sexywrapper">

      {topbanner}

      <div id="navi-primary" class="navbar navbar-static-top">

        <div class="navbar-inner">

          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          
          <ul class="nav">
            <li class="{homepage_selected_class} mw-979-show"><a href="{homepage_url}"><i class="icon-home"></i></a></li>
            <li class="{cart_selected_class} mw-979-show"><a href="{cart_url}"><i class="icon-shopping-cart"></i></a></li>
          </ul>

          <div class="nav-collapse collapse">
            
            <ul class="nav first-nav-block">
              <li class="{homepage_selected_class} mw-979-hide"><a href="{homepage_url}">Главная</a></li>
              <li class="{cart_selected_class} mw-979-hide"><a href="{cart_url}">Корзина</a></li>
              
              {topmenu}
                <li class="{selected_class} mw-480-hide"><a href="{url}" title="{title_attr}">{title}</a></li>
              {/topmenu}
            </ul>
            
            <form action="{search_form_action_default}" id="top_search_form" class="navbar-form pull-right" data-mode="{search_mode}" method="post" accept-charset="utf-8">

              <input id="top_search" type="text" class="span2" placeholder="Поиск по номеру" name="search" value="{search_string}">

              <div id="search-button" class="btn-group">
                <button class="btn" type="submit">Поиск</button>
                <button class="btn dropdown-toggle" data-toggle="dropdown">
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right">
                  <li class="active search_type_selector"><a href="#number" data-searchbox="top_search" data-formaction="{search_form_action_number}">Поиск по номеру</a></li>
                  <li class="search_type_selector"><a href="#brand" data-searchbox="top_search" data-formaction="{search_form_action_brand}">Поиск по бренду</a></li>
                </ul>
              </div>
            </form>

            <ul class="nav pull-right mw-979-hide">
              <li class="{auth_link_selected_class}"><a href="{auth_link_url}" title="{auth_link_title}"><i class="icon-user"></i> {auth_link_title}</a></li>
              <li class="divider-vertical"></li>
            </ul>

          </div>
        </div>
      </div>

      <div class="container-fluid">

        <div class="row-fluid">

          <div class="span3">

            <div class="well sidebar-widget">
              
              <h5 class="nav-header widget-header">Корзина</h5>

              <table class="table table-condensed removeifempty" id="cart_widget" data-removeifempty="cart_items">
                <thead>
                  <tr>
                    <th class="first">Наименование</th>
                    <th>Кол</th>
                    <th class="last">Сумма</th>
                  </tr>
                </thead>
                
                <tbody>

                {cart_items}
                  <tr class="cart_items">
                    <td class="first">{art_number} {sup_brand} {description}</td>
                    <td>{qty}</td>
                    <td class="last widget_subtotal">{subtotal_formatted}</td>
                  </tr>
                {/cart_items}

                </tbody>
              </table>

              <a href="{cart_url}" class="btn btn-info btn-mini removeifempty" data-removeifempty="cart_items">Оформить заказ</a>

              <p class="muted showifempty" data-showifempty="cart_items">Ваша корзина пуста</p>

            </div>

            <div class="well well-nav sidebar-collapseable mw-979-hide">
              <ul class="nav nav-list">
                
                <li class="nav-header jbsm-list-header">Сохраненные автомобили</li>

                <li class="nav-header">Подбор по автомобилю</li>

                {catalogues}
                  <li class="{selected_class}"><a href="{url}" title="{title_attr}">{title}</a></li>
                {/catalogues}

                <li class="nav-header removeifempty" data-removeifempty="custom_items">Каталог товаров</li>

                {custom_items}
                  <li class="{selected_class} custom_items"><a href="{url}" title="{title_attr}">{title}</a></li>
                {/custom_items}

              </ul>
            </div>

            <div class="well well-nav sidebar-collapseable mw-979-hide">
              <ul class="nav nav-list">
                <li class="nav-header">{auth_block_title}</li>

                {auth_block_links}
                  <li class="{selected_class}"><a href="{url}" title="{title_attr}">{icon} {title}</a></li>
                {/auth_block_links}

                {admin_auth_block}
              </ul>
            </div>

            {sidebar_include_snippet}

          </div><!--/div.span3-->

          <div class="span9">

            {content}

          </div><!-- /div.span9 -->

          <div class="clearfix"></div>

        </div><!-- /div.row-fluid -->
      </div><!-- /div.container-fluid -->

      <div id="footer">
        <div class="container-fluid">
          <div class="row-fluid">
            <div id="footnote_container" class="span9">
              <p>
                {footnote}
              </p>

              {foot_snippet_include}

            </div>
            <div id="whodidit_container" class="span3">
              <div id="whodidit">
                {jb_domain_devbrand}
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
    </div><!-- /div#sexywrapper-->
  </body>
</html>