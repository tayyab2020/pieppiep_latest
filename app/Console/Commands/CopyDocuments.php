<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\new_quotations;
use App\all_invoices;
use App\new_orders;
use App\User;

class CopyDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $quotations = new_quotations::withTrashed()->get();
        
        foreach($quotations as $key)
        {
            $creator_id = $key->creator_id;

            $quotation_invoice_number = $key->quotation_invoice_number;

            $filename = $quotation_invoice_number . '.pdf';

            $retailer_quotations_folder_path = public_path() . '/assets/newQuotations/' . $creator_id;
            $retailer_drafts_folder_path = public_path() . '/assets/draftQuotations/' . $creator_id;
            $customer_quotations_folder_path = public_path() . '/assets/CustomerQuotations/' . $creator_id;
            $retailer_orders_folder_path = public_path() . '/assets/Orders/' . $creator_id;
            $commission_invoices_folder_path = public_path() . '/assets/CommissionInvoices/' . $creator_id;
            
            if (!file_exists($retailer_quotations_folder_path)) {
                mkdir($retailer_quotations_folder_path, 0775, true);
            }

            if (!file_exists($retailer_drafts_folder_path)) {
                mkdir($retailer_drafts_folder_path, 0775, true);
            }

            if (!file_exists($customer_quotations_folder_path)) {
                mkdir($customer_quotations_folder_path, 0775, true);
            }

            if (!file_exists($retailer_orders_folder_path)) {
                mkdir($retailer_orders_folder_path, 0775, true);
            }

            if (!file_exists($commission_invoices_folder_path)) {
                mkdir($commission_invoices_folder_path, 0775, true);
            }

            $file = public_path() . '/assets/newQuotations/' . $filename;

            if (file_exists($file)) {
                copy($file, $retailer_quotations_folder_path.'/'.$filename);
            }

            $file1 = public_path() . '/assets/draftQuotations/' . $filename;

            if (file_exists($file1)) {
                copy($file1, $retailer_drafts_folder_path.'/'.$filename);
            }

            $file2 = public_path() . '/assets/CustomerQuotations/' . $filename;

            if (file_exists($file2)) {
                copy($file2, $customer_quotations_folder_path.'/'.$filename);
            }

            $file3 = public_path() . '/assets/Orders/' . $filename;

            if (file_exists($file3)) {
                copy($file3, $retailer_orders_folder_path.'/'.$filename);
            }

            $filename1 = $key->commission_invoice_number . '.pdf';
            $file4 = public_path() . '/assets/CommissionInvoices/' . $filename1;

            if (file_exists($file4)) {
                copy($file4, $commission_invoices_folder_path.'/'.$filename1);
            }
        }

        $orders = new_orders::withTrashed()->get();
        
        foreach($orders as $key)
        {
            $user_id = $key->supplier_id;
            $order_number = $key->order_number;

            $filename = $order_number . '.pdf';

            $supplier_orders_folder_path = public_path() . '/assets/supplierQuotations/' . $user_id;
            $supplier_approved_orders_folder_path = public_path() . '/assets/supplierApproved/' . $user_id;
            
            if (!file_exists($supplier_orders_folder_path)) {
                mkdir($supplier_orders_folder_path, 0775, true);
            }

            if (!file_exists($supplier_approved_orders_folder_path)) {
                mkdir($supplier_approved_orders_folder_path, 0775, true);
            }

            $file = public_path() . '/assets/supplierQuotations/' . $filename;

            if (file_exists($file)) {
                copy($file, $supplier_orders_folder_path.'/'.$filename);
            }

            $file1 = public_path() . '/assets/supplierApproved/' . $filename;

            if (file_exists($file1)) {
                copy($file1, $supplier_approved_orders_folder_path.'/'.$filename);
            }
        }

        $invoices = all_invoices::withTrashed()->get();
        
        foreach($invoices as $key)
        {
            $creator_id = $key->creator_id;
            $invoice_number = $key->invoice_number;

            $filename = $invoice_number . '.pdf';

            $invoices_folder_path = public_path() . '/assets/newInvoices/' . $creator_id;
            $negative_invoices_folder_path = public_path() . '/assets/newNegativeInvoices/' . $creator_id;
            
            if (!file_exists($invoices_folder_path)) {
                mkdir($invoices_folder_path, 0775, true);
            }

            if (!file_exists($negative_invoices_folder_path)) {
                mkdir($negative_invoices_folder_path, 0775, true);
            }

            $file = public_path() . '/assets/newInvoices/' . $filename;

            if (file_exists($file)) {
                copy($file, $invoices_folder_path.'/'.$filename);
            }

            $file1 = public_path() . '/assets/newNegativeInvoices/' . $filename;

            if (file_exists($file1)) {
                copy($file1, $negative_invoices_folder_path.'/'.$filename);
            }
        }
    }
}
