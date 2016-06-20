<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?=_cfg('jb_domain_branded')?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Le styles -->
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/2.3.2/css/bootstrap.min.css" rel="stylesheet">
	<link href="/e/admin.css/login.css?ver=2.2.2" rel="stylesheet">

  </head>

  <body>

    <div class="container">		

      <?=form_open('admin/login', array('id'=>'admin_login_form', 'class'=>'form-box form-signin')) ?>
        <h2 class="form-signin-heading"><?=_cfg('jb_domain_branded')?></h2>
		
		<?=$errors?>
		
		<div class="auth-fields">
		
        <input type="text" name="login" class="input-block-level" placeholder="Логин" value="<?=set_value('login', (isset($email))?$email:'')?>">
        <input type="password" name="password" class="input-block-level" placeholder="Пароль">
		
		</div>
        
        <button class="btn btn-primary" type="submit">Авторизация</button>

      </form>
	  
	  <div class="login-form-bottom muted">
		Это форма авторизации. Назад к сайту <a href="<?=base_url()?>"><?=_jb_sitedata('title')?>&rarr;</a>
	  </div>

    </div> <!-- /container -->

  </body>
</html>