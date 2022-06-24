<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:invoice {--user=} {--agency=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Recurring Invoices.';

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
     * @return mixed
     */
    public function handle()
    {
        $user = $this->option('user');
        $agency = $this->option('agency');
    }
}
