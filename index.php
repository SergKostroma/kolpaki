<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
$APPLICATION->SetTitle('Главная');
?>
    <div class="main-container">
    <div class="products-container">
        <?$APPLICATION->IncludeComponent("bitrix:catalog.section.list","",
            Array(
                "VIEW_MODE" => "TEXT",
                "SHOW_PARENT_NAME" => "Y",
                "IBLOCK_TYPE" => "",
                "IBLOCK_ID" => IBLOCK_CATALOG,
                "SECTION_ID" => '',
                "SECTION_CODE" => "",
                "SECTION_URL" => "",
                "COUNT_ELEMENTS" => "Y",
                "TOP_DEPTH" => "2",
                "SECTION_FIELDS" => "",
                "SECTION_USER_FIELDS" => "",
                "ADD_SECTIONS_CHAIN" => "Y",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "3600",
                "CACHE_NOTES" => "",
                "CACHE_GROUPS" => "Y"
            )
        );?>
    </div>
    <a class="scrollTo" href="#scrollTo">
        <div class="up-button">Наверх ↑</div>
    </a>

    <h2 class="header-h2">Зона доставки</h2>
    <div class="delivery-zone">
        <script type="text/javascript" charset="utf-8" style="border-radius: 34px 34px 34px 34px;" async
                src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A4d76132d7a3d68ab3025f56908705e423f35ea577672e3e31fbf74de4e493655&amp;width=100%25&amp;height=539&amp;lang=ru_RU&amp;scroll=true"></script>
    </div>
    <div class="socials">
        <a href="https://vk.com/pekarnykolpaki" class="vk"><img src="/public/images/soc_vk.png"></a>
    </div>


    <div class="product-info-mobile">

    </div>

    <div class="modal fade product-info-modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

    </div>


    

<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>