<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel Extends Model{
    protected $table = "admin";
    protected $primaryKey = "email";
    protected $allowedFields = ["username", "password", "fullname", "token"];

    public function getData($parameter){
        $builder = $this->table($this->table);
        $builder->where('username', $parameter);
        $builder->orWhere('email', $parameter);
        $query = $builder->get();
        return $query->getRowArray();
    }

    public function UpdateData($data){
        $builder = $this->table($this->table);
        if($builder->save($data)){
            return true;
        }else{
            return false;
        }
    }
}