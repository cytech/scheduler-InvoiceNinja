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

namespace Modules\Scheduler\Transformers;

use Modules\Scheduler\Models\Schedule;
use App\Ninja\Transformers\EntityTransformer;

/**
 * @SWG\Definition(definition="Schedule", @SWG\Xml(name="Schedule"))
 */

class ScheduleTransformer extends EntityTransformer
{
    /**
    * @SWG\Property(property="id", type="integer", example=1, readOnly=true)
    * @SWG\Property(property="user_id", type="integer", example=1)
    * @SWG\Property(property="account_key", type="string", example="123456")
    * @SWG\Property(property="updated_at", type="integer", example=1451160233, readOnly=true)
    * @SWG\Property(property="archived_at", type="integer", example=1451160233, readOnly=true)
    */

    /**
     * @param Schedule $schedule
     * @return array
     */
    public function transform(Schedule $schedule)
    {
        return array_merge($this->getDefaults($schedule), [
            'title' => $schedule->title,
            'description' => $schedule->description,
            'id' => (int) $schedule->public_id,
            'updated_at' => $this->getTimestamp($schedule->updated_at),
            'archived_at' => $this->getTimestamp($schedule->deleted_at),
        ]);
    }
}
