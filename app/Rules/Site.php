<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
class Site implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $site;
    public function __construct($site)
    {
        $this->site = $site;
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
        $user = \App\User::where('username',$value)->first();
        $site = \App\Models\Site::where('code',$this->site)->first();
        if($user && $site){
            $siteuser = \App\Models\SiteUser::where('user_id',$user->id)->where('site_id',$site->id)->first();
            if($siteuser){
                return true;
            }
            else{
                return false;
            } 
        }
        else{
            return true;
        }
        
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'These credentials do not match our records.';
    }
}
