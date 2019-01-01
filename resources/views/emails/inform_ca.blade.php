@extends('layouts.emaillayout')
@section("content")
    Hi CA,<br/>
    A new account has been registered.
    Account details:
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
            <tr>
                <td>Approve</td>
                <td>: <a href="{{ $approve_link }}" target="_blank">Approve</a></td>
            </tr>
        </tbody>
    </table>
@stop