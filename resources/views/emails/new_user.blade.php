@extends('layouts.emaillayout')
@section("content")
    Hi {{$user->name}},<br/>
    Thank you for your registrations.
    Your account details:
    <table class="table">
        <tbody>
            <tr>
                <td>Name</td>
                <td>: {{ $user->name }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>: {{ $user->email }}</td>
            </tr>
            <tr>
                <td>Role</td>
                <td>: Change Requester</td>
            </tr>
        </tbody>
    </table>
@stop