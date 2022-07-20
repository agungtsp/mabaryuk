<?php namespace MabarYuk\User\Components;

use DB;
use Exception;
use Flash;
use Lang;
use Request;
use MabarYuk\Main\Components\Base as ComponentBase;
use MabarYuk\Job\Models\Applicant as ApplicantModel;

/**
 * UserTalent Component
 */
class UserTalent extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'UserTalent Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $this->page['talents'] = $this->fetch();
        $this->page['job_positions'] = $this->allJobPositions();
    }

    /**
     * List Hero's Referrals / Talents
     *
     * @author Siti Hasuna <sh.hanaaa@gmail.com>
     */
    public function fetch()
    {
        try {
            if (!$user = $this->user()) {
                return;
            }

            $params   = get();
            $pageSize = 10;

            /*
            * SET ORDERS
            */
            $orders = [];
            if ($filterDates = @$params['dates']) {
                $orderCol = 'mabaryuk_job_applicants.created_at';
                $orderSort = '';
                if (in_array('asc', $filterDates)) {
                    $orderSort = 'asc';
                }
                if (in_array('desc', $filterDates)) {
                    $orderSort = 'desc';
                }
                if (in_array('all', $filterDates)) {
                    $orderSort = '';
                }
                if ($orderSort) {
                    $orders[$orderCol] = $orderSort;
                }
            }

            /*
            * MAIN QUERY 
            */
            $query = ApplicantModel::where('hero_id', $user->id)
                ->with(['job', 'job.ref_job_category', 'job.ref_skill_level', 'job.company', 'job.billing'])
                ->whereHas('job', function($query) use ($params) {
                    $query->whereNull('deleted_at');
                })
                ->when($params, function ($query) use ($params) {
                    return $query->where(function($query) use ($params) {
                        $query->when(@$params['keyword'], function ($query) use ($params) {
                            return $query->orWhere('name', 'like', '%'.$params['keyword'].'%');
                        })
                        ->when(@$params['statuses'], function($query) use ($params) {
                            return $query->orWhereHas('progresses', function($progress) use ($params) {
                                $progress->where('mabaryuk_job_applicant_progresses.is_active', true);
                                $progress->whereIn('mabaryuk_ref_progress_statuses.id', $params['statuses']);
                            });
                        })
                        ->when(@$params['positions'], function($query) use ($params) {
                            return $query->orWhereIn('job_id', $params['positions']);
                        });
                    });
                });

            if (!empty($orders)) {
                foreach ($orders as $col => $sort) {
                    $query = $query->orderBy($col, $sort);
                }
            } else {
                $query = $query->orderBy('created_at', 'desc');
            }

            $data = $query->select('mabaryuk_job_applicants.*')->paginate($pageSize);
            return $data;
        }
        catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }

    /**
     * All job positions for filter list
     *
     * @author Siti Hasuna <sh.hanaaa@gmail.com>
     */
    public function allJobPositions()
    {
        if (!$user = $this->user()) {
            return;
        }

        $data = ApplicantModel::where('hero_id', $user->id)
            ->with(['job', 'job.ref_skill_level'])
            ->distinct('job_id')
            ->get(['job_id']);

        $positions = $data->map(function($value) {
            $positionTitle = '';
            if ($value->job) {
                $skillLevel = $value->job->ref_skill_level;
                $skillLevelName = $skillLevel ? $skillLevel->name : null;
                $positionTitle = $skillLevelName .' '. $value->job->title;
            }
            return [
                'id' => $value->job_id,
                'title' => $positionTitle
            ];
        });

        return $positions;
    }
}
