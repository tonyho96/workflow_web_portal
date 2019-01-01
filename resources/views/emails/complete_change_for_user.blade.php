@extends('layouts.emaillayout')
@section("content")
    Hi {{$user->name}},<br/>
    "{{ $form->change_title }}" has been completed.<br/>
    <table class="table">
        <tbody>
        @foreach($detailData as $detailDatum)
            <tr>
                <td><?php echo $detailDatum['name']; ?></td>
                <td>: <?php echo $detailDatum['value']; ?></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@stop