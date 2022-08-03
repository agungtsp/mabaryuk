<?php namespace MabarYuk\User\Components;

use DB;
use Exception;
use Flash;
use Lang;
use Request;
use MabarYuk\Main\Components\Base as ComponentBase;
use MabarYuk\Job\Models\Applicant as ApplicantModel;

/**
 * UserApplication Component
 */
class UserApplication extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'UserApplication Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $this->page['applications'] = $this->fetch();
    }

    /**
     * List Talent's Applications
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
            $data = ApplicantModel::where('user_id', $user->id)
                ->with(['job', 'job.ref_job_category', 'job.company'])
                ->withCount('views')
                ->whereHas('job', function($query) use ($params) {
                    $query->whereNull('deleted_at');
                })
                ->when(@$params['keyword'], function ($query) use ($params) {
                    return $query->whereHas('job', function($query) use ($params) {
                        $query->where('title', 'like', '%'.$params['keyword'].'%');
                    });
                })
                ->when(@$params['statuses'], function($query) use ($params) {
                    $statusIds = [];

                    if (in_array('interview', $params['statuses'])) {
                        $statusIds[] = 10;
                    }
                    if (in_array('failed', $params['statuses'])) {
                        $statusIds[] = 7;
                        $statusIds[] = 9;
                        $statusIds[] = 12;
                    }
                    if (in_array('accepted', $params['statuses'])) {
                        $statusIds[] = 11;
                    }

                    return $query->where(function($query) use ($params, $statusIds) {
                        if (in_array('norespond', $params['statuses'])) {
                            $query->orHas('views', '=', 0);
                        }
                        if (in_array('seen', $params['statuses'])) {
                            $query->orHas('views', '>', 0);
                        }
                        return $query->when(!empty($statusIds), function ($query) use ($statusIds) {
                            $query->orWhereHas('progresses', function($progress) use ($statusIds) {
                                $progress->where('mabaryuk_job_applicant_progresses.is_active', true);
                                $progress->whereIn('mabaryuk_ref_progress_statuses.id', $statusIds);
                            });
                        });
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate($pageSize);
            return $data;
        }
        catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else Flash::error($ex->getMessage());
        }
    }
}
