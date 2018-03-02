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

namespace Modules\Scheduler\Datatables;

use Utils;
use URL;
use Auth;
use App\Ninja\Datatables\EntityDatatable;

class SchedulerDatatable extends EntityDatatable
{
    public $entityType = 'schedule';
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'title',
                function ($model) {
                    return $model->title;
                }
            ],[
                'description',
                function ($model) {
                    return $model->description;
                }
            ],
            [
                'created_at',
                function ($model) {
                    return Utils::fromSqlDateTime($model->created_at);
                }
            ],
        ];
    }

    public function actions()
    {
        return [
            [
                mtrans('schedule', 'edit_schedule'),
                function ($model) {
                    return URL::to("schedule/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('editByOwner', ['schedule', $model->user_id]);
                }
            ],
        ];
    }

}
