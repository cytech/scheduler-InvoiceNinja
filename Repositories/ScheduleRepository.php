<?php
/**
 * *
 *  * This file is part of Schedule Addon for InvoiceNinja.
 *  * (c) Cytech <cytech@cytech-eng.com>
 *  *
 *  * For the full copyright and license information, please view the LICENSE
 *  * file that was distributed with this source code.
 *
 *
 */

namespace Modules\Scheduler\Repositories;

use DB;
use Modules\Scheduler\Models\Schedule;
use App\Ninja\Repositories\BaseRepository;
//use App\Events\ScheduleWasCreated;
//use App\Events\ScheduleWasUpdated;

class ScheduleRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'Modules\Scheduler\Models\Schedule';
    }

    public function all()
    {
        return Schedule::scope()
                ->orderBy('created_at', 'desc')
                ->withTrashed();
    }

    public function find($filter = null, $userId = false)
    {
        $query = DB::table('schedule')
                    ->where('schedule.account_id', '=', \Auth::user()->account_id)
                    ->select(
                        'schedule.title', 'schedule.description', 
                        'schedule.public_id',
                        'schedule.deleted_at',
                        'schedule.created_at',
                        'schedule.is_deleted',
                        'schedule.user_id'
                    );

        $this->applyFilters($query, 'schedule');

        if ($userId) {
            $query->where('clients.user_id', '=', $userId);
        }

        /*
        if ($filter) {
            $query->where();
        }
        */

        return $query;
    }

    public function save($data, $schedule = null)
    {
        $entity = $schedule ?: Schedule::createNew();

        $entity->fill($data);
        $entity->save();

        /*
        if (!$publicId || $publicId == '-1') {
            event(new ClientWasCreated($client));
        } else {
            event(new ClientWasUpdated($client));
        }
        */

        return $entity;
    }

}
