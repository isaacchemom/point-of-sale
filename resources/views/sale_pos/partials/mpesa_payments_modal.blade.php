<div class="modal fade no-print" id="recent_mpesa_payments_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('Recent M-PESA Payments')</h4>
            </div>
            <div class="modal-body">

                <!-- Add a search bar -->
                <div class="form-group">
                    <input type="text" class="form-control" id="mpesa-search" placeholder="Search transactions">
                </div>

                <!-- Table for displaying transactions -->
                <table class="table table-stripped table-bordered">
                    <thead>
                        <tr>
                            <th>
                                First Name
                            </th>
                            <!-- <th>
                                Last Name
                            </th>
                            <th>
                                Phone
                            </th> -->
                            <th>
                                Amount
                            </th>
                            <th>
                                Transaction
                            </th>
                            <th>
                                Date
                            </th>
                            <th>
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody id='recent_payments_table_body'>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div class="modal fade no-print" id="recent_payment_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <!-- <h4 class="modal-title">@lang('USE DATA')</h4> -->
            </div>
            <div class="modal-body">

                <form action="#">

                    <div id="form_input_container">

                    </div>
                    <button type="button" class="btn btn-primary" id="finalize_c2b_transaction">Finalize</button>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


<div class="modal fade no-print" id="mpesa_c2b_new_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('Recent M-PESA Payments')</h4>
            </div>
            <div class="modal-body">

                <!-- Add a search bar -->
                <div class="form-group">
                    <input type="text" class="form-control" id="mpesa-search" placeholder="Search transactions">
                </div>

                <!-- Table for displaying transactions -->
                <table class="table table-stripped table-bordered">
                    <thead>
                        <tr>
                            <th>
                                First Name:
                            </th>
                            <th>
                                Last Name:
                            </th>
                            <th>
                                Phone:
                            </th>
                            <th>
                                Amount:
                            </th>
                            <th>
                                Transaction ID:
                            </th>
                        </tr>
                    </thead>
                    <tbody id='mpesac2b-tbody'>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>