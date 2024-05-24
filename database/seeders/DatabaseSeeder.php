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

        // User::factory()->create([
        //     'fname' => 'Test',
        //     'lname' => 'User',
        //     'username' =>'testq',
        //     'email' => 'test@example.com',
        // ]);
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
