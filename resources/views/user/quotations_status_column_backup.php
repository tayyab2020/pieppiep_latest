<td>

                                                                    @if($key->status == 3)

                                                                        @if($key->received)

                                                                            <span class="btn btn-success">{{__('text.Goods Received')}}</span>

                                                                        @elseif($key->delivered)

                                                                            <span class="btn btn-success">{{__('text.Goods Delivered')}}</span>

                                                                        @else

                                                                            <span class="btn btn-success">{{__('text.Invoice Generated')}}</span>

                                                                        @endif

                                                                    @elseif($key->status == 2)

                                                                        @if($key->accepted)

                                                                            @if($key->processing)

                                                                                <span class="btn btn-success">{{__('text.Order Processing')}}</span>

                                                                            @elseif($key->finished)

                                                                                @if(Auth::guard('user')->user()->role_id == 2)

                                                                                    @if($key->customer_received)

                                                                                        <span class="btn btn-success">{{__('text.Received')}}</span>

                                                                                    @elseif($key->retailer_delivered)

                                                                                        <span class="btn btn-success">{{__('text.Delivered')}}</span>

                                                                                    @else

                                                                                        <?php $data = $key->orders->unique('supplier_id'); $filteredData = $data->reject(function ($value, $key) {
                                                                                            return $value['approved'] != 1;
                                                                                        }); ?>

                                                                                        @if($filteredData->count() === $data->count())

                                                                                            @if($data->contains('delivered',1))

                                                                                                <?php $filteredData2 = $data->reject(function ($value, $key) {
                                                                                                    return $value['delivered'] !== 1;
                                                                                                }); ?>

                                                                                                @if($filteredData2->count() === $data->count())

                                                                                                    <span class="btn btn-success">{{__('text.Delivered by supplier(s)')}}</span>

                                                                                                @elseif($filteredData2->count() == 0)

                                                                                                    <span class="btn btn-success">{{__('text.Confirmed by supplier(s)')}}</span>

                                                                                                @else

                                                                                                    <span class="btn btn-success">{{$filteredData2->count()}}/{{$data->count()}} {{__('text.Delivered Order')}}</span>

                                                                                                @endif

                                                                                            @else

                                                                                                <span class="btn btn-success">{{__('text.Confirmed by supplier(s)')}}</span>

                                                                                            @endif

                                                                                        @elseif($filteredData->count() == 0)

                                                                                            <span class="btn btn-warning">{{__('text.Confirmation Pending')}}</span>

                                                                                        @else

                                                                                            <span class="btn btn-success">{{$filteredData->count()}}/{{$data->count()}} {{__('text.Confirmed')}}</span>

                                                                                        @endif

                                                                                    @endif

                                                                                @else

                                                                                    @if($key->data_processing)

                                                                                        <span class="btn btn-warning">{{__('text.Processing')}}</span>

                                                                                    @elseif($key->data_delivered)

                                                                                        <span class="btn btn-success">{{__('text.Order Delivered')}}</span>

                                                                                    @elseif($key->data_approved)

                                                                                        <span class="btn btn-success">{{__('text.Order Confirmed')}}</span>

                                                                                    @else

                                                                                        <span class="btn btn-warning">{{__('text.Confirmation Pending')}}</span>

                                                                                    @endif

                                                                                @endif

                                                                            @else

                                                                                @if(!$key->quote_request_id)

                                                                                    <span class="btn btn-primary1">{{__('text.Quotation Accepted')}}</span>

                                                                                @else

                                                                                    @if($key->paid)

                                                                                        <span class="btn btn-success">{{__('text.Paid')}}</span>

                                                                                    @else

                                                                                        <span class="btn btn-primary1">{{__('text.Payment Pending')}}</span>

                                                                                    @endif

                                                                                @endif

                                                                            @endif

                                                                        @else

                                                                            <span class="btn btn-success">{{__('text.Closed')}}</span>

                                                                        @endif

                                                                    @else

                                                                        @if($key->ask_customization)

                                                                            <span class="btn btn-info">{{__('text.Asking for Review')}}</span>

                                                                        @elseif($key->approved)

                                                                            <span class="btn btn-success">{{__('text.Quotation Sent')}}</span>

                                                                        @else

                                                                            @if($key->processing)

                                                                                <span class="btn btn-success">{{__('text.Order Processing')}}</span>

                                                                            @elseif($key->quote_request_id && $key->admin_quotation_sent)

                                                                                <span class="btn btn-info">{{__('text.Waiting For Approval')}}</span>

                                                                            @elseif($key->draft)

                                                                                <span class="btn btn-info">{{__('text.Draft')}}</span>

                                                                            @else

                                                                                <span class="btn btn-warning">{{__('text.Pending')}}</span>

                                                                            @endif

                                                                        @endif

                                                                    @endif

                                                                </td>