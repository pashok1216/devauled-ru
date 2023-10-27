<?php

$value = "Item Page";
require_once 'head.php';
if (empty($userinfo['Postcode']))
{
    echo '<div id="content-page" class="content-page">
          <div class="alert text-white bg-primary" role="alert">
         <div class="iq-alert-text">Для совершения покупок вам необходимо указать почтовый индекс. <a href="profile.php" style="color:red;"> Указать почтовый индекс</a></div>
         
         </div>
         </div>
         ';
    include 'footer.php';
    die();
}
$itemname = $_GET["name"];
$SQLGetIteminfo = $odb -> prepare("SELECT `name`, `price`, `description`, `img`, `stars`, `itemidshoppy`, `img2`, `img3`, `bigdesc` FROM `items` WHERE `name` = :name");
$SQLGetIteminfo->execute(array(
    ':name' => $itemname
));
$iteminfo = $SQLGetIteminfo -> fetch(PDO::FETCH_ASSOC);
?>

    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                        <div class="iq-card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Подробнее о <?php echo $iteminfo['name']; ?></h4>
                        </div>
                        <div class="iq-card-body pb-0">
                            <div class="description-contens align-items-top row">
                                <div class="col-md-6">
                                    <div class="iq-card-transparent iq-card-block iq-card-stretch iq-card-height">
                                        <div class="iq-card-body p-0">
                                            <div class="row align-items-center">
                                                <div class="col-3">
                                                    <ul id="description-slider-nav" class="list-inline p-0 m-0  d-flex align-items-center">
                                                        <li>
                                                            <a href="javascript:void(0);">
                                                                <img src="<?php echo $iteminfo['img']; ?>" class="img-fluid rounded w-100" alt="">
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);">
                                                                <img src="<?php echo $iteminfo['img2']; ?>" class="img-fluid rounded w-100" alt="">
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);">
                                                                <img src="<?php echo $iteminfo['img3']; ?>" class="img-fluid rounded w-100" alt="">
                                                            </a>
                                                        </li>

                                                    </ul>
                                                </div>
                                                <div class="col-9">
                                                    <ul id="description-slider" class="list-inline p-0 m-0  d-flex align-items-center">
                                                        <li>
                                                            <a href="javascript:void(0);">
                                                                <img src="<?php echo $iteminfo['img']; ?>" class="img-fluid w-100 rounded" alt="">
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);">
                                                                <img src="<?php echo $iteminfo['img2']; ?>" class="img-fluid w-100 rounded" alt="">
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);">
                                                                <img src="<?php echo $iteminfo['img3']; ?>" class="img-fluid w-100 rounded" alt="">
                                                            </a>
                                                        </li>

                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="iq-card-transparent iq-card-block iq-card-stretch iq-card-height">
                                        <div class="iq-card-body p-0">
                                            <h3 class="mb-3"><?php echo $iteminfo['name']; ?></h3>
                                            <div class="price d-flex align-items-center font-weight-500 mb-2">

                                                <span class="font-size-24 text-dark">$<?php echo $iteminfo['price']; ?></span>
                                            </div>
                                            <div class="mb-3 d-block">
                                          <span class="font-size-20 text-warning">
                                          <i class="fa fa-star mr-1"></i>
                                          <i class="fa fa-star mr-1"></i>
                                          <i class="fa fa-star mr-1"></i>
                                          <i class="fa fa-star mr-1"></i>
                                          <i class="fa fa-star"></i>
                                          </span>
                                            </div>
                                            <span class="text-dark mb-4 pb-4 iq-border-bottom d-block"><?php echo $iteminfo['bigdesc']; ?></span>
                                            <div class="mb-4 d-flex align-items-center">
                                                <a href="#" class="btn btn-primary view-more mr-2" data-shoppy-product="<?php echo $iteminfo['itemidshoppy']; ?>">Купить (BTC)</a>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

<?php
include 'footer.php';
?>