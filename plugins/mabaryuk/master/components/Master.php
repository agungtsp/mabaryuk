<?php namespace MabarYuk\Master\Components;

use Cms\Classes\ComponentBase;
use MabarYuk\Master\Models\BankAccount;
use MabarYuk\Master\Models\Country;
use MabarYuk\Master\Models\Industry;
use MabarYuk\Master\Models\Division;
use MabarYuk\Master\Models\JobStatus;
use MabarYuk\Master\Models\JobType;
use MabarYuk\Master\Models\Location;
use MabarYuk\Master\Models\SkillLevel;
use MabarYuk\Master\Models\MaritalStatus;
use MabarYuk\Master\Models\Education;
use MabarYuk\Master\Models\Lang;
use MabarYuk\Master\Models\LangLevel;
use MabarYuk\Master\Models\ProfSkillLevel;
use MabarYuk\Master\Models\PaymentMethod;
use MabarYuk\Master\Models\JobCategory;
use MabarYuk\Master\Models\ProgressStatus;
use MabarYuk\User\Models\Skill;

/**
 * Master Component
 */
class Master extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Master Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    //
    // LIST DATA
    //
    public function countries()
    {
        return Country::get(['id', 'name']);
    }
    
    public function provinces()
    {
        return Location::whereNull('parent_code')->get(['code', 'name']);
    }

    public function skillLevels()
    {
        return SkillLevel::get(['id', 'name']);
    }

    public function industries()
    {
        return Industry::get(['id', 'name']);
    }

    public function divisions()
    {
        return Division::get(['id', 'name']);
    }

    public function jobStatuses()
    {
        return JobStatus::get(['id', 'name']);
    }

    public function maritalStatuses()
    {
        return MaritalStatus::get(['id', 'name']);
    }

    public function educations()
    {
        return Education::get(['id', 'name']);
    }

    public function langs()
    {
        return Lang::get(['id', 'name']);
    }

    public function langLevels()
    {
        return LangLevel::get(['id', 'name']);
    }

    public function profSkillLevels()
    {
        return ProfSkillLevel::get(['id', 'name', 'alias']);
    }

    public function jobCategories()
    {
        return JobCategory::get(['id', 'name']);
    }

    public function progressStatuses()
    {
        return ProgressStatus::get(['id', 'name']);
    }

    public function jobTypes()
    {
        return JobType::get(['id', 'name']);
    }

    public function paymentMethods()
    {
        return PaymentMethod::get(['id', 'name']);
    }

    public function bankAccounts()
    {
        return BankAccount::with(['ref_bank'])->get(['id', 'ref_bank_id', 'acc_name', 'acc_number']);
    }

    public function skills()
    {
        return Skill::get(['id', 'name']);
    }

    //
    // SINGLE DATA
    //


    //
    // AJAX
    //
    public function onSearchLocation()
    {
        $type       = post('type');
        $parentCode = post('code');
        $keyword    = post('term'); 

        $query = Location::query();
        switch ($type) {
            case 'city':
                $query->whereNotNull('parent_code');
                $query->where('parent_code', $parentCode);
                break;

            case 'district':
                $query->whereNotNull('parent_code');
                $query->where('parent_code', $parentCode);
                break;

            case 'village':
                $query->whereNotNull('parent_code');
                $query->where('parent_code', $parentCode);
                break;
            
            default:
                $query->whereNull('parent_code');
                break;
        }

        $query->when($keyword, function ($query) use ($keyword) {
            return $query->where('name', 'like', "%$keyword%");
        });

        $data = $query->lists('name', 'code');

        $this->page['options'] = $data;
        $this->page['defaultText'] = post('defaultText');
        $this->page['selected'] = post('selected');

        return ['results' => $data];
    }

    public function onSearchNationality()
    {
        $keyword    = post('term'); 

        $query = Country::when($keyword, function ($query) use ($keyword) {
            return $query->where('nationality', 'like', "%$keyword%");
        });

        $data = $query->lists('nationality', 'id');

        return ['results' => $data];
    }

    public function onSearchSkill()
    {
        $keyword    = post('term'); 

        $query = Skill::when($keyword, function ($query) use ($keyword) {
            return $query->where('name', 'like', "%$keyword%");
        });

        $data = $query->lists('name', 'id');

        return ['results' => $data];
    }
}
