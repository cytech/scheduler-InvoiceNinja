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

namespace Modules\Scheduler\Presenters;

use App\Ninja\Presenters\EntityPresenter;

class SchedulePresenter extends EntityPresenter
{
	/*public function start_date()
	{
		$date = $this->model->start_date;

		return ($date instanceof \Carbon\Carbon)
			? $date->format('Y-m-d\TH:i')
			: str_replace(' ','T',substr($u->created_at, 0, 16));
	}*/

}
