<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PostModel;
use CodeIgniter\Exceptions\AlertError;
use CodeIgniter\Validation\StrictRules\Rules;

// use CodeIgniter\Config\Config;

class Socials extends BaseController
{
    function __construct()
    {
        $this->validation = \config\Services::validation();
        $this->m_posts = new PostModel();
        helper('global_fungsi_helper');
        $this->halaman_controller = 'socials';
        $this->halaman_label = 'socials';
    }

    function index()
    {
        $data = [];
        if ($this->request->getMethod() == 'post') {
            $data = $this->request->getVar();
        }



        echo view('admin/v_admin_header', $data);
        echo view('admin/v_socials', $data);
        echo view('admin/v_admin_footer', $data);
    }
}
