<?php

namespace App\Api\Presto;

use App\Collection;
use App\Api\Exceptions\ExceptionRequest;
use App\Logger;
use App\Presto\Menu;
use App\Presto\PointOfSale;
use App\Presto\Product;
use Bitrix\Main\Type\Date;

class Api
{
    const clientId = '0182278222366622';
    const secretKey = 'M8DVXKMCTPXA5JSYTMDZGI2X';
    const serviceKey = 'TbEWBstRkrAOsoTveUlh3ZCHSjQ1qISXXOMxzbtL05b1mLwvUdRukHSCmvSSBC007m57fMLc5nCaElEgG3Tyr9HfH2zeYCelbV8smsiOkQX9ocusuqoM7C';

    private static $token = null;
    private static $sessionId = null;
    public static $logger = null;

    public function __construct()
    {
        static::$logger = new Logger('/upload/Logs/Api/Presto/log.txt');

        if (is_null(self::$token) || is_null(self::$sessionId)) {
            $this->getToken();
        }
    }

    /**
     * Получем токен авторизация в приложении для дальнейшего общения
     * @throws ExceptionRequest
     */
    private function getToken()
    {
        $auth = [
            "app_client_id" => self::clientId,
            "app_secret" => self::secretKey,
            "secret_key" => self::serviceKey,
        ];

        $auth = json_encode($auth);
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => 'https://online.sbis.ru/oauth/service/',
            CURLOPT_POST => true,
            CURLOPT_HEADER => 0,
            CURLOPT_POSTFIELDS => $auth,
            CURLOPT_HTTPHEADER => array(
                'Content-type: charset=utf-8'
            )
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response);

        if (is_a($result, "stdClass")) {
            self::$token = $result->token;
            self::$sessionId = $result->sid;
        } else {
            throw new ExceptionRequest("Error getting token (" . trim($response) . ")");
        }
    }

    /**
     * Получаем тороговые точки
     * @return Collection
     * @throws ExceptionRequest
     */
    public function pointOfSale($id = null): Collection
    {
        //https://sbis.ru/help/integration/api/app_presto/Presto_delyvery/salie_point
        $url = "https://api.sbis.ru/retail/point/list?product=delivery&withPhones=true&withPrices=true&withSchedule=true&page=0&pageSize=500";
        $log = new Logger('listPoints.txt');

        if (!is_null($id)) {
            $url .= "&pointId=$id";
        }

        if ($this->checkToken()) {
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $url,
                CURLOPT_HEADER => 0,
                CURLOPT_HTTPHEADER => array(
                    'Content-type: charset=utf-8',
                    "X-SBISAccessToken: " . self::$token
                )
            ));
            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response);

            if (is_a($result, 'stdClass')) {
                $collectionPoint = new Collection();

                foreach ($result->salesPoints as $point) {

//                    $log->add($point->address . " (id =" . $point->id . ")" . PHP_EOL);

                    $collectionPoint->add(new PointOfSale($point));
                }
//                $log->add("_____________________________________");

                return $collectionPoint;
            } else {
                throw new ExceptionRequest("Error in getting points of sale (" . trim($response) . ")");
            }
        }

        return new Collection();
    }

    /**
     * Получем список меню торговой точки
     * @param PointOfSale $point
     * @return Collection
     * @throws ExceptionRequest
     */
    public function menuPointOfSale(PointOfSale $point)
    {
        $log = new Logger('listMenu.txt');

        $now = new \DateTime();
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => "https://api.sbis.ru/retail/nomenclature/price-list?pointId=" . $point->property('id') . "&actualDate=" . $now->format('d.m.Y') . "&page=0&pageSize=100",
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => array(
                'Content-type: charset=utf-8',
                'X-SBISAccessToken: ' . self::$token
            )
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response);

        if (is_a($result, 'stdClass')) {
            foreach ($result->priceLists as $menu) {
                if ($menu->name == "Пиццерия") {
                    $point->add(new Menu($menu));
                }
            }
        } else {
            throw new ExceptionRequest("Failed to get the menu list from the outlet (" . trim($response) . ")");
        }
    }

    /**
     * Получем товары для меню торговой точки
     * @param PointOfSale $point
     * @param Menu $menu
     * @return Collection
     * @throws ExceptionRequest
     */
    public function productsMenu(PointOfSale $point, Menu $menu)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => 'https://api.sbis.ru/retail/nomenclature/list?product=delivery&pointId=' . $point->property('id') . '&priceListId=' . $menu->property('id') . '&withBalance=true&page=0&pageSize=500',
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => array(
                'Content-type: charset=utf-8',
                'X-SBISAccessToken: ' . self::$token
            )
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response);

        if (is_a($result, 'stdClass')) {
            $log = new Logger('listProducts.txt');

            $sections = [];
            foreach ($result->nomenclatures as $product) {
                if ($product->isParent) {
                    $sections[$product->hierarchicalId] = $product->name;
                    continue;
                }

                if (array_key_exists($product->hierarchicalParent, $sections)) {
                    $product->section = [
                        'id' => $product->hierarchicalParent,
                        'name' => $sections[$product->hierarchicalParent],
                    ];
                }

                $product = new Product($product);
                $product->setPriceList($menu->property('id'));
                $menu->add($product);
            }
        } else {
            throw new ExceptionRequest("Failed to get list of menu items (" . trim($response) . ")");
        }
    }

    public function getProductPicture($param)
    {
        $ch = curl_init();
        $headers = [];
        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => "https://api.sbis.ru/retail$param",
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => array(
                'Content-type: charset=utf-8',
                'X-SBISAccessToken: ' . self::$token
            )
        ));

        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $header) use (&$headers) {
            $len = strlen($header);
            $header = explode(':', $header, 2);

            if (count($header) < 2) { // ignore invalid headers
                return $len;
            }

            $headers[strtolower(trim($header[0]))][] = trim($header[1]);
            return $len;
        }
        );

        $response = curl_exec($ch);
        curl_close($ch);

        $matches = '';
        preg_match("/'.*/", $headers['content-disposition'][0], $matches);
        $matches = substr($matches[0], 2);

        $result = [
            'name' => $matches,
            'content' => $response,
        ];
        return $result;
    }

    public function addressOrder($address)
    {
        $ch = curl_init();
        $query = http_build_query([
            'enteredAddress' =>  trim("{$address->location}  {$address->address}"),
        ]);

        curl_setopt_array($ch, [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => 'https://api.sbis.ru/retail/delivery/suggested-address?'. $query,
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => array(
                'Content-type: charset=utf-8',
                'X-SBISAccessToken: ' . self::$token
            )
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response);

        if (is_a($result, 'stdClass')) {
            return $result->addresses;
        } else {
            throw new ExceptionRequest("No such address");
        }
    }

    public function deliveryCostOrder($addressJson)
    {
        $query = http_build_query([
            'address' =>  $addressJson->Address,
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => 'https://api.sbis.ru/retail/delivery/cost?pointId=335&' . $query,
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER =>  array(
                'Content-type: charset=utf-8',
                'X-SBISAccessToken: ' . self::$token
            )
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response);

        if (! is_null($result->district)) {
            return $result;
        } else {
            self::$logger->add("The address \"{$addressJson->Address}\" is not included in the delivery area");
            return false;
        }
    }

    public function createOrder($data)
    {
        $result = [
            'product' => 'delivery',
            'pointId' => 335,
            'datetime' => (new \DateTime())->add(new \DateInterval('PT10M'))->format('Y-m-d H:i:s'),
            'customer' => [
                'name' => $data->form->NAME,
                'phone' => $data->form->PHONE,
            ],
            'nomenclatures' => [],
            'delivery' => [
                'isPickup' => false,
                'paymentType' => 'cash',
                'addressJSON' => $data->form->addressJSON,
                'addressFull' => $data->form->addressFull,
            ],
        ];

        foreach ($data->cart as $product) {
            $result['nomenclatures'][] = [
                'externalId' => (string) $product->externalId,
                'count' => (int) $product->count,
                'name' => (string) $product->name,
                'priceListId' => (int) $product->priceListSbys,
            ];
        }

        $date = json_encode($result);
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => 'https://api.sbis.ru/retail/order/create',
            CURLOPT_POST => true,
            CURLOPT_HEADER => 0,
            CURLOPT_POSTFIELDS => $date,
            CURLOPT_HTTPHEADER =>  array(
                'Content-type: charset=utf-8',
                "X-SBISAccessToken: " . self::$token,
            )
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response);

        if (property_exists($result, "orderNumber") && empty($result->message)) {
            return $result;
        } else {

            if (property_exists($result, "error")) {
                throw new \Exception('Error create new order. Description: ' . $result->error->details);
            }

            throw new \Exception('Error create new order');
        }
    }

    private function checkToken()
    {
        if (is_null(self::$token)) {
            throw new ExceptionRequest("Token is empty");
        }

        return true;
    }
}
