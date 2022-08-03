<?php namespace MabarYuk\User\Components;

use Carbon\Carbon;
use DB;
use Lang;
use RainLab\User\Facades\Auth;
use Mail;
use Event;
use Flash;
use Input;
use Request;
use Redirect;
use Validator;
use ValidationException;
use ApplicationException;
use October\Rain\Auth\AuthException;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Models\UserGroup as UserGroupModel;
use RainLab\User\Models\Settings as UserSettings;
use Rainlab\User\Components\Account as RainLabAccount;
use MabarYuk\Job\Models\Applicant as ApplicantModel;
use MabarYuk\User\Models\Certificate as CertificateModel;
use MabarYuk\User\Models\Education as EducationModel;
use MabarYuk\User\Models\NonformalEducation as NonformalEducationModel;
use MabarYuk\User\Models\Organization as OrganizationModel;
use MabarYuk\User\Models\WorkingExperience as WorkingExperienceModel;
use MabarYuk\User\Models\Project as ProjectModel;
use MabarYuk\User\Models\UsersSkill as UsersSkillModel;
use MabarYuk\User\Models\Lang as LangModel;
use Exception;
use Log;
use PHPUnit\Framework\Constraint\IsTrue;
use ReCaptcha\ReCaptcha;
use MabarYuk\User\Models\Recaptcha as RecaptchaSetting;

/**
 * Account Component extend from RainLab
 */
class Account extends RainLabAccount
{
    public function componentDetails()
    {
        return [
            'name' => 'Account Component',
            'description' => 'It\'s extended from RainLab.User plugin.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun() {
         /*
         * Redirect to HTTPS checker
         */
        if ($redirect = $this->redirectForceSecure()) {
            return $redirect;
        }

        /*
         * Activation code supplied
         */
        if ($code = $this->activationCode()) {
            $this->onActivate($code);
        }

        $this->prepareVars();
    }

    /**
     * Register the user
     */
    public function onRegister()
    {
        try {
            // use the reCAPTCHA PHP client library for validation
            if ((bool) RecaptchaSetting::get('is_enable', false)) {
                $recaptcha = new ReCaptcha(RecaptchaSetting::get('secret_key'));
                $resp = $recaptcha->setExpectedAction(post('action'))
                                ->setScoreThreshold(0.5)
                                ->verify(post('token'), $_SERVER['REMOTE_ADDR']);
            } else {
                $resp = new class {
                    function isSuccess() {
                        return true;
                    }
                    function getErrorCodes() {
                        return 'Recaptcha is skipped';
                    }
                };
            }

            // verify the response
            if ($resp->isSuccess()) {
                // valid submission
                if (!$this->canRegister()) {
                    throw new ApplicationException(Lang::get(/*Registrations are currently disabled.*/'rainlab.user::lang.account.registration_disabled'));
                }

                if ($this->isRegisterThrottled()) {
                    throw new ApplicationException(Lang::get(/*Registration is throttled. Please try again later.*/'rainlab.user::lang.account.registration_throttled'));
                }

                /*
                 * Validate input
                 */
                $data = post();

                if (!array_key_exists('password_confirmation', $data)) {
                    $data['password_confirmation'] = post('password');
                }

                $rules = [
                    'email'    => 'required|email|between:6,255|unique:users,email',
                    'password' => ['required','confirmed','min:8','regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
                    'phone'    => 'required|digits_between:8,14|numeric',
                ];

                if ($this->loginAttribute() !== UserSettings::LOGIN_USERNAME) {
                    unset($rules['username']);
                }

                $messages = [
                    'password.min' => 'Password must contain at least 8 characters, including UPPER/lowercase, specialchar, & numbers',
                    'password.regex' => 'Password must contain at least 8 characters, including UPPER/lowercase, specialchar, & numbers',
                ];

                $validation = Validator::make(
                    $data,
                    $rules,
                    $messages,
                    $this->getCustomAttributes()
                );

                if ($validation->fails()) {
                    throw new ValidationException($validation);
                }

                /*
                 * Record IP address
                 */
                if ($ipAddress = Request::ip()) {
                    $data['created_ip_address'] = $data['last_ip_address'] = $ipAddress;
                }

                /*
                 * Record Register As
                 */
                if (@$data['is_talent'] == 1) {
                    $data['register_as']    = 'talent';
                    $data['last_active_as'] = 'talent';
                } else {
                    $data['register_as']    = 'hero';
                    $data['last_active_as'] = 'hero';
                }

                /*
                 * Register user
                 */
                Event::fire('rainlab.user.beforeRegister', [&$data]);

                $requireActivation = UserSettings::get('require_activation', true);
                $automaticActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_AUTO;
                $userActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_USER;
                $adminActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_ADMIN;
                $user = Auth::register($data, $automaticActivation);

                /*
                 * Attach group user
                 */
                $user->groups()->save(UserGroupModel::where('code', 'hero')->first());

                Event::fire('rainlab.user.register', [$user, $data]);

                /*
                 * Activation is by the user, send the email
                 */
                if ($userActivation) {
                    $this->sendActivationEmail($user);

                    Flash::success(Lang::get(/*An activation email has been sent to your email address.*/'rainlab.user::lang.account.activation_email_sent'));
                }

                $intended = false;

                /*
                 * Activation is by the admin, show message
                 * For automatic email on account activation RainLab.Notify plugin is needed
                 */
                if ($adminActivation) {
                    Flash::success(Lang::get(/*You have successfully registered. Your account is not yet active and must be approved by an administrator.*/'rainlab.user::lang.account.activation_by_admin'));
                }

                /*
                 * Automatically activated or not required, log the user in
                 */
                if ($automaticActivation || !$requireActivation) {
                    Auth::login($user, $this->useRememberLogin());
                    $intended = true;
                }

                /*
                 * Redirect to the intended page after successful sign in
                 */
                if ($redirect = $this->makeRedirection($intended)) {
                    return $redirect;
                }
            } else {
                // collect errors and display it
                $errors = $resp->getErrorCodes();
                Log::error($errors);
                throw new Exception('Recaptcha error');
            }

        } catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }

    public function onCreateCompany()
    {
        // use the reCAPTCHA PHP client library for validation
        if ((bool) RecaptchaSetting::get('is_enable', false)) {
            $recaptcha = new ReCaptcha(RecaptchaSetting::get('secret_key'));
            $resp = $recaptcha->setExpectedAction(post('action'))
                            ->setScoreThreshold(0.5)
                            ->verify(post('token'), $_SERVER['REMOTE_ADDR']);
        } else {
            $resp = new class {
                function isSuccess() {
                    return true;
                }
                function getErrorCodes() {
                    return 'Recaptcha is skipped';
                }
            };
        }

        // verify the response
        if ($resp->isSuccess()) {
            $data = post();
            $messages = [
                'password.min' => 'Password must contain at least 8 characters, including UPPER/lowercase, specialchar, & numbers',
                'password.regex' => 'Password must contain at least 8 characters, including UPPER/lowercase, specialchar, & numbers',
            ];
            $rules = [
                'email'    => 'required|email|between:6,255|unique:users,email',
                'password' => ['required','confirmed','min:8','regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
                'phone'    => 'required|digits_between:8,14|numeric',
            ];

            $validation = Validator::make(
                $data,
                $rules,
                $messages
            );

            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            $register = [
                'email' => $data['email'],
                'password' => $data['password'],
                'password_confirmation' => $data['password_confirmation']
            ];
            Event::fire('rainlab.user.beforeRegister', [$data]);

            $requireActivation = UserSettings::get('require_activation', true);
            $automaticActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_AUTO;
            $userActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_USER;
            $adminActivation = UserSettings::get('activate_mode') == UserSettings::ACTIVATE_ADMIN;
            $user = Auth::register($register, $automaticActivation);
            $user->groups()->save(UserGroupModel::where('code', 'admin_company')->first());
            Event::fire('rainlab.user.register', [$user, $data]);

            $company = new Company();
            $company->name = $data['name'];
            $company->ref_industry_id = $data['industry'];
            $company->phone = $data['phone'];
            $company->save();

            if ($userActivation) {
                $this->sendActivationEmail($user);

                Flash::success(Lang::get(/*An activation email has been sent to your email address.*/'rainlab.user::lang.account.activation_email_sent'));
            }

            $intended = false;

            /*
            * Activation is by the admin, show message
            * For automatic email on account activation RainLab.Notify plugin is needed
            */
            if ($adminActivation) {
                Flash::success(Lang::get(/*You have successfully registered. Your account is not yet active and must be approved by an administrator.*/'rainlab.user::lang.account.activation_by_admin'));
            }

            /*
            * Automatically activated or not required, log the user in
            */
            if ($automaticActivation || !$requireActivation) {
                Auth::login($user, $this->useRememberLogin());
                $intended = true;
            }

            /*
            * Redirect to the intended page after successful sign in
            */
            if ($redirect = $this->makeRedirection($intended)) {
                return $redirect;
            }
        } else {
            // collect errors and display it
            $errors = $resp->getErrorCodes();
            Log::error($errors);
            throw new Exception('Recaptcha error');
        }
    }

    /**
     * Check email
     */
    public function onCheckEmail()
    {
        return ['isTaken' => Auth::findUserByLogin(post('email')) ? 1 : 0];
    }

    /**
     * Update the user
     *
     * @author Siti Hasuna <sh.hanaaa@gmail.com>
     */
    public function onUpdate()
    {
        if (!$user = $this->user()) {
            return;
        }

        $data = post();

        $rules = [
            'email'            => 'required|email|between:6,255|unique:users,email,'.$user->id,
            'password_current' => 'required',
            'phone'            => 'required|digits_between:8,14|numeric',
        ];

        $validation = Validator::make(
            $data,
            $rules
        );

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        if ($this->updateRequiresPassword()) {
            if (!$user->checkHashValue('password', $data['password_current'])) {
                throw new ValidationException(['password_current' => Lang::get('rainlab.user::lang.account.invalid_current_pass')]);
            }
        }

        if (Input::hasFile('avatar')) {
            $user->avatar = Input::file('avatar');
        }

        if (Input::hasFile('banner')) {
            $user->banner = Input::file('banner');
        }

        $user->fill($data);
        $user->save();

        /*
         * Password has changed, reauthenticate the user
         */
        if (array_key_exists('password', $data) && strlen($data['password'])) {
            Auth::login($user->reload(), true);
        }

        Flash::success(post('flash', Lang::get(/*Settings successfully saved!*/'rainlab.user::lang.account.success_saved')));

        /*
         * Redirect
         */
        if ($redirect = $this->makeRedirection()) {
            return $redirect;
        }

        $this->prepareVars();
    }

    /**
     * Change Password
     *
     * @author Siti Hasuna <sh.hanaaa@gmail.com>
     */
    public function onChangePassword()
    {
        if (!$user = $this->user()) {
            return;
        }

        $data = post();

        $rules = [
            'old_password' => 'required',
            'password'     => ['required','confirmed','min:8','regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
        ];

        $messages = [
            'password.min' => 'Password must contain at least 8 characters, including UPPER/lowercase, specialchar, & numbers',
            'password.regex' => 'Password must contain at least 8 characters, including UPPER/lowercase, specialchar, & numbers',
        ];

        $validation = Validator::make(
            $data,
            $rules,
            $messages
        );

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        if (!$user->checkHashValue('password', $data['old_password'])) {
            throw new ValidationException(['old_password' => Lang::get('rainlab.user::lang.account.invalid_current_pass')]);
        }

        $user->fill($data);
        $user->save();

        /*
         * Password has changed, reauthenticate the user
         */
        if (array_key_exists('password', $data) && strlen($data['password'])) {
            Auth::login($user->reload(), true);
        }

        Flash::success(post('flash', Lang::get('mabaryuk.user::lang.alert.change_password_success')));

        $this->prepareVars();
    }

    /**
     * On Switch User (Talent / Hero)
     *
     * @author Siti Hasuna <sh.hanaaa@gmail.com>
     */
    public function onSwitchUser()
    {
        if (!$user = $this->user()) {
            return;
        }

        $data = post();

        $rules = [
            'type' => 'required|in:talent,hero',
        ];

        $validation = Validator::make(
            $data,
            $rules
        );

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        if ($data['type'] == 'talent' && !$user->is_talent) {
            $user->is_talent = true;
        }

        $user->last_active_as = $data['type'];
        $user->save();

        Flash::success(Lang::get('mabaryuk.main::lang.alert.success'));

        return Redirect::to('myaccount/profile');
    }

    /**
     * On Save CV
     *
     * @author Siti Hasuna <sh.hanaaa@gmail.com>
     */
    public function onSaveCV()
    {
        try {
            DB::beginTransaction();

            if (!$user = $this->user()) {
                return;
            }

            $user = $user->load([
                    'educations', 'certificates',
                    'nonformal_educations', 'organizations',
                    'working_exps', 'working_exps.projects',
                    'lang_skills', 'skills'
                ]);

            /*
             * Validate input
             */
            $data = post();

            $rules = [
                'user.email'                         => 'required|email|between:6,255|unique:users,email,'.$user->id,
                'user.phone'                         => 'required|digits_between:8,14|numeric',
                'user.gender'                        => 'required',
                'user.about'                         => 'required',
                'user.birth_place'                   => 'required',
                'user.birth_date'                    => 'required',
                // 'user.id_card'                       => 'required',
                'user.id_number'                     => 'required',
                'user.address'                       => 'required',
                'user.province_code'                 => 'required',
                'user.city_code'                     => 'required',
                'user.district_code'                 => 'required',
                'user.village_code'                  => 'required',
                'user.domicile_address'              => 'required_if:user.is_address_diff,1',
                'user.domicile_province_code'        => 'required_if:user.is_address_diff,1',
                'user.domicile_city_code'            => 'required_if:user.is_address_diff,1',
                'user.domicile_district_code'        => 'required_if:user.is_address_diff,1',
                'user.domicile_village_code'         => 'required_if:user.is_address_diff,1',
                'user.ref_country_id'                => 'required',
                'user.ref_marital_status_id'         => 'required',
                'educations.ref_education_id.*'      => 'required',
                'educations.institution.*'           => 'required',
                'educations.major.*'                 => 'required',
                'educations.grad_year.*'             => 'required',
                'educations.gpa.*'                   => 'required',
                'skills.skill_id.*'                  => 'required',
                'skills.ref_prof_skill_level_id.*'   => 'required',
                'working_experiences.company_name.*' => 'required',
                'working_experiences.position.*'     => 'required',
                'working_experiences.period.*'       => 'required',
                'working_experiences.description.*'  => 'required',
            ];

            $validation = Validator::make(
                $data,
                $rules
            );

            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            $dataUser          = $data['user'];
            $dataEducations    = $data['educations'];
            // $dataNonformalEdu  = $data['nonformal_educations'];
            $dataCertificates  = $data['certificates'];
            $dataOrganizations = $data['organizations'];
            $dataLangSkills    = $data['lang_skills'];
            $dataSkills        = $data['skills'];
            $dataWorkingExp    = $data['working_experiences'];
            $dataProjects      = $data['projects'];

            /*
             * Save detail user
             */
            if (Input::hasFile('avatar')) {
                $user->avatar = Input::file('avatar');
            }
            $dataUser['is_profile_complete'] = true;
            $dataUser['birth_date'] = Carbon::createFromFormat('d/m/Y', trim($dataUser['birth_date']))->format('Y-m-d');

            if ( empty($dataUser['is_address_diff']) ) {
                $dataUser['is_address_diff']        = false;
                $dataUser['domicile_address']       = null;
                $dataUser['domicile_province_code'] = null;
                $dataUser['domicile_city_code']     = null;
                $dataUser['domicile_district_code'] = null;
                $dataUser['domicile_village_code']  = null;
            }
            foreach ($dataUser as $key => $value) {
                $user->{$key} = $value;
            }
            $user->save();

            /*
             * Save educations
             */
            $educations = [];
            $existIds   = [];
            foreach ($dataEducations['ref_education_id'] as $key => $value) {
                if ($id = $dataEducations['id'][$key]) {
                    $existIds[] = $id;
                    $eduModel = EducationModel::find($id);
                } else {
                    $eduModel = new EducationModel;
                }
                $eduModel->fill([
                    'ref_education_id' => $value,
                    'institution'      => $dataEducations['institution'][$key],
                    'major'            => $dataEducations['major'][$key],
                    'grad_year'        => trim($dataEducations['grad_year'][$key]),
                    'gpa'              => $dataEducations['gpa'][$key],
                ]);
                $educations[] = $eduModel;
            }

            // delete before save new
            $user->educations()
                ->when(!empty($existIds), function($query) use ($existIds) {
                    return $query->whereNotIn('id', $existIds);
                })->delete();

            // save new after delete
            $user->educations()->saveMany($educations);

            /*
             * Save non formal educations
             */
            // $nonformalEdu = [];
            // $existIds     = [];
            // $periods      = [];
            // if (!empty(array_filter($dataNonformalEdu['name'])) ){
            //     foreach ($dataNonformalEdu['name'] as $key => $value) {
            //         $periods = explode('-', $dataNonformalEdu['period'][$key]);
            //         if ($id = $dataNonformalEdu['id'][$key]) {
            //             $existIds[] = $id;
            //             $nonformalEduModel = NonformalEducationModel::find($id);
            //         } else {
            //             $nonformalEduModel = new NonformalEducationModel;
            //         }

            //         $nonformalEduModel->fill([
            //             'name'         => $value,
            //             'institution'  => $dataNonformalEdu['institution'][$key],
            //             'start_period' => trim(@$periods[0]) ? Carbon::createFromFormat('d/m/Y', trim($periods[0]))->format('Y-m-d') : null,
            //             'end_period'   => trim(@$periods[1]) ? Carbon::createFromFormat('d/m/Y', trim($periods[1]))->format('Y-m-d') : null,
            //         ]);
            //         $nonformalEdu[] = $nonformalEduModel;
            //     }

            //     // delete before save new
            //     $user->nonformal_educations()
            //         ->when(!empty($existIds), function($query) use ($existIds) {
            //             return $query->whereNotIn('id', $existIds);
            //         })->delete();

            //     // save new after delete
            //     $user->nonformal_educations()->saveMany($nonformalEdu);
            // }

            /*
             * Save certificates
             */
            $certificates = [];
            $existIds     = [];
            if (!empty(array_filter($dataCertificates['subject'])) ){
                foreach ($dataCertificates['subject'] as $key => $value) {
                    $certModel = null;
                    if ($id = $dataCertificates['id'][$key]) {
                        $existIds[] = $id;
                        $certModel = CertificateModel::find($id);
                    } else {
                        $certModel = new CertificateModel;
                    }

                    $certModel->fill([
                        'subject'       => $value,
                        'serial_number' => $dataCertificates['serial_number'][$key],
                        'issuer'        => $dataCertificates['issuer'][$key],
                        'issue_date'    => trim(@$dataCertificates['issue_date'][$key]) ? Carbon::createFromFormat('d/m/Y', trim($dataCertificates['issue_date'][$key]))->format('Y-m-d') : null,
                    ]);
                    $certificates[$key] = $certModel;

                    // save file document
                    if (Input::hasFile('certificates.doc.'.$key)) {
                        $certModel->doc = Input::file('certificates.doc.'.$key);
                    }
                }

                // delete before save new
                $user->certificates()
                    ->when(!empty($existIds), function($query) use ($existIds) {
                        return $query->whereNotIn('id', $existIds);
                    })->delete();

                // save new after delete
                $user->certificates()->saveMany($certificates);
            }

            /*
             * Save organizations
             */
            $organizations = [];
            $periods       = [];
            $existIds      = [];
            if (!empty(array_filter($dataOrganizations['name'])) ){
                foreach ($dataOrganizations['name'] as $key => $value) {
                    $periods = explode('-', $dataOrganizations['period'][$key]);
                    if ($id = $dataOrganizations['id'][$key]) {
                        $existIds[] = $id;
                        $orgModel = OrganizationModel::find($id);
                    } else {
                        $orgModel = new OrganizationModel;
                    }
                    $orgModel->fill([
                        'name'         => $value,
                        'position'     => $dataOrganizations['position'][$key],
                        'description'  => $dataOrganizations['description'][$key],
                        'start_period' => trim(@$periods[0]) ? Carbon::createFromFormat('d/m/Y', trim($periods[0]))->format('Y-m-d') : null,
                        'end_period'   => trim(@$periods[1]) ? Carbon::createFromFormat('d/m/Y', trim($periods[1]))->format('Y-m-d') : null,
                    ]);
                    $organizations[] = $orgModel;
                }

                // delete before save new
                $user->organizations()
                    ->when(!empty($existIds), function($query) use ($existIds) {
                        return $query->whereNotIn('id', $existIds);
                    })->delete();

                // save new after delete
                $user->organizations()->saveMany($organizations);
            }

            /*
             * Save language skills
             */
            $langSkills = [];
            $existIds   = [];
            if (!empty(array_filter($dataLangSkills['ref_lang_id'])) ){
                foreach ($dataLangSkills['ref_lang_id'] as $key => $value) {
                    if ($id = $dataLangSkills['id'][$key]) {
                        $existIds[] = $id;
                        $langModel = LangModel::find($id);
                    } else {
                        $langModel = new LangModel;
                    }
                    $langModel->fill([
                        'ref_lang_id'       => $value,
                        'ref_lang_level_id' => $dataLangSkills['ref_lang_level_id'][$key]
                    ]);
                    $langSkills[] = $langModel;
                }

                // delete before save new
                $user->lang_skills()
                    ->when(!empty($existIds), function($query) use ($existIds) {
                        return $query->whereNotIn('id', $existIds);
                    })->delete();

                // save new after delete
                $user->lang_skills()->saveMany($langSkills);
            }

            /*
             * Save skills
             */
            $skills   = [];
            $existIds = [];
            if (!empty(array_filter($dataSkills['skill_id'])) ){
                foreach ($dataSkills['skill_id'] as $key => $value) {
                    if ($id = $dataSkills['id'][$key]) {
                        $existIds[] = $id;
                        $userSkillModel = UsersSkillModel::find($id);
                    } else {
                        $userSkillModel = new UsersSkillModel;
                    }
                    $userSkillModel->fill([
                        'skill_id'                => $value,
                        'ref_prof_skill_level_id' => $dataSkills['ref_prof_skill_level_id'][$key]
                    ]);
                    $skills[] = $userSkillModel;
                }

                // delete before save new
                $user->skills()
                    ->when(!empty($existIds), function($query) use ($existIds) {
                        return $query->whereNotIn('id', $existIds);
                    })->delete();

                // save new after delete
                $user->skills()->saveMany($skills);
            }
            /*
             * Save working experiences
             */
            $workingExp    = [];
            $periods       = [];
            $existIds      = [];
            $totalMonthExp = 0;
            if (!empty(array_filter($dataWorkingExp['company_name'])) ){
                foreach ($dataWorkingExp['company_name'] as $key => $value) {
                    $periods = explode('-', $dataWorkingExp['period'][$key]);
                    if ($id = $dataWorkingExp['id'][$key]) {
                        $existIds[] = $id;
                        $weModel = WorkingExperienceModel::find($id);
                    } else {
                        $weModel = new WorkingExperienceModel;
                    }
                    $weModel->fill([
                        'company_name'  => $value,
                        'company_phone' => $dataWorkingExp['company_phone'][$key],
                        'position'      => $dataWorkingExp['position'][$key],
                        'description'   => $dataWorkingExp['description'][$key],
                        'start_period'  => trim(@$periods[0]) ? Carbon::createFromFormat('d/m/Y', trim($periods[0]))->format('Y-m-d') : null,
                        'end_period'    => trim(@$periods[1]) ? Carbon::createFromFormat('d/m/Y', trim($periods[1]))->format('Y-m-d') : null,
                    ]);
                    $workingExp[$key] = $weModel;

                    /*
                    * Increment months experience
                    */
                    $startDate = null;
                    $endDate = null;
                    if ($weModel->start_period) {
                        $startDate = Carbon::parse($weModel->start_period);
                        $endDate   = Carbon::parse($weModel->end_period);
                        $totalMonthExp += $startDate->diffInMonths($endDate);
                    }

                    /*
                    * Save projects of working experiences
                    */
                    $projects        = [];
                    $periods         = [];
                    $existProjectIds = [];
                    if (!empty(array_filter(@$dataProjects[$key]['name']))) {
                        foreach ($dataProjects[$key]['name'] as $keyP => $value) {
                            $periods = explode('-', $dataProjects[$key]['period'][$keyP]);
                            if ($id = $dataProjects[$key]['id'][$keyP]) {
                                $existProjectIds[] = $id;
                                $projectModel = ProjectModel::find($id);
                            } else {
                                $projectModel = new ProjectModel;
                            }
                            $projectModel->fill([
                                'name'         => $value,
                                'description'  => $dataProjects[$key]['description'][$keyP],
                                'start_period' => trim(@$periods[0]) ? Carbon::createFromFormat('d/m/Y', trim($periods[0]))->format('Y-m-d') : null,
                                'end_period'   => trim(@$periods[1]) ? Carbon::createFromFormat('d/m/Y', trim($periods[1]))->format('Y-m-d') : null,
                            ]);
                            $projects[] = $projectModel;
                        }

                        // delete before save new
                        $workingExp[$key]->projects()
                            ->when(!empty($existProjectIds), function($query) use ($existProjectIds) {
                                return $query->whereNotIn('id', $existProjectIds);
                            })->delete();

                        // save new after delete
                        $workingExp[$key]->projects()->saveMany($projects);
                    }
                }

                // delete before save new
                $user->working_exps()
                    ->when(!empty($existIds), function($query) use ($existIds) {
                        return $query->whereNotIn('id', $existIds);
                    })->delete();

                // save new after delete
                $user->working_exps()->saveMany($workingExp);
            }

            $user->months_exp = $totalMonthExp;
            $user->save();

            DB::commit();

            Flash::success(Lang::get('mabaryuk.main::lang.alert.success'));
        }
        catch (Exception $ex) {
            DB::rollback();
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }

    /**
     * On Add Item for append partial only
     *
     * @author Siti Hasuna <sh.hanaaa@gmail.com>
     */
    public function onAddItem()
    {
        $key = post('key');

        $this->page['key'] = $key;
    }

    //
    // AJAX
    //

    /**
     * Sign in the user
     */
    public function onSignin()
    {
        try {
            /*
             * Validate input
             */
            $data = post();
            $rules = [];

            $rules['login'] = $this->loginAttribute() == UserSettings::LOGIN_USERNAME
                ? 'required|between:2,255'
                : 'required|email|between:6,255';

            $rules['password'] = 'required|between:4,255';

            if (!array_key_exists('login', $data)) {
                $data['login'] = post('username', post('email'));
            }

            $data['login'] = trim($data['login']);

            $validation = Validator::make(
                $data,
                $rules,
                $this->getValidatorMessages(),
                $this->getCustomAttributes()
            );

            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            /*
             * Authenticate user
             */
            $credentials = [
                'login'    => array_get($data, 'login'),
                'password' => array_get($data, 'password')
            ];

            Event::fire('rainlab.user.beforeAuthenticate', [$this, $credentials]);

            $user = Auth::authenticate($credentials, $this->useRememberLogin());
            if ($user->isBanned()) {
                Auth::logout();
                throw new AuthException(/*Sorry, this user is currently not activated. Please contact us for further assistance.*/'rainlab.user::lang.account.banned');
            }

            /*
             * Record IP address
             */
            if ($ipAddress = Request::ip()) {
                $user->touchIpAddress($ipAddress);
            }

            /*
             * Redirect
             */
            if ($redirect = $this->makeRedirection(true)) {
                return $redirect;
            } else {
                if ($user->is_admin_company) {
                    if (! $user->company->is_profile_complete) {
                        return Redirect::to('mycompany/company-profile');
                    } else {
                        return Redirect::to('/');
                    }
                } else {
                    return Redirect::to('myaccount/profile');
                }
            }
        }
        catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }
}
