<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "front";
$route['404_override'] = '';

// Parts
$route['find/(:any)'] = 'front/nice_permalink/$1';
$route['find'] = 'front/mfgs';
$route['autopart/(:any)'] = 'front/part/$1';
$route['search'] = 'front/search';
$route['search/(:any)'] = 'front/search/$1';

// Cart, Order, User and the rest
$route['cart'] = 'front/cart_show';
$route['cart/add/(:any)'] = 'front/cart_add/$1';
$route['cart/update'] = 'front/cart_update';
$route['order/make'] = 'front/order_make';
$route['order/make/void-user'] = 'front/order_make_nouser';
$route['order/(:any)'] = 'front/order_get/$1';
$route['payment/(:any)'] = 'front/payment_result/$1';
$route['user/orders'] = 'front/orders_list';
$route['user/login'] = 'front/user_login';
$route['user/login/(:any)'] = 'front/user_login/$1';
$route['user/logout'] = 'front/user_logout';
$route['user/logout/(:any)'] = 'front/user_logout/$1';
$route['user/remind-pass'] = 'front/user_pass_remind';
$route['user/remind-pass/(:any)'] = 'front/user_pass_remind/$1';
$route['user/register'] = 'front/user_register';
$route['user/change_pass'] = 'front/user_change_password';
$route['auto/(:any)'] = 'front/page/$1';

// Pages
$route['page/(:any)'] = 'front/page/$1';

// Posts and Terms
$route['post/(:any)'] = 'front/post/$1';
$route['terms'] = 'front/terms';
$route['terms/(:any)'] = 'front/terms/$1';

// Admin
$route['admin/import_prices/(:num)'] = 'admin/import/vendor/$1';
$route['admin/import_crosses/(:num)'] = 'admin/import/vendor/$1';

/* End of file routes.php */
/* Location: ./application/config/routes.php */