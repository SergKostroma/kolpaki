<?php

namespace App\Presto;

use App\Contracts\Property;

class Product extends BaseObject
{
    protected $data = null;
    protected $api = null;
    private $priceList = null;

    public function getPictures()
    {
        if (is_array($this->property('images'))) {
            foreach ($this->property('images') as $key => $img) {
                $img = $this->api->getProductPicture($img);

                $externalIpPicture = md5($this->property('externalId') . $img['name']);
                $existPicture = $this->getPictureByExternalId($externalIpPicture);
                if ($existPicture) {
                    $this->data->pictures[] = [
                        'VALUE' => $existPicture,
                        'DESCRIPTION' => ''
                    ];
                } else {
                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/upload/" . $img['name'], $img['content']);
                    $fileArray = \CFile::MakeFileArray(
                        $_SERVER['DOCUMENT_ROOT'] . "/upload/" . $img['name'],
                        false,
                        false,
                        $externalIpPicture
                    );
                    $this->data->pictures[] = [
                        'VALUE' => $fileArray,
                        'DESCRIPTION' => ''
                    ];
                }
            }
        }
    }

    public function hasPicture()
    {
        return is_array($this->property('images'));
    }

    private function getPictureByExternalId($externalId)
    {
        $result = \CFile::GetList([], ['EXTERNAL_ID' => $externalId]);

        return $result->Fetch();
    }

    public function setPriceList($id)
    {
        $this->priceList = $id;
    }

    public function getPriceList()
    {
        return !is_null($this->priceList) ? $this->priceList : 0;
    }
}
