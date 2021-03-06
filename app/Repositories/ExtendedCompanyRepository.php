<?php

namespace App\Repositories;

use App\Models\Company;
use CreateTrackingTable;
use CreateSalesTable;

/**
 * Class CompanyRepository
 * @package App\Repositories
 * @version September 27, 2019, 5:24 pm UTC
 *
 * @method Company findWithoutFail($id, $columns = ['*'])
 * @method Company find($id, $columns = ['*'])
 * @method Company first($columns = ['*'])
*/
class ExtendedCompanyRepository extends CompanyRepository
{
    
    public function allBySeller(int $seller_id = null)
    {
        $Attentands = $this->findWhere(['user_seller_id' => $seller_id]);
    
        return $Attentands;
    }

    public function getCompany(int $company_id)
    {
        $Company = $this->findWhere(['id' => $company_id]);
        return $Company;
    }

    /*public function createCompanyQueueTable(int $company_id)
    {
        try {
            $chatsMigrationsDir = __DIR__.'/../../database/migrations/2020_03_17_172912_create_queue_table.php';
            require_once($chatsMigrationsDir);

            $queueTable = new CreateQueueTable();
            $queueTable->up('queue_'.(string)$company_id);
        } catch (\Throwable $th) {
            print("Erro creating Company Queue Table...! " + $th);
            throw $th;
        }
    }*/


    public function createTrackingTable(int $company_id)
    {
        try {
            $migrationsDir = __DIR__.'/../../database/migrations/2020_04_09_181218_create_tracking_table.php';
            require_once($migrationsDir);

            $table = new CreateTrackingTable();
            $table->up((string)$company_id);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function createBlingTable(int $company_id)
    {
        try {
            $migrationsDir = __DIR__.'/../../database/migrations/2020_02_14_104111_create_sales_table.php';
            require_once($migrationsDir);

            $table = new CreateSalesTable();
            $table->up((string)$company_id);
        } catch (\Throwable $th) {
            throw $th;
        }
    }



    /**
     * Configure the Model
     **/
    public function model()
    {
        return Company::class;
    }
}
