<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="UserResource",
 *     description="User item",
 *     @OA\Xml(
 *         name="UserResource"
 *     )
 * )
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    /**
     * @OA\Property(type="integer", example="100", description="User identifier", property="id"),
     * @OA\Property(type="string", example="User Name", description="User name", property="name"),
     * @OA\Property(type="string", example="admin@mail.ru", description="User email", property="email"),
     * @OA\Property(type="string", example="11|t3lofbFZlz7LzFW0iaRxbzSswSTXNW4DoRxd9IA", description="Authorization token", property="token"),
     * @OA\Property(type="string", format="date-time", example="01.01.2020 12:00:00", description="User creation time", property="created_at"),
     * @OA\Property(type="string", format="date-time", example="01.01.2020 12:00:00", description="User update time", property="updated_at"),
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'token' => $this->when(null != $this->token, function(){
                return $this->token;
            }),
            'created_at' => $this->created_at->format(config('app.date_format_full')),
            'updated_at' => $this->updated_at->format(config('app.date_format_full'))
        ];
    }
}
