<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencieResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id'=> $this->id,
            'name'=> $this->name,
            'entry_price'=> $this->entry_price,
            'tp1'=> $this->tp1,
            'tp2'=> $this->tp2,
            'tp3'=> $this->tp3,
            'tp4'=> $this->tp4,
            'tp5'=> $this->tp5,
            'sl'=> $this->sl,
            'status'=> $this->status,
            'sgy_type'=> $this->sgy_type,
            'created'=> $this->created_at,
        ];


    }
}
