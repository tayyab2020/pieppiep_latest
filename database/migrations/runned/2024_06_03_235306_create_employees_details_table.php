<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('contract')->nullable();
            $table->text('contract_number')->nullable();
            $table->text('freelancer_registration_number')->nullable();
            $table->string('business_name')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('function')->nullable();
            $table->string('street')->nullable();
            $table->string('house_number')->nullable();
            $table->string('affix_house_number')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('IBAN_number')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees_details');
    }
}