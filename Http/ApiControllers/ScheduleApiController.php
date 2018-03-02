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

namespace Modules\Scheduler\Http\ApiControllers;

use App\Http\Controllers\BaseAPIController;
use Modules\Scheduler\Repositories\ScheduleRepository;
use Modules\Scheduler\Http\Requests\ScheduleRequest;
use Modules\Scheduler\Http\Requests\CreateScheduleRequest;
use Modules\Scheduler\Http\Requests\UpdateScheduleRequest;

class ScheduleApiController extends BaseAPIController
{
    protected $ScheduleRepo;
    protected $entityType = 'schedule';

    public function __construct(ScheduleRepository $scheduleRepo)
    {
        parent::__construct();

        $this->scheduleRepo = $scheduleRepo;
    }

    /**
     * @SWG\Get(
     *   path="/schedule",
     *   summary="List schedule",
     *   operationId="listSchedules",
     *   tags={"schedule"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of schedule",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Schedule"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $data = $this->scheduleRepo->all();

        return $this->listResponse($data);
    }

    /**
     * @SWG\Get(
     *   path="/schedule/{schedule_id}",
     *   summary="Individual Schedule",
     *   operationId="getSchedule",
     *   tags={"schedule"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="schedule_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single schedule",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Schedule"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function show(ScheduleRequest $request)
    {
        return $this->itemResponse($request->entity());
    }




    /**
     * @SWG\Post(
     *   path="/schedule",
     *   summary="Create a schedule",
     *   operationId="createSchedule",
     *   tags={"schedule"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="schedule",
     *     @SWG\Schema(ref="#/definitions/Schedule")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New schedule",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Schedule"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function store(CreateScheduleRequest $request)
    {
        $schedule = $this->scheduleRepo->save($request->input());

        return $this->itemResponse($schedule);
    }

    /**
     * @SWG\Put(
     *   path="/schedule/{schedule_id}",
     *   summary="Update a schedule",
     *   operationId="updateSchedule",
     *   tags={"schedule"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="schedule_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="schedule",
     *     @SWG\Schema(ref="#/definitions/Schedule")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated schedule",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Schedule"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function update(UpdateScheduleRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $schedule = $this->scheduleRepo->save($request->input(), $request->entity());

        return $this->itemResponse($schedule);
    }


    /**
     * @SWG\Delete(
     *   path="/schedule/{schedule_id}",
     *   summary="Delete a schedule",
     *   operationId="deleteSchedule",
     *   tags={"schedule"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="schedule_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted schedule",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Schedule"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function destroy(UpdateScheduleRequest $request)
    {
        $schedule = $request->entity();

        $this->scheduleRepo->delete($schedule);

        return $this->itemResponse($schedule);
    }

}
