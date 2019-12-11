<?php

use App\Models\Company;
use App\Models\Rpi;
use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Truncate Companies Table...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('companies')->truncate();
        
        $this->createCompanies();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }


    public function createCompanies(){
        $this->command->info('Create Companies:');
        
        Company::create([
            'id' => '1',
            'rpi_id' => '1',
            'user_seller_id' => '2',
            'name' => 'Companie-Develop',
            'phone' => '5521965536174@c.us',
            'CNPJ' => '07367014196',
            'description' => 'Companie Develop',
        ]);
        $this->command->info('Created Companie-Develop');
    }    
}
            