@extends('adminlte::page')

{{--@section('title', 'AdminLTE')--}}

@section('content_header')

    <h1>Forward Schedule Of Change</h1>

@stop

@section('content')
    <style type="text/css">
        .tb-checked-completed {
            background-color: #2F70BD !important;
        }

        .tb-checked-cab {
            background-color: #56cfc7 !important;
        }

        .tb-checked-planned {
            background-color: #89CF56 !important;
        }

        .tb-checked-failed {
            background-color: #C71D0C !important;
        }

        .tb-checked-non-change {
            background-color: #F9BD25 !important;
        }

        #forward-schedule td {
            vertical-align: middle;
            border: 1px solid #dddddd;
        }

        #forward-schedule tbody td {
            border: 1px solid #333333;
        }

        #forward-schedule thead tr:last-child td {
            border-bottom: 1px solid #333333;
        }

        #forward-schedule tbody td.equal {
            width: 20px !important;
            height: 35.56px;
        }

        .padding_0 {
            padding: 0px !important;
        }
    </style>
    @if (empty($data))
        <h1>Empty data</h1>
    @else
    <div class="table-responsive">
        <table id="forward-schedule" class="table">
            <thead>
            <!-- START - Bind data -->
            @php

                $start = new DateTime($data['oldestMonday']);
                $end = new DateTime($data['latestSunday']);

                $diff = $end->diff($start);
                $days = $diff->days + 1;
                $start = $data['oldestMonday'];
                $end   = $data['latestSunday'];
            @endphp
            <tr>
                <td></td>
                <td>Change Planned</td>
                <td style="background-color: #89CF56;"></td>
                @for($i = 0; $i < $days; $i++)
                    <td></td>
                @endfor
            </tr>

            <tr>
                <td></td>
                <td>CAB date</td>
                <td style="background-color: #56cfc7;"></td>
                @for($i = 0; $i < $days; $i++)
                    <td></td>
                @endfor
            </tr>

            <tr>
                <td></td>
                <td>Change Successful</td>
                <td style="background-color: #2F70BD;"></td>
                @for($i = 0; $i < $days; $i++)
                    <td></td>
                @endfor
            </tr>

            <tr>
                <td></td>
                <td>Change Unsuccessful</td>
                <td style="background-color: #C71D0C;"></td>
                @for($i = 0; $i < $days; $i++)
                    <td></td>
                @endfor
            </tr>

            <tr>
                <td></td>
                <td>Non change awareness of work</td>
                <td style="background-color: #F9BD25;"></td>
                @for($i = 0; $i < $days; $i++)
                    <td></td>
                @endfor
            </tr>
            <!-- END - Bind data -->


            <tr>
                <td></td>
                <td></td>
                <td>wc</td>

                <!-- START - Bind data -->

                @php
                    $start = $data['oldestMonday'];
                    $end   = $data['latestSunday'];
                @endphp
                @while($start <= $end)
                    <td colspan="7" class="text-center">{{ date('d/m/Y', strtotime($start)) }}</td>
                @php
                    $start = date('Y-m-d', strtotime("$start + 7 DAYS"));
                @endphp
            @endwhile
            <!-- END - Bind data -->
            </tr>
            <tr>
                <td>Reference</td>
                <td>Title</td>
                <td>Status</td>

                <!-- START - Bind data -->

                @php
                    $start = $data['oldestMonday'];
                    $end   = $data['latestSunday'];
                @endphp
                @while($start <= $end)
                    <td>M</td>
                    <td>T</td>
                    <td>W</td>
                    <td>Th</td>
                    <td>F</td>
                    <td>Sa</td>
                    <td>Su</td>
                    @php
                        $start = date('Y-m-d', strtotime("$start + 7 DAYS"));
                    @endphp
                @endwhile
            </tr>

            </thead>

            <tbody>
            <!-- START - Bind data -->
            @foreach($data['forms'] as $form)
                <tr>
                    <td>{{ $form['reference'] }}</td>
                    <td>{{ $form['title'] }}</td>
                    @if($form['isOpened'])
                      <td>Opened</td>
                    @else
                      <td>Closed</td>
                    @endif
                    @php
                        $start = $data['oldestMonday'];
                        $end   = $data['latestSunday'];
                    @endphp
                    @while($start <= $end)
                        @for($i = 0; $i < 7; $i++)
                            @php
                            $date = date('Y-m-d', strtotime($start . " + $i DAYS"));

                            $classes = [];
                            if ($form['non_change'] == 'non_change'){
                                if ($form['start_date'] <= $date && $form['end_date'] >= $date){
                                    $classes[] = 'tb-checked-non-change';
                                    }
                            }else{
                                if ($form['start_date'] <= $date && $form['end_date'] >= $date)
                                    $classes[] = 'tb-checked-planned';
                            }
                            if ($form['completed'] == $date)
                                $classes[] = 'tb-checked-completed';
                            if ($form['failed'] == $date)
                                $classes[] = 'tb-checked-failed';
                            if ($form['planned'] == $date)
                                $classes[] = 'tb-checked-cab';
                            @endphp

                            <td class="equal padding_0 toggle-non-change" data-toggle="tooltip" title="{{$date}}" data-date="{{ $date }}" data-form-id="{{ $form['id'] }}">
                                @foreach($classes as $class)
                                    <div class="checked-status {{ $class }}" style="width: 100%; height: {{ 100 / (max(count($classes), 1)) }}%"></div>
                                @endforeach
                            </td>
                        @endfor
                        @php
                            $start = date('Y-m-d', strtotime("$start + 7 DAYS"));
                        @endphp
                    @endwhile
                </tr>
            @endforeach
            <!-- END - Bind data -->
            </tbody>
        </table>
    </div>
    @endif
@stop