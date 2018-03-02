@extends('header')

@section('head')
    @parent
    @include('Scheduler::partials._head')
    @include('Scheduler::partials._js_datetimepicker')
    {{--for InvoiceNinja--}}
    @yield('javascript')
@endsection

{{--for FusionInvoice--}}
{{--@section('javascript')--}}

    {{--@yield('javascript')--}}

{{--@endsection--}}
