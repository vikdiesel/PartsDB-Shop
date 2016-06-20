<div class="page-header">
    <h1>Настройки</h1>
</div>


<div class="row-fluid">
    <div class="span4">
        <h4>Замена шапки</h4>

        <p>Вы можете полностью изменить шапку магазина, вставив собственное брендированное изображение.</p>

        <p><a class="btn btn-inverse btn-small" href="<?= site_url('admin/options_headimage') ?>"><i
                    class="icon-picture icon-white"></i> Заменить шапку</a></p>
    </div>
    <div class="span4">
        <h4>Замена фона</h4>

        <p>Вы можете полностью изменить фон магазина, вставив собственное изображение.</p>

        <p><a class="btn btn-inverse btn-small" href="<?= site_url('admin/options_bgimage') ?>"><i
                    class="icon-fullscreen icon-white"></i> Заменить фон</a></p>
    </div>
    <div class="span4">
        <h4>Редактор шаблона</h4>

        <p>HTML-код шаблона магазина можно отредактировать на ваш вкус.</p>

        <p><a class="btn btn-inverse btn-small" href="<?= site_url('admin/options_template_editor') ?>"><i
                    class="icon-edit icon-white"></i> Редактор шаблона</a></p>
    </div>

</div>
<hr>
<div class="row-fluid">
    <div class="span4">
        <h4>Живой чат, Счетчики и вставка кода на сайт</h4>

        <p>Вставка HTML/CSS/JS для подключения <a href="http://www.jivosite.ru/?pid=1806" target="_blank">Живого
                чата</a>, <a href="http://metrika.yandex.ru/" target="_blank">Яндекс.Метрики</a>, <a
                href="http://webmaster.yandex.ru/" target="_blank">Яндекс.Вебмастера</a>, <a
                href="https://www.google.com/analytics/" target="_blank">Google Analytics</a> или любой другой сторонней
            системы.</p>

        <p><a class="btn btn-inverse btn-small" href="<?= site_url('admin/options_header_footer') ?>"><i
                    class="icon-tasks icon-white"></i> Вставка кода</a></p>

    </div>


</div>

<hr>


<div class="row-fluid">


    <div class="span4">
        <h4>Сторонние каталоги</h4>

        <p>Здесь вы можете подключить каталоги любого стороннего поставщика, такого как <a
                href="http://parts.autoxp.ru/allprices/catalog.aspx" target="_blank">AutoXP</a>,
            <a href="http://www.catcar.info/" target="_blank">Сatcar</a> или любого другого.</p>

        <p><a class="btn btn-primary btn-small" href="<?= site_url('admin/posts/extcat') ?>"><i
                    class="icon-list-alt icon-white"></i> Управление каталогами</a></p>
    </div>


    <div class="span4">
        <h4>Платежные реквизиты</h4>

        <p>Здесь вы можете поменять платежные реквизиты, которые видит пользователь при оформлении заказа.</p>

        <p><a class="btn btn-primary btn-small" href="<?= site_url('admin/options_payment') ?>"><i
                    class="icon-map-marker icon-white"></i> Управление реквизитами</a></p>
    </div>


</div>

<hr>


<div class="row-fluid">
    <div class="span4">
        <h4>Общие настройки</h4>

        <p>Основные настройки типа заголовка, подзаголовка, e-mail администратора, часового пояса и т.д&hellip;</p>

        <p><a class="btn btn-primary btn-small" href="<?= site_url('admin/options_common') ?>"><i
                    class="icon-wrench icon-white"></i> Общие настройки</a></p>
    </div>

    <div class="span4">
        <h4>Бренды в общем каталоге</h4>

        <p>Редактирование перечня автопроизводителей присутствующих в <a href="<?= base_url(); ?>" title="_blank">общем
                каталоге</a> <em>(по умолчанию выбраны все, но для удобства можно убрать
                некоторых из них)</em>.</p>

        <p><a class="btn btn-primary btn-small" href="<?= site_url('admin/options_mfgs') ?>"><i
                    class="icon-tags icon-white"></i> Бренды</a></p>
    </div>


    <div class="span4">
        <h4>Способы доставки</h4>

        <p>Перечень способов доставки из которых можно выбрать при создании заказа.</p>

        <p><a class="btn btn-primary btn-small" href="<?= site_url('admin/options_delivery') ?>"><i
                    class="icon-gift icon-white"></i> Способы доставки</a></p>
    </div>

</div>
<hr>
<div class="row-fluid">
    <div class="span4">
        <h4>Смена пароля</h4>

        <p>Чтобы изменить пароль администратора, &mdash; перейдите в этот раздел.</p>

        <p><a class="btn btn-primary btn-small" href="<?= site_url('admin/options_change_pass') ?>"><i
                    class="icon-eye-open icon-white"></i> Сменить пароль</a></p>
    </div>


</div>
<hr>