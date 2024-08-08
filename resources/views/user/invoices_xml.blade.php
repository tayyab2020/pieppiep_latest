<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
 
<Reeleezee xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">

    @foreach($data as $key)
        <Export>
            <ExportInfo>
                <Name>Pieppiep Export Verkoopfacturen</Name>
                <Source>pieppiep</Source>
            </ExportInfo>
            <CustomerList>
                <Customer ID="">
                    <ID></ID>
                    <FullName>{{$key->business_name}}</FullName>
                    <SearchName>{{$key->business_name}}</SearchName>
                    <Code></Code>
                </Customer>
            </CustomerList>
    
            <SalesInvoiceList>
                <SalesInvoice ReferenceNumber="">
                    <ReferenceNumber></ReferenceNumber>
                    <CustomerReference ID="{{$key->external_relation_number}}"/>
                    <DocumentDate>{{date('Y-m-d', strtotime($key->created_at))}}</DocumentDate>
                    <BookDate></BookDate>
                    <PaymentDueDate></PaymentDueDate>
                    <IsVatIncludedInPrice>Yes</IsVatIncludedInPrice>
                    <Status></Status>
                    <Header></Header>
                    <PaymentReference></PaymentReference>
                    <LineList>
                        <Line>
                            @foreach($key->data as $temp)
                                <Date>{{date('Y-m-d', strtotime($key->created_at))}}</Date>
                                <Description>{{$temp->description}}</Description>
                                <Quantity>{{number_format((float)$temp->qty, 2, ',', '.')}}</Quantity>
                                <Price>{{$temp->negative_invoice ? "-".number_format((float)$temp->amount, 2, ',', '.') : number_format((float)$temp->amount, 2, ',', '.')}}</Price>
                                <VatCode></VatCode>
                                <AccountNumber></AccountNumber>
                            @endforeach
                        </Line>
                    </LineList>
                </SalesInvoice>
            </SalesInvoiceList>
        </Export>
    @endforeach

</Reeleezee>