<?php
use Bitrix\Main\Page\Asset;
$asset = Asset::getInstance();

//JavaScript
$asset->addString('<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>');
$asset->addString('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>');
$asset->addJs('/resources/js/slider.js');
$asset->addJs('/resources/js/input-quantity.js');
$asset->addJs('/resources/js/basket-mobile.js');
//$asset->addJs('/resources/js/msnet/catalogAction.js');
$asset->addString('<script src="/resources/js/msnet/cartActions.js" type="module"></script>');
$asset->addString('<script src="/resources/js/msnet/catalogAction.js" type="module"></script>');

//Css
$asset->addCss('/resources/css/base.css');
$asset->addCss('/resources/css/slider.css');
$asset->addCss('/public/css/styles.min.css');
$asset->addString('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">');

//Plugins JavaSctipt
$asset->addJs('/public/plugins/slider/js/slider.min.js');
$asset->addJs('/public/plugins/cookieMaster/cookie.min.js');
$asset->addJs('/public/plugins/jquery.maskedinput-master/jquery.maskedinput.min.js');

//Plugins Css
//$asset->addCss($_SERVER['DOCUMENT_ROOT'] . '/public/plugins/slider/css/slick.css');
//$asset->addCss($_SERVER['DOCUMENT_ROOT'] . '/public/plugins/slider/css/slick-theme.css');