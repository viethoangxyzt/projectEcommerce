<?php

namespace App\Repository\Eloquent;

use App\Models\TemporaryAddress;
use App\Repository\AddressRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AddressRepository
 * @package App\Repositories\Eloquent
 */
class TemporaryAddressRepository extends BaseRepository implements AddressRepositoryInterface
{
    /**
     * AddressRepository constructor.
     *
     * @param TemporaryAddress $address
     */
    public function __construct(TemporaryAddress $address)
    {
        parent::__construct($address);
    }

    /**
     * create or update user model in the database.
     * 
     * @param array $attributes
     * @return Model
     */
    public function updateOrCreate(array $attributes): Model
    {
        return $this->model->updateOrCreate($attributes);
    }
}

?>