<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\new_quotations;
use App\all_invoices;
use App\new_orders;
use App\User;

class RenameDocumentsFolders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rename-documents-folders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rename all documents folders from using user id to organization id';

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
        $retailers = User::where("role_id",2)->where("main_id",NULL)->withTrashed()->get();
        
        foreach($retailers as $key)
        {
            $retailer_id = $key->id;

            if($key->organization)
            {
                $organization_id = $key->organization->id;

                $retailer_quotations_folder_path = public_path() . '/assets/newQuotations/' . $retailer_id;
                $retailer_drafts_folder_path = public_path() . '/assets/draftQuotations/' . $retailer_id;
                $customer_quotations_folder_path = public_path() . '/assets/CustomerQuotations/' . $retailer_id;
                $retailer_orders_folder_path = public_path() . '/assets/Orders/' . $retailer_id;
                $commission_invoices_folder_path = public_path() . '/assets/CommissionInvoices/' . $retailer_id;
                $invoices_folder_path = public_path() . '/assets/newInvoices/' . $retailer_id;
                $negative_invoices_folder_path = public_path() . '/assets/newNegativeInvoices/' . $retailer_id;
    
                $new_retailer_quotations_folder_path = public_path() . '/assets/newQuotations/' . $organization_id . '_a';
                $new_retailer_drafts_folder_path = public_path() . '/assets/draftQuotations/' . $organization_id . '_a';
                $new_customer_quotations_folder_path = public_path() . '/assets/CustomerQuotations/' . $organization_id . '_a';
                $new_retailer_orders_folder_path = public_path() . '/assets/Orders/' . $organization_id . '_a';
                $new_commission_invoices_folder_path = public_path() . '/assets/CommissionInvoices/' . $organization_id . '_a';
                $new_invoices_folder_path = public_path() . '/assets/newInvoices/' . $organization_id . '_a';
                $new_negative_invoices_folder_path = public_path() . '/assets/newNegativeInvoices/' . $organization_id . '_a';
                
                if (file_exists($retailer_quotations_folder_path)) {
                    rename($retailer_quotations_folder_path, $new_retailer_quotations_folder_path);
                }
    
                if (file_exists($retailer_drafts_folder_path)) {
                    rename($retailer_drafts_folder_path, $new_retailer_drafts_folder_path);
                }
    
                if (file_exists($customer_quotations_folder_path)) {
                    rename($customer_quotations_folder_path, $new_customer_quotations_folder_path);
                }
    
                if (file_exists($retailer_orders_folder_path)) {
                    rename($retailer_orders_folder_path, $new_retailer_orders_folder_path);
                }
    
                if (file_exists($commission_invoices_folder_path)) {
                    rename($commission_invoices_folder_path, $new_commission_invoices_folder_path);
                }

                if (file_exists($invoices_folder_path)) {
                    rename($invoices_folder_path, $new_invoices_folder_path);
                }

                if (file_exists($negative_invoices_folder_path)) {
                    rename($negative_invoices_folder_path, $new_negative_invoices_folder_path);
                }
            }
        }

        foreach($retailers as $key)
        {
            if($key->organization)
            {
                $organization_id = $key->organization->id;

                $retailer_quotations_folder_path = public_path() . '/assets/newQuotations/' . $organization_id . '_a';
                $retailer_drafts_folder_path = public_path() . '/assets/draftQuotations/' . $organization_id . '_a';
                $customer_quotations_folder_path = public_path() . '/assets/CustomerQuotations/' . $organization_id . '_a';
                $retailer_orders_folder_path = public_path() . '/assets/Orders/' . $organization_id . '_a';
                $commission_invoices_folder_path = public_path() . '/assets/CommissionInvoices/' . $organization_id . '_a';
                $invoices_folder_path = public_path() . '/assets/newInvoices/' . $organization_id . '_a';
                $negative_invoices_folder_path = public_path() . '/assets/newNegativeInvoices/' . $organization_id . '_a';
    
                $new_retailer_quotations_folder_path = public_path() . '/assets/newQuotations/' . $organization_id;
                $new_retailer_drafts_folder_path = public_path() . '/assets/draftQuotations/' . $organization_id;
                $new_customer_quotations_folder_path = public_path() . '/assets/CustomerQuotations/' . $organization_id;
                $new_retailer_orders_folder_path = public_path() . '/assets/Orders/' . $organization_id;
                $new_commission_invoices_folder_path = public_path() . '/assets/CommissionInvoices/' . $organization_id;
                $new_invoices_folder_path = public_path() . '/assets/newInvoices/' . $organization_id;
                $new_negative_invoices_folder_path = public_path() . '/assets/newNegativeInvoices/' . $organization_id;
                
                if (file_exists($retailer_quotations_folder_path)) {
                    rename($retailer_quotations_folder_path, $new_retailer_quotations_folder_path);
                }
    
                if (file_exists($retailer_drafts_folder_path)) {
                    rename($retailer_drafts_folder_path, $new_retailer_drafts_folder_path);
                }
    
                if (file_exists($customer_quotations_folder_path)) {
                    rename($customer_quotations_folder_path, $new_customer_quotations_folder_path);
                }
    
                if (file_exists($retailer_orders_folder_path)) {
                    rename($retailer_orders_folder_path, $new_retailer_orders_folder_path);
                }
    
                if (file_exists($commission_invoices_folder_path)) {
                    rename($commission_invoices_folder_path, $new_commission_invoices_folder_path);
                }

                if (file_exists($invoices_folder_path)) {
                    rename($invoices_folder_path, $new_invoices_folder_path);
                }

                if (file_exists($negative_invoices_folder_path)) {
                    rename($negative_invoices_folder_path, $new_negative_invoices_folder_path);
                }
            }

            $suppliers = User::where("role_id",4)->where("main_id",NULL)->withTrashed()->get();
        
            foreach($suppliers as $key)
            {
                $user_id = $key->id;

                if($key->organization)
                {
                    $organization_id = $key->organization->id;

                    $supplier_orders_folder_path = public_path() . '/assets/supplierQuotations/' . $user_id;
                    $supplier_approved_orders_folder_path = public_path() . '/assets/supplierApproved/' . $user_id;
        
                    $new_supplier_orders_folder_path = public_path() . '/assets/supplierQuotations/' . $organization_id . '_a';
                    $new_supplier_approved_orders_folder_path = public_path() . '/assets/supplierApproved/' . $organization_id . '_a';
                    
                    if (file_exists($supplier_orders_folder_path)) {
                        rename($supplier_orders_folder_path, $new_supplier_orders_folder_path);
                    }

                    if (file_exists($supplier_approved_orders_folder_path)) {
                        rename($supplier_approved_orders_folder_path, $new_supplier_approved_orders_folder_path);
                    }
                }
            }

            foreach($suppliers as $key)
            {
                if($key->organization)
                {
                    $organization_id = $key->organization->id;

                    $supplier_orders_folder_path = public_path() . '/assets/supplierQuotations/' . $organization_id . '_a';
                    $supplier_approved_orders_folder_path = public_path() . '/assets/supplierApproved/' . $organization_id . '_a';
        
                    $new_supplier_orders_folder_path = public_path() . '/assets/supplierQuotations/' . $organization_id;
                    $new_supplier_approved_orders_folder_path = public_path() . '/assets/supplierApproved/' . $organization_id;
                    
                    if (file_exists($supplier_orders_folder_path)) {
                        rename($supplier_orders_folder_path, $new_supplier_orders_folder_path);
                    }

                    if (file_exists($supplier_approved_orders_folder_path)) {
                        rename($supplier_approved_orders_folder_path, $new_supplier_approved_orders_folder_path);
                    }
                }
            }
        }
    }
}
