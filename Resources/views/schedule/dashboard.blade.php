@extends('Scheduler::partials._master')

@section('content')
    <div class="container col-lg-12">
        <br>
        <nav class="navbar navbar-default ">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand mb-0" href="#">{{ trans('Scheduler::texts.schedule_dashboard') }}</a>
                </div>
                <ul class="nav navbar-nav ">
                    <li><a href="{!! route('scheduler.fullcalendar') !!}">{{ trans('Scheduler::texts.calendar') }}</a></li>
                    <li><a href="{!! route('scheduler.create') !!}">{{ trans('Scheduler::texts.create_event') }}</a></li>
                    <li><a href="{!! route('scheduler.tableevent') !!}">{{ trans('Scheduler::texts.event_table') }}</a></li>
                    <li><a href="{!! route('scheduler.tablerecurringevent') !!}">{{ trans('Scheduler::texts.recurring_event') }}</a></li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">{{ trans('Scheduler::texts.utilities') }}
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{!! route('scheduler.tablereport') !!}">{{ trans('Scheduler::texts.table_report') }}</a></li>
                            <li><a href="{!! route('scheduler.calendarreport') !!}">{{ trans('Scheduler::texts.calendar_report') }}</a></li>
                            <li><a href="{!! route('scheduler.eventtrash') !!}">{{ trans('Scheduler::texts.trash') }}</a></li>
                            <li><a href="{!! route('scheduler.categories.index') !!}">{{ trans('Scheduler::texts.categories') }}</a></li>
                            <li><a href="{!! route('scheduler.settings') !!}">{{ trans('Scheduler::texts.settings') }}</a></li>
                            <li><a href="{!! route('scheduler.about') !!}">{{ trans('Scheduler::texts.about') }}</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="row col-lg-12">
            <div class="col-lg-4 col-md-4">
                <div class="panel panel-green">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-tasks fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge">{!! $monthEvent !!}</div>
                                <div>{{ trans('Scheduler::texts.events_this_month') }}</div>
                            </div>
                        </div>
                    </div>
                    <a href="{!! route('scheduler.fullcalendar') !!}">
                        <div class="panel-footer">
                            <span class="pull-left">{{ trans('Scheduler::texts.vevents_this_month') }}</span>
                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="panel panel-yellow">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-tasks fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge">{!! $lastMonthEvent !!}</div>
                                <div>{{ trans('Scheduler::texts.events_last_month') }}</div>
                            </div>
                        </div>
                    </div>
                    <a href="{!! route('scheduler.fullcalendar') !!}?status=last">
                        <div class="panel-footer">
                            <span class="pull-left">{{ trans('Scheduler::texts.vevents_last_month') }}</span>
                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="panel panel-red">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-tasks fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge">{!! $nextMonthEvent !!}</div>
                                <div>{{ trans('Scheduler::texts.events_next_month') }}</div>
                            </div>
                        </div>
                    </div>
                    <a href="{!! route('scheduler.fullcalendar') !!}?status=next">
                        <div class="panel-footer">
                            <span class="pull-left">{{ trans('Scheduler::texts.vevents_next_month') }}</span>
                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        {{--Reminder table --}}
        <div class="row col-lg-12" ng-app="event" ng-controller="eventDeleteController">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-bell-o"></i> {{ trans('Scheduler::texts.reminders') }}</h3>
                </div>
                <div class="panel-body">
                    <table id="dt-reminderstable" class="display" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>{{ trans('Scheduler::texts.event_title') }}</th>
                            <th>{{ trans('Scheduler::texts.reminder_text') }}</th>
                            <th>{{ trans('Scheduler::texts.occasion_start') }}</th>
                            <th>{{ trans('Scheduler::texts.occasion_end') }}</th>
                            <th>{{ trans('Scheduler::texts.link') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($reminders as $reminder)
                            <tr id="{!! $reminder->id !!}">
                                <td>{!! $reminder->Schedule->title !!}</td>
                                <td>{!! $reminder->reminder_text !!}</td>
                                <td>{!! $reminder->Schedule->occurrences->first()->start_date !!}</td>
                                <td>{!! $reminder->Schedule->occurrences->first()->end_date !!}</td>
                                <td><a href="{!! $reminder->Schedule->url !!}">
                                        @if($reminder->Schedule->url)
                                            {{ trans('Scheduler::texts.link_to_workorder') }}</a></td>
                                @else
                                    <a class="btn btn-danger delete" ng-click="delete({!! $reminder->id !!})"><i
                                                class="fa fa-fw fa-trash-o"></i>{{ trans('Scheduler::texts.delete') }}</a></td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> {{ trans('Scheduler::texts.month_day_events') }}</h3>
                </div>
                <div class="panel-body">
                    <div id="morris-bar-chart"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> {{ trans('Scheduler::texts.year_month_report') }}</h3>
                </div>
                <div class="panel-body">
                    <div id="morris-donut-chart" style="width: 100%;height: 400px;font-size: 11px;"></div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('javascript')
    {!! HTML::style('modules/scheduler/morris.js.so/morris.css') !!}
    {!! HTML::script('modules/scheduler/raphael/raphael.min.js') !!}
    {!! HTML::script('modules/scheduler/morris.js.so/morris.min.js') !!}
    {{--{!! HTML::script('custom/addons/Scheduler/assets/js/morris-data.js') !!}--}}
    <script type="text/javascript" language="javascript" class="init">
        $(function () {
            Morris.Donut({
                element: 'morris-donut-chart',
                data: [
                        @foreach($fullYearMonthEvent as $yearMonthEvent)
                    {
                        label: "{!! date('M-Y', strtotime($yearMonthEvent->start_date)) !!}",
                        value: "{!! $yearMonthEvent->total !!}"
                    },
                    @endforeach
                ]
            });
        });
        $(function () {
            Morris.Bar({
                element: 'morris-bar-chart',
                data: [
                        @foreach($fullMonthEvent as $MonthEvent)
                    {
                        y: "{!! date('M-d', strtotime($MonthEvent->start_date)) !!}",
                        a: "{!! $MonthEvent->total !!}"
                    },
                    @endforeach
                ],
                xkey: 'y',
                ykeys: ['a'],
                labels: ['Total Event This Day'],
                hideHover: 'auto',
                resize: true
            });
        });
    </script>
    @include('Scheduler::partials._js_eventDeleteController',
            ['droute'=>'scheduler.trashreminder',
            'pnote'=>trans('Scheduler::texts.reminder_deleted_success'),
            'pCnote'=>trans('Scheduler::texts.reminder_delete_warning')])
@stop


