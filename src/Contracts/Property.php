<?php

namespace App\Contracts;

abstract class Property
{
    protected $data = null; //Object stdClass

    /**
     * @param string $pathToPropertry разделитель вложенности "."
     * @return bool|mixed|null
     */
    public function property(string $pathToPropertry)
    {
        $pathToPropertry = explode('.', $pathToPropertry);

        if (! is_array($pathToPropertry)) {
            return $this->data;
        }

        if (array_key_exists($pathToPropertry[0], $this->data)) {
            $data = $this->data;

            foreach ($pathToPropertry as $prop) {
                if (property_exists($data, $prop)) {
                    $data = $data->$prop;
                }
            }

            return $data;
        }

        return false;
    }
}
