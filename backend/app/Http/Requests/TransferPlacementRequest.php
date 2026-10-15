<?php

namespace App\Http\Requests;

class TransferPlacementRequest extends StorePlacementRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'reason' => ['required', 'string', 'max:500'],
        ]);
    }
}