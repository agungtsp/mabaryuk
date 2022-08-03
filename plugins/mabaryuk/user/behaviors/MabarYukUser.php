<?php namespace MabarYuk\User\Behaviors;

use Carbon\Carbon;
use ValidationException;
use ApplicationException;
use RainLab\User\Models\User;
use RainLab\User\Models\UserGroup;
use October\Rain\Database\Collection;
use October\Rain\Extension\ExtensionBase;
use MabarYuk\Master\Models\Country;
use MabarYuk\Master\Models\MaritalStatus;

class MabarYukUser extends ExtensionBase
{
    /**
     * @var \October\Rain\Database\Model Reference to the extended model.
     */
    protected $model;

    /**
     * Constructor
     * @param \October\Rain\Database\Model $model The extended model.
     */
    public function __construct($model)
    {
        $this->model = $model;

        $model->addFillable([
            'phone',
            'is_talent',
            'is_address_diff',
            'npwp'
        ]);

        //
        // Set Relations
        //
        $model->hasMany['educations']           = \MabarYuk\User\Models\Education::class;
        $model->hasMany['certificates']         = \MabarYuk\User\Models\Certificate::class;
        $model->hasMany['nonformal_educations'] = \MabarYuk\User\Models\NonformalEducation::class;
        $model->hasMany['organizations']        = \MabarYuk\User\Models\Organization::class;
        $model->hasMany['working_exps']         = \MabarYuk\User\Models\WorkingExperience::class;
        $model->hasMany['skills']               = \MabarYuk\User\Models\UsersSkill::class;
        $model->hasMany['lang_skills']          = \MabarYuk\User\Models\Lang::class;
        $model->hasMany['referrals']            = [\MabarYuk\Job\Models\Applicant::class, 'key' => 'hero_id'];
        $model->hasMany['applications']         = [\MabarYuk\Job\Models\Applicant::class, 'key' => 'user_id', 'conditions' => 'hero_id is null'];
        $model->belongsTo['company']            = 'MabarYuk\Company\Models\Company';
        $model->belongsTo['province']           = [\MabarYuk\Master\Models\Location::class, 'otherKey' => 'code', 'key' => 'province_code', 'conditions' => 'parent_code is null'];
        $model->belongsTo['city']               = [\MabarYuk\Master\Models\Location::class, 'otherKey' => 'code', 'key' => 'city_code', 'conditions' => 'parent_code is not null'];
        $model->belongsTo['district']           = [\MabarYuk\Master\Models\Location::class, 'otherKey' => 'code', 'key' => 'district_code', 'conditions' => 'parent_code is not null'];
        $model->belongsTo['village']            = [\MabarYuk\Master\Models\Location::class, 'otherKey' => 'code', 'key' => 'village_code', 'conditions' => 'parent_code is not null'];
        $model->belongsTo['domicile_province']  = [\MabarYuk\Master\Models\Location::class, 'otherKey' => 'code', 'key' => 'domicile_province_code', 'conditions' => 'parent_code is null'];
        $model->belongsTo['domicile_city']      = [\MabarYuk\Master\Models\Location::class, 'otherKey' => 'code', 'key' => 'domicile_city_code', 'conditions' => 'parent_code is not null'];
        $model->belongsTo['domicile_district']  = [\MabarYuk\Master\Models\Location::class, 'otherKey' => 'code', 'key' => 'domicile_district_code', 'conditions' => 'parent_code is not null'];
        $model->belongsTo['domicile_village']   = [\MabarYuk\Master\Models\Location::class, 'otherKey' => 'code', 'key' => 'domicile_village_code', 'conditions' => 'parent_code is not null'];
        $model->belongsTo['ref_country']        = \MabarYuk\Master\Models\Country::class;
        $model->belongsTo['ref_marital_status'] = \MabarYuk\Master\Models\MaritalStatus::class;
        $model->attachOne['banner']             = 'System\Models\File';
        
        //
        // Set Rules
        //
        $model->rules['email']    = 'between:6,255|email|unique:users';
        $model->rules['username'] = 'between:2,255';
    }

    //
    // SET ATTRIBUTES
    //
    public function setIsTalentAttribute($value)
    {
        if ($value == true) {
            $this->model->attributes['talent_date'] = Carbon::now();
        }
        $this->model->attributes['is_talent'] = $value;
    }

    public function setDomicileProvinceCodeAttribute($value)
    {
        if (!$value) {
            $value = null;
        }
        $this->model->attributes['domicile_province_code'] = $value;
    }

    //
    // GET ATTRIBUTES
    //

    public function getIsHeroAttribute()
    {
        if ($this->model->groups->where('code', 'hero')->isNotEmpty()) {
            return true;
        }
        return false;
    }

    public function getIsAdminCompanyAttribute()
    {
        if ($this->model->groups->where('code', 'admin_company')->isNotEmpty()) {
            return true;
        }
        return false;
    }

    public function getGenderNameAttribute()
    {
        $gender = ['F' => 'Female', 'M' => 'Male'];
        $value = @$gender[$this->model->gender];
        if (!$value) $value = '';

        return $value;
    }

    public function getAgeAttribute()
    {
        $now = Carbon::now();
        return ($this->model->birth_date) ? $now->diffInYears($this->model->birth_date) : 0;
    }

    public function getPermanentLocationAttribute()
    {
        $location = '';
        if ($this->model->province_code) {
            $location .= $this->model->province->name.', ';
        }
        if ($this->model->ref_country_id) {
            $location .= $this->model->ref_country->name;
        }

        return $location;
    }

    public function getDomicileLocationAttribute()
    {
        $location = '';
        if ($this->model->domicile_province_code) {
            $location .= $this->model->domicile_province->name.', ';
        }
        if ($this->model->ref_country_id) {
            $location .= $this->model->ref_country->name;
        }

        return $location ? $location : $this->model->permanent_location;
    }

    /**
     * Get years experience
     *
     * @param  string  $value
     * @return string
     */
    public function getYearsExpAttribute($value)
    {
        $monthsExp = $this->model->months_exp ?? 0;
        $value = $monthsExp / 12; 
        if (($monthsExp % 12) > 0) {
            $value = round($value, 1);
        }
        return $value;
    }

    public function getRefCountryIdOptions($value, $formData)
    {
        $updateId = $formData->id;
        return Country::get(['id', 'name'])->pluck('name', 'id');
    }

    public function getRefMaritalStatusIdOptions($value, $formData)
    {
        $updateId = $formData->id;
        return MaritalStatus::get(['id', 'name'])->pluck('name', 'id');
    }
}
