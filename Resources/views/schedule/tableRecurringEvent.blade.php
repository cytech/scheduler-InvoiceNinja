@extends('Scheduler::partials._master')

@section('content')
    @if(!config('app.name') == 'Invoice Ninja') {!! Form::breadcrumbs() !!} @endif
    <div class="container col-lg-12">
        <div class="row">
            <div class="col-lg-12">
                <a href="{!! route('scheduler.createrecurringevent') !!}" class="btn btn-success std-actions" ><i
                            class="fa fa-fw fa-plus"></i> {{ trans('Scheduler::texts.create_recurring_event') }}</a>
            </div>
            <div class="col-lg-12">
                <a href="javascript:void(0)" class="btn btn-danger bulk-actions" id="btn-bulk-trash"><i
                            class="fa fa-trash-o"></i> {{ trans('Scheduler::texts.bulk_event_trash') }}</a>
            </div>
        </div>
        <br/>
        <!-- /.row -->
        <div class="row" ng-app="event" ng-controller="eventDeleteController">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i
                                    class="fa fa-fw fa-table fa-fw"></i> {{ trans('Scheduler::texts.recurring_table') }}
                        </h3>
                    </div>
                    <div class="panel-body">
                        <table id="dt-filtertable" class="display" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th><div class="btn-group"><input type="checkbox" id="bulk-select-all"></div></th>
                                <th>{{ trans('Scheduler::texts.title') }}</th>
                                <th>{{ trans('Scheduler::texts.description') }}</th>
                                <th>{{ trans('Scheduler::texts.start_date') }}</th>
                                <th>{{ trans('Scheduler::texts.frequency') }}</th>
                                <th>{{ trans('Scheduler::texts.category') }}</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($events as $event)
                                <tr id="{!! $event->id !!}">
                                    <td><input type="checkbox" class="bulk-record" data-id="{{ $event->id }}"></td>
                                    <td>{!! $event->title !!}</td>
                                    <td>{!! str_limit(strip_tags($event->description),25) !!}</td>
                                    <td>{!! $event->occurrences()->first()->start_date !!}</td>
                                    <td>{!! $event->textTrans !!}</td>
                                    <td>{!! $event->category->name !!}</td>
                                    <td>
                                        <a class="btn btn-danger delete" ng-click="delete({!! $event->id !!})"><i
                                                    class="fa fa-fw fa-trash-o"></i>{{ trans('Scheduler::texts.trash') }}</a>
                                        <a class="btn btn-primary iframe"
                                           href='{!! route("scheduler.editrecurringevent", [$event->id]) !!}'><i
                                                    class="fa fa-fw fa-edit"></i>{{ trans('Scheduler::texts.edit') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('javascript')
    @include('Scheduler::partials._js_eventDeleteController',
    ['droute'=>'scheduler.trashevent',
    'pnote'=>trans('Scheduler::texts.event_trashed_success'),
    'pCnote'=>trans('Scheduler::texts.event_trash_warning')])
    @include('Scheduler::partials._js_bulk_ajax',
    ['droute'=>'scheduler.bulk.trash',
    'pnote'=>trans('Scheduler::texts.bulk_event_trash_success'),
    'pCnote'=>trans('Scheduler::texts.bulk_event_trash_warning')])
@stop