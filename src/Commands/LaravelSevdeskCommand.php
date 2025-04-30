<?php

namespace mindtwo\LaravelSevdesk\Commands;

use Illuminate\Console\Command;
use mindtwo\LaravelSevdesk\Facades\LaravelSevdesk;

class LaravelSevdeskCommand extends Command
{
    public $signature = 'laravel-sevdesk';

    public $description = 'My command';

    public function handle(): int
    {
        $response = LaravelSevdesk::base()
            ->get('/CheckAccount');

        $sevClientId = $response->json('objects.0');

        if (empty($sevClientId)) {
            $this->error('No sevClientId found');

            return self::FAILURE;
        }

        $sevClientId = $sevClientId['sevClient']['id'];

        $this->info('Add the following to your .env file:');
        $this->line('SEVDESK_SEV_USER='.$sevClientId);

        return self::SUCCESS;
    }
}
