@extends('layouts.emaillayout')
@section("content")
    Hi {{$user->name}},<br/>
    Your application has been {{strtolower($subject)}} by the CA.<br/>
    @if($reason!=null && trim($reason)!="")
        Reason: {!! str_replace("\n","<br/>", $reason ) !!}
    @endif
@stop