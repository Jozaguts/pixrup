<?php

namespace App\Console\Commands;

use App\Infrastructure\Billing\StripeCatalogImporter;
use Illuminate\Console\Command;

class BillingStripeImportCommand extends Command
{
    protected $signature = 'billing:stripe-import';

    protected $description = 'Import Stripe products and prices into the local billing catalog';

    public function handle(StripeCatalogImporter $importer): int
    {
        $counts = $importer->importAll();

        $this->info('Stripe catalog import completed.');
        $this->line(sprintf('Products created: %d', $counts['products_created']));
        $this->line(sprintf('Products updated: %d', $counts['products_updated']));
        $this->line(sprintf('Prices created: %d', $counts['prices_created']));
        $this->line(sprintf('Prices updated: %d', $counts['prices_updated']));

        return self::SUCCESS;
    }
}
