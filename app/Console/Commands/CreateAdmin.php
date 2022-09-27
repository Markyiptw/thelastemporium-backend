<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin {username} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Admin::create([
            'username' => $this->argument('username'),
            'password' => Hash::make($this->argument('password')),
        ]);
    }
}
