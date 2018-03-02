@extends('Scheduler::partials._master')

@section('content')
    @if(!config('app.name') == 'Invoice Ninja') {!! Form::breadcrumbs() !!} @endif

    <!--basic form starts-->
    <div class="col-md-8 col-md-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i
                            class="fa fa-edit fa-fw"></i>
                    {{ trans('Scheduler::texts.create_category') }}
                </h3>

            </div>
            <div class="panel-body">
            {!! Form::open(['route' => 'scheduler.categories.store', 'class'=>'form-horizontal']) !!}
            <!-- Name input-->
                <div class="form-group">
                    {!! Form::label('name',trans('Scheduler::texts.category_name'),['class'=>'col-sm-2 control-label']) !!}
                    <div class="col-md-6">
                        {!! Form::text('name',old('name'),['id'=>'name', 'placeholder'=>'Category Name', 'class'=>'form-control']) !!}
                    </div>
                </div>
                <!-- text_color input-->
                <div id="cp1" class="form-group colorpicker-component">
                    {!! Form::label('text_color',trans('Scheduler::texts.category_text_color'),['class'=>'col-sm-2 control-label']) !!}
                    <div class="col-md-3">
                        {!! Form::text('text_color',Request::old('text_color'),['id'=>'text_color', 'placeholder'=>'Text Color', 'class'=>'form-control']) !!}
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
                <!-- text_color input-->
                <div id="cp2" class="form-group colorpicker-component">
                    {!! Form::label('bg_color',trans('Scheduler::texts.category_bg_color'),['class'=>'col-sm-2 control-label']) !!}
                    <div class="col-md-3">
                        {!! Form::text('bg_color',Request::old('bg_color'),['id'=>'bg_color', 'placeholder'=>'Background Color', 'class'=>'form-control']) !!}
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div style="text-align:center" class="buttons">
            <a class="btn btn-warning btn-lg" href={!! URL::previous()  !!}>{{ trans('Scheduler::texts.cancel') }} <span
                        class="glyphicon glyphicon-remove-circle"></span></a>
            <button type="submit" class="btn btn-success btn-lg">{{ trans('Scheduler::texts.save') }} <span
                        class="glyphicon glyphicon-floppy-disk"></span></button>
            {{--{!! Button::normal(trans('texts.cancel'))
                    ->large()
                    ->asLinkTo(URL::previous())
                    ->appendIcon(Icon::create('remove-circle')) !!}

            {!! Button::success($title)
                    ->submit()
                    ->large()
                    ->appendIcon(Icon::create('floppy-disk')) !!}--}}
        </div>
    </div>
    {!! Form::close() !!}
    <script>
        $('#cp1').colorpicker({color: '#ffffff', format: 'hex'});
        $('#cp2').colorpicker({color: '#000000', format: 'hex'});
    </script>
    </div>
    </div>
@stop
@section('javascript')
    {!! HTML::style('modules/scheduler/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') !!}
    {!! HTML::script('modules/scheduler/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') !!}

@stop