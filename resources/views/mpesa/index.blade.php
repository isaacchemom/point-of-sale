@extends('layouts.app')
@section('title', __( 'M-PESA Settings' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'M-PESA Settings' )
        <small>@lang( 'Manage M-PESA' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="container p-0">
        <div class="row">
            <div class="col-lg-12">
            @if (isset($output))
            @if($output['status'] == 'success')
               <p style="color:greenyellow"> {{$output['message']}}</p>

            @else
                <p style="color:red"> {{$output['message']}}</p>

            @endif
                
            @endif

                {{ Form::open(['class' => 'form-horizontal', 'route' => 'update_mpesa_settings', 'method'
                => 'POST']) }}

                <div class="white-box">
                    <div class="row p-0">
                        <div class="col-lg-8 offset-lg-2">
                            <div class="row mt-2 d-flex justify-content-between align-items-center" style="margin-bottom:3px;">
                                <div class="">
                                    <p class="text-uppercase fw-500 mb-10">
                                        @lang('M-PESA Env:') </p>
                                </div>
                                <div class="">
                                    <select name="mpesa_env" class="form-control mpesa_env">

                                        <option value="{{$mpesa_settings? $mpesa_settings->mpesa_env: 'sandbox'}}"
                                            selected>{{$mpesa_settings? $mpesa_settings->mpesa_env: 'sandbox'}}
                                        </option>
                                        <option value="live">Live</option>
                                        <option value="sandbox">Sandbox</option>

                                    </select>
                                    @error('mpesa_env')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>

                            </div>
                            <div class="row mt-2 live-credential d-flex justify-content-between align-items-center" style="margin-bottom:3px;">
                                <div class="">
                                    <p class="text-uppercase fw-500 mb-10">
                                        @lang('M-PESA Pass Key:') </p>
                                </div>
                                <div class="">
                                    <input type="text" name="mpesa_passkey"
                                        value="{{$mpesa_settings? $mpesa_settings->mpesa_passkey: ''}}"
                                        class="form-control" id="">
                                </div>

                            </div>
                            <div class="row mt-2 live-credential d-flex justify-content-between align-items-center" style="margin-bottom:3px;">
                                <div class="">
                                    <p class="text-uppercase fw-500 mb-10">
                                        @lang('M-PESA Consumer Key:') </p>
                                </div>
                                <div class="">
                                    <input type="text" name="mpesa_consumerkey"
                                        value="{{$mpesa_settings? $mpesa_settings->mpesa_consumerkey: ''}}"
                                        class="form-control" id="">
                                </div>

                            </div>
                            <div class="row mt-2 live-credential d-flex justify-content-between align-items-center" style="margin-bottom:3px;">
                                <div class="">
                                    <p class="text-uppercase fw-500 mb-10">
                                        @lang('M-PESA Consumer Secret:') </p>
                                </div>
                                <div class="">
                                    <input type="text" name="mpesa_consumersecret"
                                        value="{{$mpesa_settings? $mpesa_settings->mpesa_consumersecret: ''}}"
                                        class="form-control" id="">
                                </div>

                            </div>
                            <div class="row mt-2 live-credential d-flex justify-content-between align-items-center" style="margin-bottom:3px;">
                                <div class="">
                                    <p class="text-uppercase fw-500 mb-10">
                                        @lang('M-PESA Short Code:') </p>
                                </div>
                                <div class="">
                                    <input type="text" name="mpesa_shortcode"
                                        value="{{$mpesa_settings? $mpesa_settings->mpesa_shortcode: ''}}"
                                        class="form-control" id="">
                                </div>

                            </div>
                            <div class="row mt-2 live-credential d-flex justify-content-between align-items-center" style="margin-bottom:3px;">
                                <div class="">
                                    <p class="text-uppercase fw-500 mb-10">
                                        @lang('M-PESA Callback URL:') </p>
                                </div>
                                <div class="">
                                    <input type="text" name="mpesa_callbackurl"
                                        value="{{$mpesa_settings? $mpesa_settings->mpesa_callbackurl: ''}}"
                                        class="form-control" id="">
                                    @error('mpesa_callbackurl')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>

                            </div>

                            <div class="row mt-2 live-credential d-flex justify-content-between align-items-center" style="margin-bottom:3px;">
                                <div class="">
                                    <p class="text-uppercase fw-500 mb-10">
                                        @lang('M-PESA Validation URL:') </p>
                                </div>
                                <div class="">
                                    <input type="text" name="mpesa_validationurl"
                                        value="{{$mpesa_settings? $mpesa_settings->mpesa_validationurl: ''}}"
                                        class="form-control" id="">
                                </div>

                            </div>
                            <div class="row mt-2 live-credential d-flex justify-content-between align-items-center" style="margin-bottom:3px;">
                                <div class="">
                                    <p class="text-uppercase fw-500 mb-10">
                                        @lang('M-PESA Confirmation URL:') </p>
                                </div>
                                <div class="">
                                    <input type="text" name="mpesa_confirmationurl"
                                        value="{{$mpesa_settings? $mpesa_settings->mpesa_confirmationurl: ''}}"
                                        class="form-control" id="">
                                </div>

                            </div>
                            <div class="row mt-2 live-credential d-flex justify-content-between align-items-center" style="margin-bottom:3px;">
                                <div class="">
                                    <p class="text-uppercase fw-500 mb-10">
                                        @lang('M-PESA Amount:') </p>
                                </div>
                                <div class="">
                                    <input type="text" name="mpesa_amount"
                                        value="{{$mpesa_settings? $mpesa_settings->mpesa_amount: ''}}"
                                        class="form-control" id="">
                                </div>

                            </div>

                            <div class="row mt-2 test_credential d-flex justify-content-between align-items-center" style="margin-bottom:3px;">
                                <div class="">
                                    <p class="text-uppercase fw-500 mb-10">
                                        @lang('M-PESA Test Pass Key:') </p>
                                </div>
                                <div class="">
                                    <input type="text" name="mpesa_testpasskey"
                                        value="{{$mpesa_settings? $mpesa_settings->mpesa_testpasskey: ''}}"
                                        class="form-control" id="">
                                    @error('mpesa_testpasskey')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>

                            </div>
                            <div class="row mt-2 test_credential d-flex justify-content-between align-items-center" style="margin-bottom:3px;">
                                <div class="">
                                    <p class="text-uppercase fw-500 mb-10">
                                        @lang('M-PESA Test Consumer Key:') </p>
                                </div>
                                <div class="">
                                    <input type="text" name="mpesa_testconsumerkey"
                                        value="{{$mpesa_settings? $mpesa_settings->mpesa_testconsumerkey: ''}}"
                                        class="form-control" id="">
                                    @error('mpesa_testconsumerkey')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>

                            </div>
                            <div class="row mt-2 test_credential d-flex justify-content-between align-items-center" style="margin-bottom:3px;">
                                <div class=" test_credential">
                                    <p class="text-uppercase fw-500 mb-10">
                                        @lang('M-PESA Test Consumer Secret:') </p>
                                </div>
                                <div class="">
                                    <input type="text" name="mpesa_testconsumersecret"
                                        value="{{$mpesa_settings? $mpesa_settings->mpesa_testconsumersecret: ''}}"
                                        class="form-control" id="">
                                    @error('mpesa_testconsumersecret')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>

                            </div>
                            <div class="row mt-2 test_credential d-flex justify-content-between align-items-center" style="margin-bottom:3px;">
                                <div class=" test_credential">
                                    <p class="text-uppercase fw-500 mb-10">
                                        @lang('M-PESA Test Short Code:') </p>
                                </div>
                                <div class="">
                                    <input type="text" name="mpesa_testshortcode"
                                        value="{{$mpesa_settings? $mpesa_settings->mpesa_testshortcode: ''}}"
                                        class="form-control" id="">
                                    @error('mpesa_testshortcode')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>

                            </div>
                            <div class="row mt-2 test_credential d-flex justify-content-between align-items-center" style="margin-bottom:3px;">
                                <div class="">
                                    <p class="text-uppercase fw-500 mb-10">
                                        @lang('M-PESA Test Amount:') </p>
                                </div>
                                <div class="">
                                    <input type="text" name="mpesa_testamount"
                                        value="{{$mpesa_settings? $mpesa_settings->mpesa_testamount: ''}}"
                                        class="form-control" id="">
                                    @error('mpesa_env')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>

                            </div>
                        </div>


                    </div>
                    <div class="row mt-40">
                            <button class="btn btn-success" type="submit">
                                @lang('Save Changes')</button>
                            </button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade tax_rate_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade tax_group_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@endsection
@section('javascript')
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
<script>
    $(document).ready(function() {
        // alert("jQuery is working!");
        $('.live-credential').hide();
        $('.test_credential').hide();
        var mpesa_env = $('.mpesa_env')
        if (mpesa_env == 'live') {
            $('.test_credential').hide();
            $('.live-credential').show();
        } else {
            $('.live-credential').hide();
            $('.test_credential').show();

        }

        $('.mpesa_env').change(function() {
            console.log('Mpesa settings changed: ', $(this).val());
            var mpesa_env = $(this).val();
            if (mpesa_env == 'live') {
                $('.live-credential').show();
                $('.test_credential').hide();
            } else {
                $('.live-credential').hide();
                $('.test_credential').show();
            }
        });
    });
</script>
@endsection