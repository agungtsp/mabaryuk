<?php namespace MabarYuk\User\Components;

use Auth;
use Flash;
use Lang;
use Mail;
use Url;
use Validator;
use ValidationException;
use ApplicationException;
use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use RainLab\User\Models\User as UserModel;
use Rainlab\User\Components\ResetPassword as RainLabResetPassword;

/**
 * ResetPassword Component extend from RainLab
 */
class ResetPassword extends RainLabResetPassword
{
    public function componentDetails()
    {
        return [
            'name' => 'ResetPassword Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    //
    // AJAX
    //

    /**
     * Trigger the password reset email
     */
    public function onRestorePassword()
    {
        $rules = [
            'email' => 'required|email|between:6,255'
        ];

        $validation = Validator::make(post(), $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        $user = UserModel::findByEmail(post('email'));
        if (!$user || $user->is_guest) {
            throw new ApplicationException(Lang::get(/*A user was not found with the given credentials.*/'rainlab.user::lang.account.invalid_user'));
        }

        $code = implode('!', [$user->id, $user->getResetPasswordCode()]);

        $link = $this->makeResetUrl($code);

        $data = [
            'name' => $user->name,
            'username' => $user->username,
            'link' => $link,
            'code' => $code
        ];

        Mail::send('rainlab.user::mail.restore', $data, function($message) use ($user) {
            $message->to($user->email, $user->full_name);
        });
    }

    /**
     * Perform the password reset
     */
    public function onResetPassword()
    {
        $rules = [
            'code'     => 'required',
            'password' => ['required','confirmed','min:8','regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
        ];

        $messages = [
            'password.min' => 'Password must contain at least 8 characters, including UPPER/lowercase, specialchar, & numbers',
            'password.regex' => 'Password must contain at least 8 characters, including UPPER/lowercase, specialchar, & numbers',
        ];

        $validation = Validator::make(post(), $rules, $messages);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        $errorFields = ['code' => Lang::get(/*Invalid activation code supplied.*/'rainlab.user::lang.account.invalid_activation_code')];

        /*
         * Break up the code parts
         */
        $parts = explode('!', post('code'));
        if (count($parts) != 2) {
            throw new ValidationException($errorFields);
        }

        list($userId, $code) = $parts;

        if (!strlen(trim($userId)) || !strlen(trim($code)) || !$code) {
            throw new ValidationException($errorFields);
        }

        if (!$user = Auth::findUserById($userId)) {
            throw new ValidationException($errorFields);
        }

        if (!$user->attemptResetPassword($code, post('password'))) {
            throw new ValidationException($errorFields);
        }

        // Check needed for compatibility with legacy systems
        if (method_exists(\RainLab\User\Classes\AuthManager::class, 'clearThrottleForUserId')) {
            Auth::clearThrottleForUserId($user->id);
        }

        Flash::success(Lang::get('talenhero.user::lang.alert.change_password_success'));
        Auth::login($user);

        return Redirect::intended("/");
    }
}
