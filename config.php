<?php
$url = explode('?',strtolower($_SERVER['REQUEST_URI']));

if ($url[0]=='/logout'){
    require_once 'logout.php';;
}
elseif ($url[0]=='/login'){
    require_once 'login.php';;
}
elseif ($url[0]=='/products'){
    require_once 'products.php';;
}
elseif ($url[0]=='/delete_product'){
    require_once 'delete_product.php';;
}
elseif ($url[0]=='/cart'){
    require_once 'cart.php';;
}
elseif ($url[0]=='/register'){
    require_once 'register.php';;
}
elseif ($url[0]=='/adminpanel'){
    require_once 'adminpanel.php';;
}
elseif ($url[0]=='/add_product'){
    require_once 'add_product.php';;
}
elseif ($url[0]=='/orders'){
    require_once 'orders.php';;
}
elseif ($url[0]=='/search_results'){
    require_once 'search_results.php';;
}
elseif ($url[0]=='/edit_product'){
    require_once 'edit_product.php';;
}
elseif ($url[0]=='/product_info'){
    require_once 'product_info.php';;
}
elseif ($url[0]=='/home'){
    require_once 'home.php';;
}
elseif ($url[0]=='/index'){
    require_once 'index.php';;
}

?>