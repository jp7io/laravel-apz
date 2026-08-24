<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DbTruncate extends Command
{
    protected $signature = 'db:truncate';

    protected $description = 'Empty every table but migrations. Local and testing only';

    /** SQLite keeps its autoincrement counters in a table truncate cannot touch. */
    private const SKIP = ['migrations', 'sqlite_sequence'];

    public function handle(): int
    {
        if (! $this->laravel->environment(['local', 'testing'])) {
            $this->error('Refusing to truncate outside local and testing.');

            return self::FAILURE;
        }

        $tables = collect(Schema::getTableListing())
            ->map(fn (string $table): string => Str::afterLast($table, '.'))
            ->reject(fn (string $table): bool => in_array($table, self::SKIP, true));

        Schema::withoutForeignKeyConstraints(function () use ($tables): void {
            $tables->each(fn (string $table) => DB::table($table)->truncate());
        });

        $this->info("Truncated {$tables->count()} tables.");

        return self::SUCCESS;
    }
}
