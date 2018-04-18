@extends('Scheduler::partials._master')

@section('content')

    <div class="container col-lg-12">
        <div class="row">
            <div class="col-lg-12">
                <a href="{!! route('scheduler.categories.create') !!}" class="btn btn-success"><i
                            class="fa fa-fw fa-plus"></i> {{ trans('Scheduler::texts.create_category') }}</a>
            </div>
        </div>
        <br/>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i
                                    class="fa fa-fw fa-table fa-fw"></i>{{ trans('Scheduler::texts.categories') }}
                        </h3>
                    </div>
                    {{--@include('Scheduler::layouts._alerts')--}}
                    <div class="panel-body">
                        <table id="dt-categoriestable" class="display" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{ trans('Scheduler::texts.category_name') }}</th>
                                <th>{{ trans('Scheduler::texts.category_text_color') }}</th>
                                <th>{{ trans('Scheduler::texts.category_bg_color') }}</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($categories as $category)
                                <tr id="{!! $category->id !!}">
                                    <td>{!! $category->id !!}</td>
                                    <td>{!! $category->name !!}</td>
                                    <td>{!! $category->text_color !!}&nbsp;&nbsp;&nbsp;<i class="fa fa-square"
                                                                                          style="color:{!! $category->text_color !!}"></i>
                                    </td>
                                    <td>{!! $category->bg_color !!}&nbsp;&nbsp;&nbsp;<i class="fa fa-square"
                                                                                        style="color:{!! $category->bg_color !!}"></i>
                                    </td>
                                    <td>
                                        <a class="btn btn-primary iframe"
                                           href="{{ route('scheduler.categories.edit', [$category->id]) }}"><i
                                                    class="fa fa-fw fa-edit"></i>{{ trans('Scheduler::texts.edit') }}</a>
                                        @if($category->id > 9)
                                            {!! Form::button('<i class="fa fa-fw fa-trash"></i>'.trans('Scheduler::texts.delete'), ['type' => 'button','class' => 'btn btn-danger delete-button', 'data-id'=> $category->id]) !!}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <script>
                            $(function () {
                            $('#dt-categoriestable').on('click','.delete-button',function () {
                                var id = ($(this).data('id'));
                                pconfirm_def.text = '{!! trans('Scheduler::texts.delete_warning') !!}';
                                new PNotify(pconfirm_def).get().on('pnotify.confirm', function () {
                                    $.post('{!! url('/scheduler/categories/delete/') !!}' + '/' + id, {

                                    }).done(function () {
                                        pnotify('{!! trans('Scheduler::texts.deleted_item_success') !!}', 'success');
                                        $("#" + id).hide();
                                    }).fail(function (data) {
                                        pnotify(data.responseJSON.error, 'error');
                                    });
                                }).on('pnotify.cancel', function () {
                                    //Do Nothing
                                });
                            })
                            });

                        </script>
                    </div>
                </div>
            </div>
        </div>
@stop
@section('javascript')

@stop
