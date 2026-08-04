<?php

namespace App\Services;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class Converts
{
    public function convertToJson($data)
    {
        // Si es un Model o Collection → convertir a array
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        // Si implementa JsonSerializable → usarlo
        if ($data instanceof JsonSerializable) {
            $data = $data->jsonSerialize();
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
