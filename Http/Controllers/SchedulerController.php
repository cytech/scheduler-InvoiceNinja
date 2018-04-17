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

use Modules\Scheduler\Http\Requests\ReportRequest;
//use Modules\Workorders\Models\Employee;
//use Modules\Workorders\Models\Resource;
use Modules\Scheduler\Models\Schedule;
use Modules\Scheduler\Models\ScheduleReminder;
use Modules\Scheduler\Models\ScheduleOccurrence;
use Modules\Scheduler\Models\ScheduleResource;
use Modules\Scheduler\Models\Category;
use Modules\Scheduler\Models\Setting;
use Recurr;
use Recurr\Transformer;
use Recurr\Exception;
use Carbon\Carbon;
use DB;
use Auth;
use Session;
use Response;
use Illuminate\Http\Request;

use DateTimeImmutable;
//for FusionInvoice
//use app\Modules\CompanyProfiles\Models\CompanyProfile;
use Modules\Scheduler\Http\Requests\EventRequest;
//for coreevents
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Project;
use App\Models\Task;

class SchedulerController extends Controller
{
    public function index()
    {
        $today = new Carbon();

	    $data['monthEvent'] = Schedule::withOccurrences()->where( 'schedule_occurrences.start_date', '>=', $today->copy()->modify( '0:00 first day of this month' ) )
	                                  ->where( 'schedule_occurrences.start_date', '<=', $today->copy()->modify( '23:59:59 last day of this month' ) )
	                                  ->count();
// alternate eloquent way...
//		$data['monthEvent'] = Schedule::whereHas('occurrences',function($q) use($today){
//			$q->where( 'start_date', '>=', $today->copy()->modify( '0:00 first day of this month' ) )
//			  ->where( 'schedule_occurrences.start_date', '<=', $today->copy()->modify( '23:59:59 last day of this month' ) );
//			})->count();

	    $data['lastMonthEvent'] = Schedule::withOccurrences()->where( 'schedule_occurrences.start_date', '>=', $today->copy()->modify( '0:00 first day of last month' ) )
	                                      ->where( 'schedule_occurrences.start_date', '<=', $today->copy()->modify( '23:59:59 last day of last month' ) )
	                                      ->count();

	    $data['nextMonthEvent'] = Schedule::withOccurrences()->where( 'schedule_occurrences.start_date', '>=', $today->copy()->modify( '0:00 first day of next month' ) )
	                                      ->where( 'schedule_occurrences.start_date', '<=', $today->copy()->modify( '23:59:59 last day of next month' ) )
	                                      ->count();

	    $data['fullMonthEvent'] = Schedule::withOccurrences()->select( DB::raw( "count('id') as total,schedule_occurrences.start_date" ) )
	                                      ->where( 'schedule_occurrences.start_date', '>=', date( 'Y-m-01' ) )
	                                      ->where( 'schedule_occurrences.start_date', '<=', date( 'Y-m-t' ) )
	                                      ->groupBy( DB::raw( "DATE_FORMAT(schedule_occurrences.start_date, '%Y%m%d')" ) )
	                                      ->get();

	    $data['fullYearMonthEvent'] = Schedule::withOccurrences()->select( DB::raw( "count('id') as total,schedule_occurrences.start_date" ) )
	                                          ->where( 'schedule_occurrences.start_date', '>=', date( 'Y-01-01' ) )
	                                          ->where( 'schedule_occurrences.start_date', '<=', date( 'Y-12-31' ) )
	                                          ->groupBy( DB::raw( "DATE_FORMAT(schedule_occurrences.start_date, '%Y%m')" ) )
	                                          ->get();

	    $data['reminders'] = ScheduleReminder::with('Schedule','Schedule.occurrences')->where('reminder_date','>=',$today->copy()->modify('0:00'))->get();
	    // laravel 5.3+ $data['reminders'] = ScheduleReminder::whereHas('schedule')->where( 'reminder_date', '>=', $today->copy()->modify( '0:00' ) )->get();

        return view('Scheduler::schedule.dashboard', $data);
    }

    public function calendar()
    {
        //only fetch back configured amount of days
/*        $data['events'] = Schedule::with('occurrence','ScheduleResource')->whereDate('start_date', '>=', Carbon::now()->subDays(config('schedule_settings.addonSchedulerPastdays')))
            ->get();*/

        $data['status'] = (request('status')) ?: 'now';

        $data['events'] = Schedule::withOccurrences()->with('resources')->whereDate('start_date', '>=',
                            Carbon::now()->subDays(config('schedule_settings.pastdays')))->get();//->last();
        $data['categories'] = Category::pluck('name','id');
        $data['catbglist'] = Category::pluck('bg_color','id');
	    $data['cattxlist'] = Category::pluck('text_color','id');
	    //for FusionInvoice
        //$data['companyProfiles'] = CompanyProfile::getList();
	    //for InvoiceNinja
	    $data['companyProfiles'] = ['Default'];

	    //retrieve configured coreevents
        $coreevents = [];
        $filter = request()->filter ?: (new Setting())->coreeventsEnabled();

        $coredata = [
            ENTITY_QUOTE => Invoice::scope()->quotes(),
            ENTITY_INVOICE => Invoice::scope()->invoices(),
            ENTITY_PAYMENT => Payment::scope()->with(['invoice']),
            ENTITY_EXPENSE => Expense::scope()->with(['expense_category']),
            ENTITY_PROJECT => Project::scope(),
            ENTITY_TASK => Task::scope()->with(['project']),
        ];

        foreach ($coredata as $type => $source) {
            if (! count($filter) || in_array($type, $filter)) {
                $source->where(function($query) use ($type) {
                    $start = Carbon::now()->subDays(config('schedule_settings.pastdays'));
                    $end = Carbon::now()->addCentury();//really.....
                    return $query->dateRange($start, $end);
                });

                foreach ($source->with(['account', 'client.contacts'])->get() as $entity) {
                    if ($entity->client && $entity->client->trashed()) {
                        continue;
                    }

                    $subColors = count($filter) == 1;
                    $coreevents[] = $entity->present()->calendarEvent($subColors);
                    $coreevents[count($coreevents) - 1]->category_id =  (Category::where('name', $type)->value('id'));
                }
            }
        }

        $data['coreevents'] = $coreevents;

        return view('Scheduler::schedule.calendar', $data);
    }

	//event create or edit
	public function editEvent( $id = null ) {
		if ( $id ) { //if edit route called with id parameter
			$data = [
				'schedule'   => Schedule::withOccurrences()->find( $id ),
				'categories' => Category::pluck( 'name', 'id' ),
				'url'        => 'schedule\edit_event',
				'title'      => 'update_event',
				'message'    => 'event_updated'
			];

			return view('Scheduler::schedule.eventEdit', $data );

		} else {// no id - create new
			$schedule = new Schedule();
			$data = [
				'schedule'   => $schedule,
				'url'        => 'schedule\edit_event',
				'title'      => 'create_event',
				'message'    => 'event_created',
				'categories' => Category::pluck( 'name', 'id' )
			];
			//defaults
			$schedule['category_id'] = 3;
			$schedule['start_date'] = Date( 'Y-m-d' ) . ' 08:00';
			$schedule['end_date'] = Date( 'Y-m-d' ) . ' 16:00';

			return view('Scheduler::schedule.eventEdit', $data );
		}
	}

	//event store or update
	public function updateEvent( EventRequest $request ) {
		$event = ($request->id) ? Schedule::find( $request->id ) : new Schedule();

		$event->title       = $request->title;
		$event->description = $request->description;
		$event->category_id = $request->category_id;
		$event->user_id     = Auth::user()->id;
		//for InvoiceNinja
		$event->account_id  = Auth::user()->account->id;
		$event->save();

		$occurrence = ($request->id) ? ScheduleOccurrence::find( $request->oid ) : new ScheduleOccurrence();

		$occurrence->schedule_id   = $event->id;
		$occurrence->start_date = $request->start_date.':00';
		$occurrence->end_date   = $request->end_date.':00';
		$occurrence->save();

		//delete existing resources for the event
		ScheduleResource::where('schedule_id', '=', $event->id)->delete();

		if ( $request->category_id == 3 ) { //if client appointment
			if ( ! empty( config( 'workorder_settings.version' ) ) ) {//check if workorder addon is installed
				$employee = Employee::where( 'short_name', '=', $request->title )->where( 'active', 1 )->first();
				if ($employee && $employee->schedule == 1) { //employee exists and is scheduleable...
					$scheduleItem = ScheduleResource::firstOrNew(['id' => $event->id]);
					$scheduleItem->id = $event->id;
					$scheduleItem->schedule_id = $event->id;
					$scheduleItem->fid = 2;
					$scheduleItem->resource_table = 'employees';
					$scheduleItem->resource_id = $employee->id;
					$scheduleItem->value = null;
					$scheduleItem->qty = 1;
					$scheduleItem->save();
				}
			}
		}

		if ( $request->id ) {
			ScheduleReminder::where( 'schedule_id', $request->id )->forceDelete();
		}
		if ( $request->reminder_date && is_array( $request->reminder_date ) && ! empty( $request->reminder_date ) ) {

			for ( $i = 0; $i <= count( $request->reminder_date ) - 1; $i ++ ) {
				$reminder                    = new ScheduleReminder();
				$reminder->schedule_id       = $event->id;
				$reminder->reminder_date     = $request->reminder_date[ $i ].':00';
				$reminder->reminder_location = $request->reminder_location[ $i ];
				$reminder->reminder_text     = $request->reminder_text[ $i ];
				$reminder->save();
			}
		}

		//retrieve for fullcalendar render after create
		$catinfo    = Category::where( 'id', '=', $event->category_id )->first();
		$text_color = $catinfo->text_color;
		$bg_color   = $catinfo->bg_color;

		$response = [
			'type'       => 'success',
			'data'       => $event->id,
			'dataoid'    => $occurrence->oid,
			'text_color' => $text_color,
			'bg_color'   => $bg_color,
		];

		return Response::json( $response );


	}

    public function tableEvent()
    {
        //$data['events'] = Schedule::with('category')->orderBy('start_date', 'desc')->paginate(500);
        $data['events'] = Schedule::withOccurrences()->
                        with('category')->where('isRecurring', '<>', '1')->orderBy('start_date', 'desc')->get();//paginate(500);
        return view('Scheduler::schedule.tableEvent', $data);
    }

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws Exception\InvalidRRule
	 */
	public function tableRecurringEvent(Request $request)
    {
	        //require_once __DIR__ . '/../vendor/autoload.php';
            $data['events'] = Schedule::where('isRecurring',1)->
            with('category')->get();//->paginate(500);

            //add human readable rule to array
            foreach ($data['events'] as $i => $event) {
                $rule = new Recurr\Rule($event->rrule, new \DateTime());
                $textTransformer = new Recurr\Transformer\TextTransformer();
                $data['events'][$i]->textTrans = $textTransformer->transform($rule);
            }

            return view('Scheduler::schedule.tableRecurringEvent', $data);
    }

	/**
	 * @param null $id
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @throws Exception\InvalidRRule
	 */
	//recurring event create or edit
	public function editRecurringEvent( $id = null ) {
		if ( $id ) { //if edit route called with id parameter

			$schedule = Schedule::withOccurrences()->find( $id );
			$rule     = Recurr\Rule::createFromString( $schedule->rrule);
			//$rule = new Recurr\Rule($schedule->rrule);
			$textTransformer = new Recurr\Transformer\TextTransformer();

			$rrule = [
				"frequency"  => $rule->getString(),
				"freqtext"   => $textTransformer->transform( $rule ),
				"freq"       => $rule->getFreqAsText(),
				"start_date" => $rule->getStartDate()->format( 'Y-m-d H:i' ),
				"end_date"   => $rule->getEndDate()->format( 'Y-m-d H:i' ),
				"until"      => ($rule->getUntil())?$rule->getUntil()->format( 'Y-m-d H:i' ):'',
				"count"      => $rule->getCount(),
				"interval"   => $rule->getInterval(),
				"wkst"       => $rule->getWeekStart(),
				"byday"      => $rule->getByDay(),
				"bysetpos"   => $rule->getBySetPosition(),
				"bymonthday" => $rule->getByMonthDay(),
				"byyearday"  => $rule->getByYearDay(),
				"byweekno"   => $rule->getByWeekNumber(),
				"bymonth"    => $rule->getByMonth(),
			];

			$data = [
				'schedule'   => $schedule,
				'categories' => Category::pluck( 'name', 'id' ),
				'url'        => 'schedule\edit_event',
				'title'      => 'update_recurring_event',
				'message'    => 'recurring_event_updated',
				'rrule'      => $rrule,
			];

			return view('Scheduler::schedule.recurringEventEdit', $data );

		} else {// no id - create new
			$schedule = new Schedule();
			$data     = [
				'schedule'   => $schedule,
				'rrule'      => [
					"freq"       => 'WEEKLY',
					"start_date" => Date( 'Y-m-d' ) . ' 08:00',
					"end_date"   => Date( 'Y-m-d' ) . ' 16:00',
					"wkst"       => 'MO',
				],
				'url'        => 'schedule\edit_event',
				'title'      => 'create_recurring_event',
				'message'    => 'recurring_event_created',
				'categories' => Category::pluck( 'name', 'id' )
			];
			//defaults
			$schedule['category_id'] = 3;

			return view('Scheduler::schedule.recurringEventEdit', $data );
		}
	}

	/**
	 * @param EventRequest $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws Exception\InvalidRRule
	 * @throws Exception\InvalidWeekday
	 */
	//recurring event store or update
	public function updateRecurringEvent( EventRequest $request ) {
		//generate rrule
		$allfields = $request->all();

		//remap start and end to RRULE types
		$allfields['DTSTART'] = $allfields['start_date'];
		$allfields['DTEND']   = $allfields['end_date'];
		unset( $allfields['start_date'] );
		unset( $allfields['end_date'] );
		$allfields = array_change_key_case( $allfields, CASE_UPPER );
		//clear all empty
		$allfields = array_filter( $allfields );

		$timezone = 'America/Phoenix';

		$rule            = Recurr\Rule::createFromArray( $allfields );
		$transformer     = new Recurr\Transformer\ArrayTransformer();
		$textTransformer = new Recurr\Transformer\TextTransformer();
		$recurrences     = $transformer->transform( $rule );

		$event = ( $request->id ) ? Schedule::find( $request->id ) : new Schedule();

		$event->title       = $request->title;
		$event->description = $request->description;
		$event->isRecurring = 1;
		$event->rrule       = $rule->getString();
		//$event->start_date  = $rule->getStartDate();
		//$event->end_date    = $rule->getEndDate();
		$event->category_id = $request->category_id;
		$event->user_id     = Auth::user()->id;
		//for InvoiceNinja
		$event->account_id  = Auth::user()->account->id;
		$event->save();

		$event->occurrences()->delete();
		foreach ( $recurrences as $index => $item ) {
			$occurrence             = new ScheduleOccurrence();
			$occurrence->schedule_id   = $event->id;
			$occurrence->start_date = $item->getStart();
			$occurrence->end_date   = $item->getEnd();
			$occurrence->save();
		}

		//delete existing resources for the event
		ScheduleResource::where('schedule_id', '=', $event->id)->delete();

		if ( $request->category_id == 3 ) { //if client appointment
			if ( ! empty( config( 'workorder_settings.version' ) ) ) {//check if workorder addon is installed
				$employee = Employee::where( 'short_name', '=', $request->title )->where( 'active', 1 )->first();
				if ($employee && $employee->schedule == 1) { //employee exists and is scheduleable...
					$scheduleItem = ScheduleResource::firstOrNew(['id' => $event->id]);
					$scheduleItem->id = $event->id;
					$scheduleItem->schedule_id = $event->id;
					$scheduleItem->fid = 2;
					$scheduleItem->resource_table = 'employees';
					$scheduleItem->resource_id = $employee->id;
					$scheduleItem->value = null;
					$scheduleItem->qty = 1;
					$scheduleItem->save();
				}
			}
		}

		if ( $request->id ) {

			ScheduleReminder::where( 'schedule_id', $request->id )->forceDelete();
		}
		if ( $request->reminder_date && is_array( $request->reminder_date ) && ! empty( $request->reminder_date ) ) {

			for ( $i = 0; $i <= count( $request->reminder_date ) - 1; $i ++ ) {

				$reminder                    = new ScheduleReminder();
				$reminder->schedule_id       = $event->id;
				$reminder->reminder_date     = $request->reminder_date[ $i ].':00';
				$reminder->reminder_location = $request->reminder_location[ $i ];
				$reminder->reminder_text     = $request->reminder_text[ $i ];
				$reminder->save();
			}
		}

		//retrieve for fullcalendar render after create
		$catinfo    = Category::where( 'id', '=', $event->category_id )->first();
		$text_color = $catinfo->text_color;
		$bg_color   = $catinfo->bg_color;

		$response = [
			'type'       => 'success',
			'data'       => $event->id,
			'dataoid'    => $occurrence->oid,
			'text_color' => $text_color,
			'bg_color'   => $bg_color,
		];

		return Response::json( $response );


	}

    public function tableReport(ReportRequest $request)
    {
	    if ( $request->isMethod( 'post' ) ) {
		    $data['events'] = Schedule::withOccurrences()->where( 'start_date', '>=', $request->start )
		                              ->where( 'start_date', '<=', $request->end )
		                              ->get();
		    if ( $data['events']->isEmpty() ) {
			    Session::flash( 'error', 'No events found with specified dates' );
			    return back();
		    }

		    return view('Scheduler::schedule.tableEvent', $data );

	    } else {

		    return view('Scheduler::schedule.tableReportView' );
	    }
    }

    public function calendarReport(ReportRequest $request)
    {
	    if ( $request->isMethod( 'post' ) ) {
		    $data['events'] = Schedule::withOccurrences()->with( 'resources' )->where( 'start_date', '>=', $request->start )
		                              ->where( 'start_date', '<=', $request->end )
		                              ->get();
		    if ( $data['events']->isEmpty() ) {
			    Session::flash( 'error', 'No events found with specified dates' );
			    return back();
		    }
		    $data['categories'] = Category::pluck('name','id');
		    $data['catbglist'] = Category::pluck('bg_color','id');
		    $data['cattxlist'] = Category::pluck('text_color','id');
		    //for FusionInvoice
		    //$data['companyProfiles'] = CompanyProfile::getList();
		    //for InvoiceNinja
		    $data['companyProfiles'] = ['Default'];
		    $data['status']       = '';

		    return view('Scheduler::schedule.calendar', $data );

	    } else {
		    return view('Scheduler::schedule.calendarReportView' );
	    }
    }

	public function Settings( Request $request ) {
		if ( $request->isMethod( 'post' ) ) {

			foreach ( $request->only( 'Pastdays', 'EventLimit', 'CreateWorkorder', 'Version',
								'FcThemeSystem', 'FcAspectRatio', 'TimeStep', 'enabledCoreEvents' ) as $key => $value ) {
				//TODO when workorder module is complete
				if ( $key == 'CreateWorkorder' && $value == 1 ) {
					if ( empty( config( 'workorder_settings.version' ) ) ) {
						return redirect()->route('scheduler.settings')
							->with( 'error', trans( 'Scheduler::texts.NoWorkorder' ) );
					}
				}
				$setting  = Setting::firstOrNew( [ 'setting_key' => $key ] );

				if ($key == 'enabledCoreEvents'){
                    $setting->setting_value = !empty($request->enabledCoreEvents) ? array_sum($request->enabledCoreEvents) : 0;
                } else {
                    $setting->setting_value = $value;
                }
				$setting->save();
			}

			return redirect()->route('scheduler.settings')
			                 ->with( 'alertSuccess', trans( 'Scheduler::texts.updated_settings' ) );
		} else {

			return view('Scheduler::schedule.settings' );
		}
	}

    public function scheduledResources($date)
    {
        $scheduled_resources = Schedule::withOccurrences()->with('resources')->whereDate('start_date','=', $date)->get();
        $drivers =  Employee::where('active','=','1')->where('driver','=', 1)->pluck('id','short_name')->toArray();
        //active, scheduleable employees
        $active_employees = Employee::where('active','=','1')->where('schedule', '=', '1')->pluck('short_name','id')->toArray();
        //$active_resources = Resource::where('active','=','1')->pluck('name','id')->toArray();
        $active_resources = Resource::where('active','=','1')->get(['id','name','numstock'])->toArray();

		$scheduled_clients   = [];
		$scheduled_employees = [];
		$scheduled_equipment = [];

        foreach ($scheduled_resources as $item){
            if ($item->resource_table == 'employees') {
                $scheduled_clients[$item->resource_id] = $item->title;//client appointments
            }
            foreach ($item->resources as $resitem) {
                if ($resitem->resource_table == 'employees') {
                    $scheduled_employees[$resitem->resource_id] = strip_tags($resitem->value);//employees from schedule_resources
                } else if ($resitem->resource_table == 'resources') {
                    if(!isset($scheduled_equipment[$resitem->resource_id])) {
                        $scheduled_equipment[$resitem->resource_id]['id'] = $resitem->resource_id;
                        $scheduled_equipment[$resitem->resource_id]['name'] = $resitem->value;//resources from schedule_resources
                        $scheduled_equipment[$resitem->resource_id]['numstock'] = $resitem->qty;
                    }else{
                        $scheduled_equipment[$resitem->resource_id]['numstock'] += $resitem->qty;
                    }
                }
            }
        }

        //merge client appointments and scheduled_employees
        $scheduled_all = $scheduled_clients + $scheduled_employees;

        // build array of AVAILABLE workers
        if (isset($scheduled_all)) {
            $available_employees = array_diff_key($active_employees, $scheduled_all);
        } else {
            $available_employees = $active_employees;
        }
        //check if drivers in list and color blue
        foreach ($available_employees as $key => $value){
            if (in_array($key,$drivers)){
                //prepending __D to indicate driver - parsed in jquery to change color
                $available_employees[$key] = '___D'.$value;
            }
        }

        // build array of AVAILABLE resources
        if (isset($scheduled_equipment)) {
            //check if scheduled resource is availalble against resource numstock
            $scheduled_instock = array();
            foreach ($scheduled_equipment as $equip){
                foreach ($active_resources as $active){
                    if ($equip['id'] == $active['id']){
                        if($equip['numstock'] < $active['numstock']){
                            array_push($scheduled_instock,$equip);
                        }
                    }
                }
            }
            //remove equipment that is not in stock
            $scheduled_equipment = array_udiff($scheduled_equipment,$scheduled_instock, function ($a, $b){return $b['id'] - $a['id'] ;});
            //remove unavailable resource
            $available_resources = array_udiff($active_resources, $scheduled_equipment, function ($a, $b){return $b['id'] - $a['id'] ;});
        } else {
            $available_resources = $active_resources;
        }

        return response()->json(['success' => true, 'available_employees' => $available_employees,'available_resources' => $available_resources], 200);
    }

	/**
	 * @param EventRequest $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws Exception\InvalidRRule
	 */
	public function getHuman( EventRequest $request ) {
		//get human readable rule from dialog
		//generate rrule
		$allfields = $request->all();
		$allfields = array_change_key_case( $allfields, CASE_UPPER );
		//clear all empty
		$allfields = array_filter( $allfields );

		$timezone = 'America/Phoenix';

		$rule            = Recurr\Rule::createFromArray( $allfields );
		$textTransformer = new Recurr\Transformer\TextTransformer();
		$textTrans       = $textTransformer->transform( $rule );

		$response['type']   = 'success';
		$response['result'] = $textTrans;

		return Response::json( $response );
	}

}
