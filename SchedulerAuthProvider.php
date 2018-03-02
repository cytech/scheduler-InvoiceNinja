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

namespace Modules\Schedulerr;

use App\Providers\AuthServiceProvider;

class SchedulerAuthProvider extends AuthServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \Modules\Scheduler\Models\Schedule::class => \Modules\Scheduler\Policies\SchedulePolicy::class,
    ];
}
