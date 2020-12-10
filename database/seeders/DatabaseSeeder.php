<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Poles
        DB::statement(" INSERT INTO `poles` (`id`, `abr`, `nom`) VALUES
            (1, 'TR', 'TRANSPORT'),
            (2, 'BV', 'BV'),
            (3, 'AMAH', 'AMAH'),
            (4, 'AH', 'AH'),
            (5, 'EE', 'EE');
        ");
        // Divisions
        DB::statement("INSERT INTO `divisions` (`id`, `nom`, `pole_id`) VALUES
            (1, 'OA', 1),
            (2, 'VRD', 2),
            (3, 'AT', 1),
            (4, 'DAR', 1),
            (5, 'PM', 1),
            (6, 'RT', 1),
            (7, 'BATIMENTS', 2),
            (9, 'APM', 3),
            (10, 'AHDR', 3),
            (11, 'ASS', 5),
            (12, 'ENV', 5),
            (13, 'AT', 5),
            (14, 'REAU', 4),
            (15, 'BCHE', 4),
            (16, 'EP', 5);
        ");
        // Roles
        DB::statement("INSERT INTO roles (`nom`) VALUES
            ('SuperUser'),
            ('Admin'),
            ('CA'),
            ('CF');
        ");
        // Users
        DB::statement("INSERT INTO users (`name`, `email`, `password`, `role_id`) VALUES
        ('Super User', 'su@cid.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
        ('Admin', 'admin@cid.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2);
        ");
    }
}
