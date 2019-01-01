@extends('adminlte::login')
<section class="content" style="min-height:0px">
<div class="col-xs-12">
    @if(Session::has('message'))
        <div class="alert {{ Session::get('alert-class', 'alert-info') }} alert-dismissible">{{ Session::get('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        </div>
    @endif
</div>
</section>