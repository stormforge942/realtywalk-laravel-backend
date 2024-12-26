<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PointCoords;

class Polygon extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (!empty($this->code)) ? $this->code : null,
            'dbId' => $this->id,
            'color' => (!empty($this->code)) ? $this->color : "275b30",
            'title' => $this->title,
            'zoom' => $this->zoom,
            'coords' => PointCoords::collection($this->points)
        ];
    }
}
