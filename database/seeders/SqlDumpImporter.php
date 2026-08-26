<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SqlDumpImporter
{
    /**
     * Import data from a phpMyAdmin-style SQL dump into a table.
     *
     * Only the INSERT statements for the table are executed; any schema
     * (CREATE TABLE), ALTER, or control statements are ignored. The target
     * table is truncated first so the seeder behaves as a full overwrite.
     */
    public static function import(string $table, string $fileName): void
    {
        $path = database_path('seeders/sql/' . $fileName);

        if (!File::exists($path)) {
            return;
        }

        $sql = File::get($path);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table($table)->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $parts = preg_split('/\nINSERT INTO /i', $sql);

        foreach ($parts as $index => $part) {
            if ($index === 0) {
                continue;
            }

            // Strip any trailing schema/control statements that may follow
            // the INSERT block (ALTER TABLE, COMMIT, comments, SET, etc.).
            $part = preg_replace('/\n(ALTER TABLE|COMMIT|--|\/\*!|\/\*|SET |START TRANSACTION).*$/is', '', $part);
            $part = trim($part);

            if ($part === '') {
                continue;
            }

            DB::statement('INSERT INTO ' . $part);
        }
    }
}
