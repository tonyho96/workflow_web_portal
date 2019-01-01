@extends('adminlte::page')

{{--@section('title', 'AdminLTE')--}}

@section('content_header')
    <h1>Import/Export</h1>
    @if ($errors->has('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{$errors->first('error')}}
        </div>
    @endif
    @if (Session::has('message'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <p><i class="icon fa fa-check"></i>{{Session::get('message')}}</p>
        </div>
    @endif
@stop

@section('content')
    <div class="container-fluid">
        <div class="form-group ">
            <div class="col-sm-12">
                {!! Form::open(['action' => ['FormController@importCSV'], 'method' => 'POST', 'id' => 'import-csv', 'enctype' => 'multipart/form-data']) !!}
                {!! Form::token() !!}

                <div class="form-group">
                    {!! Form::label('import_file', 'Import', ['class' => 'control-label col-sm-2']) !!}

                    <div class="col-sm-3" style="margin-bottom: 15px;">
                        {!! Form::file('import_file', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="col-sm-4" style="margin-bottom: 15px;">
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div><!-- END STATUS -->

        <hr>

        <div class="form-group ">
            <div class="col-sm-12">
                {!! Form::open(['action' => ['FormController@exportCSV'], 'method' => 'POST', 'id' => 'export-csv']) !!}
                {!! Form::token() !!}

                <div class="form-group">
                    {!! Form::label('range', 'Export', ['class' => 'control-label col-sm-2']) !!}

                    <div class="col-sm-3" style="margin-bottom: 15px;">
                        {!! Form::text('range', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="col-sm-4" style="margin-bottom: 15px;">
                        <button type="submit" name="export" class="btn btn-primary"
                                value="{{config('form.export_option.all')}}">Export
                        </button>
                        <div class="col-sm-8" style="margin-bottom: 15px;">
                        {!! Form::select('specified_user', $specified_user, null, ['class' => 'form-control', 'style' => '','required' =>'required']) !!}
                        </div>
                    </div>

                </div>
                {!! Form::close() !!}
            </div>
        </div><!-- END STATUS -->

        <hr>

        <div class="form-group ">
            <div class="col-sm-12">
                <div class="form-group">
                    {!! Form::label('overview', 'Overview', ['class' => 'control-label col-sm-2']) !!}

                    <div class="col-sm-3" style="margin-bottom: 15px;">
                        {!! Form::select('overview', $crUsers, @$_GET['overview'], ['class' => 'form-control']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div><!-- END STATUS -->


        <h2 class="text-center">Summary</h2>
        <canvas id="myChart" width="400" height="130"></canvas>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/daterangepicker.js') }}"></script>
    <script src="{{ asset('js/Chart.min.js') }}"></script>

    <script>
        $('#overview').change(function () {
            location.href = '{{ route('import-export') }}' + '?overview=' + $(this).val();
        });

        $('#range').daterangepicker({
            ranges: {
                'Last Week': [moment().subtract(6, 'days'), moment()],
                'Last Month': [moment().subtract(1, 'month'), moment()],
                'Last Quarter': [moment().subtract(3, 'month'), moment()],
                'Last Year': [moment().subtract(1, 'year'), moment()]
            }
        });

        var chartDataString = '<?php echo json_encode($chartData) ?>';
        var chartData = JSON.parse(chartDataString);
        var ctx = document.getElementById("myChart").getContext('2d');
        var backgroundColor = [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)'
        ];
        var borderColor = [
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)'
        ];

        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ["All changes", "Raised by user", "Success by particulars", "Pending CAB", "Rejected"],
                datasets: [
                    {
                        label: 'Year',
                        data: [
                            chartData.year.allForms,
                            chartData.year.raisedByCRForms,
                            chartData.year.successByCRForms,
                            chartData.year.pendingCABForms,
                            chartData.year.rejectedForms
                        ],
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
                        borderWidth: 1,
                        hidden: true
                    },
                    {
                        label: 'Quarter',
                        data: [
                            chartData.quarter.allForms,
                            chartData.quarter.raisedByCRForms,
                            chartData.quarter.successByCRForms,
                            chartData.quarter.pendingCABForms,
                            chartData.quarter.rejectedForms
                        ],
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
                        borderWidth: 1,
                        hidden: true
                    },
                    {
                        label: 'Month',
                        data: [
                            chartData.month.allForms,
                            chartData.month.raisedByCRForms,
                            chartData.month.successByCRForms,
                            chartData.month.pendingCABForms,
                            chartData.month.rejectedForms
                        ],
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
                        borderWidth: 1,
                        hidden: true
                    },
                    {
                        label: 'Week',
                        data: [
                            chartData.week.allForms,
                            chartData.week.raisedByCRForms,
                            chartData.week.successByCRForms,
                            chartData.week.pendingCABForms,
                            chartData.week.rejectedForms
                        ],
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }]
                }
            }
        });
    </script>
@stop
