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


namespace Modules\Scheduler\Http\Controllers;

use Addons\Workorders\Events\WorkorderModified;
use FI\Modules\Clients\Models\Client;
use Addons\Workorders\Models\WorkorderItem;
use Addons\Workorders\Models\Workorder;
use Addons\Workorders\Validators\WorkorderValidator;
use Addons\Workorders\Validators\ItemValidator;
use Addons\Workorders\Support\DateFormatter;
use Addons\Workorders\Models\Employee;
use Addons\Workorders\Models\Resource;

class WorkorderController extends Controller
{
    public function __construct(WorkorderValidator $workorderValidator, ItemValidator $itemValidator)
    {
        $this->workorderValidator = $workorderValidator;
        $this->itemValidator = $itemValidator;
    }

    public function create()
    {
        $request = request()->all();
        $request['workorder_date'] = date('Y-m-d');
        $validator = $this->workorderValidator->getValidator($request);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $input = request()->except('client_name', 'workers', 'resources');
        $input['client_id'] = Client::firstOrCreateByUniqueName(request('client_name'))->id;
        $input['start_time'] = DateFormatter::formattime($input['start_time']);
        $input['end_time'] = DateFormatter::formattime($input['end_time']);

        $workorder = Workorder::create($input);

        $input = request()->only('workers', 'resources');
        // Now let's add some employee items to that new workorder.
        if (isset($input['workers'])) {
            foreach ($input['workers'] as $val) {
                $lookupItem = Employee::where('id', '=', $val)->firstOrFail();
                $item['workorder_id'] = $workorder->id;
                $item['resource_table'] = 'employees';
                $item['resource_id'] = $lookupItem->id;
                $item['name'] = $lookupItem->short_name;
                $item['description'] = $lookupItem->title . "-" . $lookupItem->number;
                $item['quantity'] = 0;
                $item['price'] = $lookupItem->billing_rate;

                $itemValidator = $this->itemValidator->getApiWorkorderValidator($item);

                if ($itemValidator->fails()) {
                    return response()->json(['errors' => $itemValidator->errors()], 400);
                }

                WorkorderItem::create($item);
            }
        }
        // Now let's add some resource items to that new workorder.
        if (isset($input['resources'])) {
            foreach ($input['resources'] as $val) {
                $lookupItem = Resource::where('id', '=', $val)->firstOrFail();
                $item['workorder_id'] = $workorder->id;
                $item['resource_table'] = 'resources';
                $item['resource_id'] = $lookupItem->id;
                $item['name'] = $lookupItem->name;
                $item['description'] = $lookupItem->description;
                $item['quantity'] = 0;
                $item['price'] = $lookupItem->cost;

                $itemValidator = $this->itemValidator->getApiWorkorderValidator($item);

                if ($itemValidator->fails()) {
                    return response()->json(['errors' => $itemValidator->errors()], 400);
                }

                WorkorderItem::create($item);
            }
        }

        event(new WorkorderModified(Workorder::find($workorder->id)));

        return redirect()->route('workorders.edit', ['id' => $workorder->id])->with('message', 'Successfully Created workorder!');
    }
}