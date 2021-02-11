<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Flow;

class ValidateUniqueFlowName implements Rule
{
    private $botId;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($botId)
    {
        $this->botId = $botId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $flow = Flow::where([
            'name' => $value,
            'bot_id' => $this->botId
        ])->first();
        if($flow) return false;
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The name has already been taken';
    }
}
