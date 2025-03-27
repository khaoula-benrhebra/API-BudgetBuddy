<?php
// app/Http/Resources/GroupResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'devise' => $this->devise,
            'members' => UserResource::collection($this->users), 
            'expenses' => $this->expenses, 
        ];
    }
}
