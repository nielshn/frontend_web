<?php

namespace App\Services;

use App\Repositories\WebRepositories;
use Illuminate\Support\Facades\Http;

class WebService
{
    protected $webRepo;

    public function __construct(WebRepositories $webRepo)
    {
        $this->webRepo = $webRepo;
    }


    public function getById($token, $id = 1)
    {
        return $this->webRepo->getById($token, $id);
    }

    public function update($token, $id, $data)
    {
        return $this->webRepo->update($token, $id, $data);
    }

     public function getBy($id = 1)
    {
        return $this->webRepo->getBy($id);
    }

}
