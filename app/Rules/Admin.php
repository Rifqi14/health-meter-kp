<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
class Admin implements Rule
{
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
        $user = \App\User::where('username',$value)->first();
        if($user){
            $siteuser = \App\Models\SiteUser::where('user_id',$user->id)->get()->count();
            if($siteuser > 0){
                return false;
            }
            else{
                return true;
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
