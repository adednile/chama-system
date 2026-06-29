<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chama:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compress, encrypt, and back up the database state to local storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');

        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database', 'chama_db');
        $keyName = "Tables_in_{$dbName}";

        $sqlDump = "-- Chama Gold & Trust Database Backup\n";
        $sqlDump .= "-- Generated: " . Carbon::now()->toDateTimeString() . "\n\n";

        foreach ($tables as $table) {
            if (!isset($table->$keyName)) {
                // If key is different, try to grab first property value
                $props = get_object_vars($table);
                $tableName = reset($props);
            } else {
                $tableName = $table->$keyName;
            }

            // Get structure
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sqlDump .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $createKey = 'Create Table';
            $sqlDump .= $createTable[0]->$createKey . ";\n\n";

            // Get rows
            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $columns = array_keys($rowArray);
                $escapedValues = array_map(function ($val) {
                    if (is_null($val)) return 'NULL';
                    return "'" . addslashes($val) . "'";
                }, array_values($rowArray));

                $sqlDump .= "INSERT INTO `{$tableName}` (`" . implode("`, `", $columns) . "`) VALUES (" . implode(", ", $escapedValues) . ");\n";
            }
            $sqlDump .= "\n\n";
        }

        // Encryption logic (AES-256)
        $passphrase = config('app.key', 'SomeRandomKeyThatIsLongEnough');
        $encryptedDump = openssl_encrypt($sqlDump, 'aes-256-cbc', $passphrase, 0, substr(md5($passphrase), 0, 16));

        $fileName = 'backup-' . Carbon::now()->format('Y-m-d-H-i-s') . '.enc';
        Storage::disk('local')->put('backups/' . $fileName, $encryptedDump);

        $this->info("Backup created successfully: storage/app/backups/{$fileName}");
    }
}
