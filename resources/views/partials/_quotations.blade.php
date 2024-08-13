@php $invoice_id = $key->invoice_id; @endphp
<div style="margin-right: 5px;" class="dropdown dropdown1">
    <button style="outline: none;position: relative;z-index: 1000;" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
        {{ __('text.Action') }} <span class="caret"></span>
    </button>
    <ul style="z-index: 1001;" class="dropdown-menu">
        @if ($user_role == 2)
            @if ($key->draft)
                <li><a href="{{ url('/aanbieder/approve-draft-quotation/' . $invoice_id) }}">{{ __('text.Approve Draft') }}</a></li>
            @endif

            @if (!$key->quote_request_id)
                <li><a href="{{ url('/aanbieder/copy-new-quotation/' . $invoice_id) }}">{{ __('text.Copy Quotation') }}</a></li>
            @endif

            <li><a class="delete-btn" data-href="{{ url('/aanbieder/delete-new-quotation/' . $invoice_id) }}">{{ __('text.Delete Quotation') }}</a></li>
            <li><a href="{{ url('/aanbieder/messages/' . $invoice_id) }}">{{ __('text.See Messages') }}</a></li>
            <li><a href="{{ url('/aanbieder/sent-emails/' . $invoice_id) }}">{{ __('text.Sent Mails') }}</a></li>
            <li><a href="{{ url('/aanbieder/view-new-quotation/' . $invoice_id) }}">{{ __('text.View Quotation') }}</a></li>

            @if ($key->accepted)
                <li><a href="{{ url('/aanbieder/view-details/' . $invoice_id) }}">{{ __('text.View Details') }}</a></li>
            @endif

            @if (!$key->invoice)
                @if ((!$key->quote_request_id || $key->paid) && !$key->draft)
                    <li><a style="cursor: pointer;" class="create-invoice-btn" data-href="{{ url('/aanbieder/create-new-invoice/' . $invoice_id) }}">{{ __('text.Create Invoice') }}</a></li>
                @endif
            @else
                <li><a href="{{ url('/aanbieder/view-new-invoice/' . $invoice_id) }}">{{ __('text.View Invoice') }}</a></li>
                <li><a href="{{ isset($key->invoices[0]) ? url('/aanbieder/download-invoice-pdf/' . $key->invoices[0]->id) : null }}">{{ __('text.Download Invoice PDF') }}</a></li>
            @endif

            @if ($key->paid)
                <li><a href="{{ url('/aanbieder/download-commission-invoice/' . $invoice_id) }}">{{ __('text.Download Commission Invoice') }}</a></li>
            @endif

            @if ($key->status != 2 && $key->status != 3)
                @if ($key->ask_customization)
                    <li><a onclick="ask(this)" data-text="{{ $key->review_text }}" href="javascript:void(0)">{{ __('text.Review Reason') }}</a></li>
                @endif

                @if (!$key->quote_request_id && !$key->draft)
                    <li><a style="cursor: pointer;" class="accept-btn" data-href="{{ url('/aanbieder/accept-new-quotation/' . $invoice_id) }}">{{ __('text.Accept') }}</a></li>
                @endif
            @endif

            @if ($key->accepted && !$key->finished)
                <li><a href="{{ url('/aanbieder/discard-quotation/' . $invoice_id) }}">{{ __('text.Discard Quotation') }}</a></li>
            @endif

            @if (!$key->quote_request_id || $key->paid)
                @if (count($key->orders) > 0)
                    <li><a href="{{ url('/aanbieder/view-order/' . $invoice_id) }}">{{ __('text.View Order') }}</a></li>
                @endif
            @endif

            @if (!$key->quote_request_id || $key->paid)
                @if ($key->accepted && !$key->processing && !$key->finished)
                    <li><a class="send-new-order" data-id="{{ $invoice_id }}" data-date="{{ $key->delivery_date ? date('d-m-Y', strtotime($key->delivery_date)) : null }}" href="javascript:void(0)">{{ __('text.Send Order') }}</a></li>
                @endif
            @endif

            @if ($key->received && !$key->retailer_delivered)
                <li><a href="{{ url('/aanbieder/retailer-mark-delivered/' . $invoice_id) }}">{{ __('text.Mark as delivered') }}</a></li>
            @endif

            @if ($key->status == 2)
                @if ($key->finished)
                    @foreach ($key->orders->unique('supplier_id') as $data)
                        <li><a href="{{ url('/aanbieder/download-order-pdf/' . $data->id) }}">{{ __('text.Download Supplier (:attribute) Order PDF', ['attribute' => $data->company_name]) }}</a></li>
                    @endforeach
                @endif

                @foreach ($key->orders->unique('supplier_id') as $data)
                    @if ($data->approved)
                        <li><a href="{{ url('/aanbieder/download-order-confirmation-pdf/' . $data->id) }}">{{ __('text.Download Supplier (:attribute) Order Confirmation PDF', ['attribute' => $data->company_name]) }}</a></li>
                    @endif
                @endforeach
            @endif

            <li><a href="{{ url('/aanbieder/download-new-quotation/' . $invoice_id) }}">{{ __('text.Download PDF') }}</a></li>

            @if (!$key->quote_request_id || $key->paid)
                @if (!$key->processing && count($key->orders) > 0)
                    <li><a href="{{ url('/aanbieder/download-full-order-pdf/' . $invoice_id) }}">{{ __('text.Download Full Order PDF') }}</a></li>
                @endif
            @endif

            @if ($key->quote_request_id && !$key->admin_quotation_sent)
                <li><a href="{{ url('/aanbieder/send-quotation-admin/' . $invoice_id) }}">{{ __('text.Send Quotation') }}</a></li>
            @endif

            @if (!$key->quote_request_id)
                <li><a class="send-new-quotation" data-id="{{ $invoice_id }}" href="javascript:void(0)">{{ __('text.Send Quotation') }}</a></li>
            @endif
        @else
            <li><a href="{{ url('/aanbieder/view-order/' . $invoice_id) }}">{{ __('text.View Order') }}</a></li>
            @if (!$key->data_delivered && !$key->data_processing)
                <li><a href="{{ url('/aanbieder/change-delivery-dates/' . $invoice_id) }}">{{ __('text.Edit Delivery Dates') }}</a></li>
            @endif
            @if ($key->data_approved && !$key->data_delivered)
                <li><a href="{{ url('/aanbieder/supplier-order-delivered/' . $invoice_id) }}">{{ __('text.Mark as delivered') }}</a></li>
            @endif
            @if ($key->data_approved)
                <li><a href="{{ url('/aanbieder/download-order-confirmation-pdf/' . $key->data_id) }}">{{ __('text.Download Order Confirmation PDF') }}</a></li>
            @endif
            <li><a href="{{ url('/aanbieder/download-order-pdf/' . $key->data_id) }}">{{ __('text.Download Order PDF') }}</a></li>
        @endif
    </ul>
</div>

@if ($user_role == 2)
    @if (count($key->unseen_messages) > 0)
        <a href="{{ url('/aanbieder/messages/' . $invoice_id) }}">
            <main style="width: 3.5em;height: 3em;" rel="main">
                <div style="width: 100%;height: 100%;" class="notification">
                    <svg viewbox="-10 -2 35 20">
                        <path class="notification--bell" d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.118.418 2.27 1.1 3.19a1 1 0 0 1 .148.704 11.297 11.297 0 0 1-.518 1.904c.466-.112 1.106-.306 1.948-.672z" />
                        <circle class="notification--wave" cx="8" cy="8" r="8" />
                    </svg>
                    <span class="badge badge-warning notify-no">{{ count($key->unseen_messages) }}</span>
                </div>
            </main>
        </a>
    @endif
@endif
