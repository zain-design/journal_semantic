<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use CodeIgniter\Exceptions\AlertError;
use CodeIgniter\Validation\StrictRules\Rules;

// use CodeIgniter\Config\Config;

class Akun extends BaseController
{
    function __construct()
    {
        $this->validation = \config\Services::validation();
        $this->m_admin = new AdminModel();
        helper('global_fungsi_helper');
        $this->halaman_controller = 'akun';
        $this->halaman_label = 'akun';
    }

    function index()
    {
        $data = [];

        if ($this->request->getMethod() == 'post') {
            $data = $this->request->getVar();

            $fullname = $this->request->getVar('fullname');
            $password_lama = $this->request->getVar('password_lama');
            $password_baru = $this->request->getVar('password_baru');
            $password_baru_konfirmasi = $this->request->getVar('password_baru_konfirmasi');

            $aturan = [
                'fullname' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'nama lengkap harus diisi'
                    ]
                ]

            ];
            // if ($password_baru != '') {
            $aturan = [
                'password_lama' => [
                    'rules' => 'required|old_password[password_lama]',
                    'errors' => [
                        'required' => 'password lama harus diisi',
                        'old_password' => 'password lama tidak sesuai'
                    ],
                ],
                'password_baru' => [
                    'rules' => 'min_length[5]|alpha_numeric',
                    'errors' => [

                        'min_length' => 'password minimal 5 karakter',
                        'alpha_numeric' => 'hanya huruf dan angka yang diterima'
                    ],
                ],
                'password_baru_konfirmasi' => [
                    'rules' => 'matches[password_baru]',
                    'errors' => [
                        'match' => 'konfirmasi password tidak sesuai'
                    ]
                ]
            ];
            // }

            if (!$this->validate($aturan)) {
                session()->setFlashdata('warning', $this->validation->getErrors());
            } else {
                $dataUpdate = [
                    'email' => session()->get('akun_email'),
                    'fullname' => $fullname,
                ];
                $this->m_admin->UpdateData($dataUpdate);

                $sesi = [
                    'akun_fullname' => $fullname
                ];

                $password_baru = password_hash($password_baru, PASSWORD_DEFAULT);
                $dataUpdate = [
                    'email' => session()->get('akun_email'),
                    'password' => $password_baru

                ];
                $this->m_admin->UpdateData($dataUpdate);
                session()->set($sesi);
            }
            session()->setFlashdata('success', 'data telah berhasil diubah');

            return redirect()->to('admin/' . $this->halaman_controller);
        }
        $username = session()->get('akun_username');
        $data = $this->m_admin->getData($username);

        $data['templateJudul'] = "halaman " . $this->halaman_label;
        echo view('admin/v_admin_header', $data);
        echo view('admin/v_akun', $data);
        echo view('admin/v_admin_footer', $data);
    }
}
