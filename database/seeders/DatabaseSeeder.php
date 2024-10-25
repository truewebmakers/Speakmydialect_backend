<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'fname' => 'admin',
            'lname' => 'admin',
            'username' =>'admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('admin@123'),
            'language' => 'urdu',
            'second_language' => 'english',
            'department' => 'seed'
        ]);
        $this->InsertCountryData();
        $this->InsertLanguageData();
        $this->InsertTimezoneData();
    }

    public function InsertCountryData(){
        $sqlDumpPath = __DIR__ . '/countries.sql';
        $sql = file_get_contents($sqlDumpPath);
        DB::unprepared($sql);
    }
    public function InsertLanguageData(){
        $sqlDumpPath = __DIR__ . '/language.sql';
        $sql = file_get_contents($sqlDumpPath);
        DB::unprepared($sql);
    }
    public function InsertTimezoneData(){
        $sqlDumpPath = __DIR__ . '/timezone.sql';
        $sql = file_get_contents($sqlDumpPath);
        DB::unprepared($sql);
    }




}
