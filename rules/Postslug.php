<?php namespace Dynamedia\Posts\Rules;

use Dynamedia\Posts\Models\Post;
use Lang;
use Illuminate\Contracts\Validation\Rule;

/**
 * Reserved keyword rule.
 *
 * Validates for the use of any PHP-reserved keywords or constants, as specified from the PHP Manual
 * http://php.net/manual/en/reserved.keywords.php
 * http://php.net/manual/en/reserved.other-reserved-words.php
 */
class Postslug implements Rule
{

    /**
     * Validate the provided value
     *
     * @param string $attribute The attribute being tested
     * @param string $value The value being tested
     * @param array $params The parameters passed to the rule
     * @return bool
     */
    public function validate($attribute, $value, $params)
    {
        return $this->passes($attribute, $value);
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
        $exists = Post::where('slug', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return Lang::get('rainlab.builder::lang.validation.reserved');
    }
}
