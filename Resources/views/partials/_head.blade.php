<!-- Bootstrap Core CSS -->
{{-- line below causing conflict with layouts.master --}}
{{--{!! HTML::style('custom/addons/Scheduler/assets/css/bootstrap.min.css') !!}--}}
{!! HTML::style('modules/scheduler/jquery-ui-dist/jquery-ui.min.css') !!}
{!! HTML::style('modules/scheduler/css/custom.css') !!}
{!! HTML::style('modules/scheduler/pnotify/dist/pnotify.css') !!}
{!! HTML::style('modules/scheduler/datatables.net-dt/css/jquery.dataTables.css') !!}
{{--make sure to use from the BUILD directory...--}}
{!! HTML::style('modules/scheduler/jquery-datetimepicker/build/jquery.datetimepicker.min.css') !!}
<!-- Custom CSS -->
{!! HTML::style('modules/scheduler/css/sb-admin-2.min.css') !!}
<!-- Fonts -->
{!! HTML::style('modules/scheduler/font-awesome/css/font-awesome.min.css') !!}
{{--SCRIPTS--}}
<!-- Bootstrap Core JavaScript -->
{{-- line below causing conflict with layouts.master which is using 3.3.4   BELOW is 3.3.7--}}
{{--<script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>--}}
{!! HTML::script('modules/scheduler/jquery-ui-dist/jquery-ui.min.js') !!}
{{--make sure to use from the BUILD directory...--}}
{!! HTML::script('modules/scheduler/jquery-datetimepicker/build/jquery.datetimepicker.full.min.js') !!}
{{-- line below causing conflict with layouts.master which is using 2.2.4   BELOW is 3.2.1--}}
{{--{!! HTML::script('modules/scheduler/jquery/dist/jquery.min.js') !!}--}}
{!! HTML::script('modules/scheduler/pnotify/dist/pnotify.js') !!}
{{-- line below causing conflict InvoiceNinja--}}
{{--{!! HTML::script('modules/scheduler/angular/angular.min.js') !!}--}}
{!! HTML::script('modules/scheduler/datatables.net/js/jquery.dataTables.js') !!}
{{-- line below causing conflict InvoiceNinja--}}
{{--{!! HTML::script('modules/scheduler/datatables.net-bs/js/dataTables.bootstrap.js') !!}--}}

{{--_foot--}}
@include('Scheduler::partials._js_datatables')
@include('Scheduler::partials._alerts')