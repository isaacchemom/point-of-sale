<!-- simonchanged -->
@extends('layouts.app')
@section('title', __( 'Payment Gateway Settings' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'Payment Gateway Settings' )
        <small>@lang( 'Manage your payment gateway settings' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'Payment Gateways' )])

    @slot('tool')
    <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal" id="mpesa_trigger_btn">
            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
    </div>
    @endslot

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="payment_gateways_table">
            <thead>
                <tr>
                    <th>@lang( 'Provider' )</th>
                    <th>@lang( 'Shortcode' )</th>
                    <th>@lang( 'Shortcode Type' )</th>
                </tr>
            </thead>
            <tbody id="table_content">

            </tbody>
        </table>
    </div>

    @endcomponent



    <div class="modal fade payment_gateways_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                {!! Form::open(['method' => 'post', 'id' => 'save_gateway' ]) !!}

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang( 'Add Payment Gateway Settings' )</h4>
                </div>

                <div class="modal-body">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 col-xs-12">
                                <input name="location_id" type="hidden" value="405">
                            </div>
                            <div class="clearfix"></div>


                            <div class="col-md-4 col-xs-12">
                                <div class="form-group">
                                    <label for="payment gateway type">Payment gateway type:*</label>
                                    <select class="form-control select2 valid" required="" autofocus="" id="provider" name="provider" aria-required="true" aria-invalid="false">
                                        <option selected="selected" value="">Select payment gateway type</option>
                                        <option value="mpesa">MPESA</option>
                                        <!-- <option value="kopokopo">KOPOKOPO</option>
                                        <option value="equity">EQUITY</option> -->
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix"></div>




                            <div class="payment_gateway_settings" data-service="mpesa">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="shortcode">Shortcode:*</label>
                                        <input class="form-control" required="" placeholder="Shortcode" id="mpesa_shortcode" name="mpesa_shortcode" type="text" aria-required="true">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="consumer_key">Consumer Key:</label>
                                        <input class="form-control" placeholder="Consumer Key" id="mpesa_consumerkey" name="mpesa_consumerkey" type="text">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="consumer_secret">Consumer Secret:</label>
                                        <input class="form-control" placeholder="Consumer Secret" id="mpesa_consumersecret" name="mpesa_consumersecret" type="text">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="passkey">Passkey:*</label>
                                        <input class="form-control" placeholder="Passkey" required="" id="mpesa_passkey" name="mpesa_passkey" type="text" aria-required="true">
                                    </div>
                                </div>


                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="paybill/till">Paybill or Till:*</label> <i class="fa fa-info-circle text-info hover-q no-print " aria-hidden="true" data-container="body" data-toggle="popover" data-placement="auto bottom" data-content="Pick the shortcode type you are using in your organization" data-html="true" data-trigger="hover"></i> 
                                        <select class="form-control" required="" id="mpesa_shortcode_type" name="mpesa_shortcode_type" aria-required="true">
                                            <option selected="selected" value="">Please Select</option>
                                            <option value="paybill">MPESA PAYBILL</option>
                                            <option value="till">MPESA TILL</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6" id="till_number">
                                    <label for="passkey">Till Number:*</label>
                                    <input type="text" id="till"  name="till_number" class="form-control">
                                </div> 
                                <div class="col-sm-6">
                                    <input type="submit" id="saveButton" class="btn btn-primary" value="Save">
                                </div>
                            </div>



                        </div>
                    </div>

                    {!! Form::close() !!}

                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>


</section>


<!-- /.content -->
@endsection


@section('javascript')
<script>
    $(document).ready(function() {
        //hide till_number
        $('#till_number').hide();

        //show till_number if mpesa_shortcode_type is till

        $('#mpesa_shortcode_type').change(function() {
            if ($(this).val() == 'till') {
                $('#till_number').show();
            } else {
                $('#till_number').hide();
            }
        });



        getGatewaysData();
        let mpesa_btn = $('#mpesa_trigger_btn');
        mpesa_btn.click(function(e) {
            $('.payment_gateways_modal').modal('show');
        })
        $('#save_gateway').submit(function(e) {
            e.preventDefault();
            let provider = $('#provider').val();
            let mpesa_shortcode = $('#mpesa_shortcode').val();
            let mpesa_passkey = $('#mpesa_passkey').val();
            let mpesa_consumerkey = $('#mpesa_consumerkey').val();
            let mpesa_consumersecret = $('#mpesa_consumersecret').val();
            let mpesa_shortcode_type = $('#mpesa_shortcode_type').val();
            let till_number = $('#till').val();
            let _token = $('meta[name="csrf-token"]').attr('content');

            let data = {
                provider,
                mpesa_passkey,
                mpesa_consumerkey,
                mpesa_consumersecret,
                mpesa_shortcode,
                mpesa_shortcode_type,
                till_number,
                _token

            }
            $.ajax({
                url: '/payment-gateways/store',
                type: 'POST',
                data: data,
                success: function(data) {
                    if (data.status == 'success') {
                        let contentDiv = $('#table_content');
                        getGatewaysData();
                        toastr.success(data.message);

                        $('.payment_gateways_modal').modal('hide');
                    } else {
                        toastr.error(data.message);
                        console.log('error-data:', data)
                    }
                },

                error: function(data) {
                    toastr.error(data.message);
                    console.log('error-data:', data)
                }
            });

        })




    })

    function getGatewaysData() {
        $.ajax({
            url: '/gateway-data',
            type: 'GET',
            success: function(data) {
                if (data.status == 'success') {
                    console.log('data:', data)
                    let contentDiv = $('#table_content');
                    let tr = `<tr>
                                    <td>${data.data.provider}</td>
                                    <td>${data.data.mpesa_shortcode}</td>
                                    <td>${data.data.mpesa_shortcode_type}</td>
                              
                                </tr>`;
                    contentDiv.append(tr);
                    $('#payment_gateways_table').DataTable();
                    $('.payment_gateways_modal').modal('hide');
                } else {
                    toastr.error(data.message);
                    console.log('error-data:', data)
                }
            },

            error: function(data) {
                toastr.error(data.message);
                console.log('error-data:', data)
            }
        });
    }
</script>
@endsection