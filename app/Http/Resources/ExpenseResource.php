<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'user_id' => $this->user_id,
            'group_id' => $this->group_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'tags' => TagResource::collection($this->tags),
        ];

    
        if ($this->relationLoaded('participants')) {
            $data['participants'] = $this->participants->map(function($participant) {
                return [
                    'user_id' => $participant->id,
                    'amount_paid' => $participant->pivot->amount_paid,
                    'split_type' => $participant->pivot->split_type,
                ];
            });
        }

        return $data;
    }
}