<?php

namespace App\Http\Controllers\Db\Tables;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use function response;

class Users extends Controller
{
    public function createTableUsers()
    {
        $table = 'users';
        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        DB::statement("DROP TABLE IF EXISTS $table");
        DB::statement("
            CREATE TABLE IF NOT EXISTS $table (
                id int UNSIGNED NOT NULL AUTO_INCREMENT,
                client enum ('mweb', 'cab7', 'izgrev') NOT NULL DEFAULT 'mweb',
                email varchar(50) NOT NULL DEFAULT '500.Internal@Server.Error',
                last_email_at timestamp NULL DEFAULT NULL,
                first_name varchar(99) NOT NULL DEFAULT 'Server',
                last_name varchar(99) NOT NULL DEFAULT 'Error',
                verified tinyint(1) UNSIGNED DEFAULT '0',
                active tinyint(1) UNSIGNED DEFAULT '0',
                gdpr tinyint(1) UNSIGNED DEFAULT '0',
                news tinyint(1) UNSIGNED DEFAULT '1',
                api_token varchar(80) NULL DEFAULT NULL,
                password varchar(80) NOT NULL,
                created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY unique_email(email),
                UNIQUE KEY unique_api_token(api_token)
            ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
        ");
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
        DB::insert('
            INSERT INTO users (email, first_name, last_name, verified, active, api_token, password) VALUES (
                "vativa4c@gmail.com", "Tichomir", "Rangelov", 1, 1,
                "KjiCloTj7ICvyWiAtUI08513wOKa0AaEO3ZohDNP9mfRs9RuVml1BTeEF0Xk",
                "$2y$10$rhgxHHWkPhUrvZJZPq04..OPX6e.cuZhgJIhFL.rySm1AXkcOkTUi"
            );
        ');
        return response()->json("Successfully recreated table {$table}", 200);
    }

    public function createTableEmailAuthentications()
    {
        $table = 'email_authentications';
        DB::statement("DROP TABLE IF EXISTS $table");
        DB::statement("CREATE TABLE IF NOT EXISTS $table (
            id int UNSIGNED NOT NULL AUTO_INCREMENT,
            token varchar(60) NOT NULL UNIQUE,
            user_id int UNSIGNED NOT NULL UNIQUE,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at timestamp NULL DEFAULT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;");
        return response()->json("Successfully recreated table {$table}", 200);
    }

    # Dump all referencing child tables on account hard delete
    public function dumpChildTables()
    {
        DB::statement("USE information_schema");
        DB::select("SELECT table_name FROM key_column_usage
            WHERE table_schema = 'cab7' AND referenced_table_name = 'users' AND referenced_column_name = 'id'");
        DB::statement("USE mweb");
    }

}
