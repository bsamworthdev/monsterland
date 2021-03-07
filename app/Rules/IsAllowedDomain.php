<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsAllowedDomain implements Rule
{

    /**
    * Allowed email domains for
    * user registration
    *
    * @var array
    */
    protected $blockedDomains = [
        'dupived.online',
        'twzhhq.online',
        'miucce.com',
        'kiabws.com',
        'niwghx.com',
        'upived.online',
        'mailnest.net'
    ];

    protected $blockedTLDs = [
        'online',
    ];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $domain = substr(strrchr($value, "@"), 1);
        if (in_array($domain, $this->blockedDomains)) {
            return false;
        }
        $tld = substr(strrchr($value, "."), 1);
        if (in_array($tld, $this->blockedTLDs)) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Sorry your account could not be registered at this time. Please try again later.';
    }
}
