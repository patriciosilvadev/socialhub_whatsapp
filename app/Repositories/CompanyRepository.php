<?php

namespace App\Repositories;

use App\Models\Company;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CompanyRepository
 * @package App\Repositories
 * @version September 27, 2019, 5:24 pm UTC
 *
 * @method Company findWithoutFail($id, $columns = ['*'])
 * @method Company find($id, $columns = ['*'])
 * @method Company first($columns = ['*'])
*/
class CompanyRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'CNPJ',
        'name',
        'phone',
        'email',
        'whatsapp',
        'description',
        'rpi_id',
        'user_seller_id',

        'CEP',
        'estado',
        'cidade',
        'rua',
        'numero',
        'complemento',
        'bairro',


    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Company::class;
    }
}
