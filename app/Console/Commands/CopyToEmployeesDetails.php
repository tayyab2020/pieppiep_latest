<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\employees_details;

class CopyToEmployeesDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy-to-employees-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy employees data from users table to employees details table';

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
        $employees = User::where("role_id","!=",3)->withTrashed()->get();

        foreach($employees as $key)
        {
            $employee_detail = new employees_details;
            $employee_detail->user_id = $key->id;
            $employee_detail->contract = "Employee";
            $employee_detail->name = $key->name;
            $employee_detail->email = $key->email;
            $employee_detail->postcode = $key->postcode;
            $employee_detail->city = $key->city;
            $employee_detail->phone = $key->phone;
            $employee_detail->address = $key->address;
            $employee_detail->profile_type = 1;
            $employee_detail->personal_number = $key->personal_number;
            $employee_detail->business_name = $key->business_name;
            $employee_detail->tax_number = $key->tax_number;
            $employee_detail->bank_account = $key->bank_account;
            $employee_detail->save();
        }
    }
}
