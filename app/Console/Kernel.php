<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\LinkDefaultFeatures::class,
        Commands\FixNegativeInvoices::class,
        Commands\CheckReceived::class,
        Commands\CopyDocuments::class,
        Commands\CreateCustomersProject::class,
        Commands\customerAddressStreetUpdate::class,
        Commands\PropagateLedgers::class,
        Commands\ProductFeaturesUpdate::class,
        Commands\SubFeaturesStatusUpdate::class,
        Commands\CopyToOrganizations::class,
        Commands\CopyToEmployeesDetails::class,
        Commands\CopyToCustomersDetails::class,
        Commands\CopySupplierAccountShowToOrganizations::class,
        Commands\CopySOidToSidQuotationsData::class,
        Commands\CopySOidToSidOrdersData::class,
        Commands\CopySOidToSidInvoicesData::class,
        Commands\OtherSuppliersFix::class,
        Commands\PrestashopProductsSuppliers::class,
        Commands\RenameDocumentsFolders::class,
        Commands\RetailersRequestsFix::class,
        Commands\SupplierCategoriesFix::class,
        Commands\SupplierOrganizationPlannings::class,
        Commands\ProductsOrganization::class,
        Commands\CustomerQuoteCounter::class,
        Commands\CopyRetailerTerminals::class,
        Commands\CopyROidToRidHandymanQuotes::class,
        Commands\DefaultModelsIds::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('check-received:cron')
            ->everyMinute();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
