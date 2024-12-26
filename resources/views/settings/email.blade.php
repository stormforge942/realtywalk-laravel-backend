@extends('layouts.app')

@section('title')
    {!! __('setting.email.meta.title') !!}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            @include('flash::message')
            @include('coreui-templates::common.errors')

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <strong>
                                {{ __('setting.email.title') }}
                            </strong>
                        </div>
                        <div class="card-body">
                            {!! Form::open(['route' => 'settings.email.update', 'method' => 'patch']) !!}

                            @include('settings.fields.email')

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <strong>
                                {{ __('setting.secretary-email.title') }}
                            </strong>
                        </div>
                        <div class="card-body">
                            {!! Form::open(['route' => 'settings.email.update', 'method' => 'patch']) !!}

                            @include('settings.fields.secretary-email')

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            if ($("#add-email")) {
                let emailsNum = 1;
                //add more inputs for links
                $("#add-email").click(function(e) {
                    e.preventDefault();
                    emailsNum++;
                    //change to match the email field
                    $("#emails-container").append(
                        "<div class='inner-emails'><input class='form-control' type='email' name='notification_email[]' /><button id='" +
                        emailsNum +
                        "' type='button' class='btn btn-default remove'>Remove</button></div>")
                });


                $(document).on("click", ".remove", function() {
                    let $this = $(this);
                    //remove the parent of the current link
                    $this.parent().remove();

                });
            }

            if ($("#add-secretary-email")) {
                let emailsNum = 1;
                //add more inputs for links
                $("#add-secretary-email").click(function(e) {
                    e.preventDefault();
                    emailsNum++;
                    //change to match the email field
                    $("#secretary-emails-container").append(
                        "<div class='inner-emails'><input class='form-control' type='email' name='secretary_notification_email[]' /><button id='" +
                        emailsNum +
                        "' type='button' class='btn btn-default remove'>Remove</button></div>")
                });


                $(document).on("click", ".remove", function() {
                    let $this = $(this);
                    //remove the parent of the current link
                    $this.parent().remove();

                });
            }
        });
    </script>
@endsection
