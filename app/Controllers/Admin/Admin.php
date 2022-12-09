<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use CodeIgniter\Config\Config;

class Admin extends BaseController
{
    function __construct()
    {
        $this->m_admin = new AdminModel();
        $this->validation = \config\Services::validation();
        helper('cookie');
        helper('global_fungsi_helper');
    }
    public function login()
    {
        $data = [];

        // if(get_cookie('cookie_username') && get_cookie('cookie_password')){
        //     $username = get_cookie('username');
        //     $password = get_cookie('password');

        //     $dataAkun = $this->m_admin->getData($username);
        //         if($password != $dataAkun['password']){
        //             $err[] = "password yang anda masukkan tidak sesuai.";
        //             session()->setFlashdata('username', $username);
        //             session()->setFlashdata('warning', $err);

        //             delete_cookie('cookie_username');
        //             delete_cookie('cookie_password');
        //             return redirect()->to("admin/login");
        //         };
        //         $akun = [
        //             'akun_username' => $username,
        //             'akun_fullname' => $dataAkun['fullname'],
        //             'akun_email' => $dataAkun['email'],
        //         ];
        //         session()->set($akun);
        //         return redirect()->to("admin/sukses");
        //     }

        if ($this->request->getMethod() == 'post') {
            $rules = [
                'username' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'username harus diisi'
                    ]
                ],
                'password' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'password harus diisi'
                    ]
                ],
            ];
            if (!$this->validate($rules)) {
                session()->setFlashdata("warning", $this->validation->getErrors());
                return redirect()->to("admin/login");
            }
            // else{
            //     session()->setFlashdata("success", "selamat anda berhasil login");
            //     return redirect()->to("login");
            // }
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');
            $remember_me = $this->request->getVar('remember_me');

            // $dataAkun = $this->m_admin->getData($username);
            if ($this->m_admin->getData($username)) {
                $dataAkun = $this->m_admin->getData($username);
            } else {
                $err[] = "username yang anda masukkan tidak sesuai.";
                session()->setFlashdata('username', $username);
                session()->setFlashdata('warning', $err);
                return redirect()->to("admin/login");
            }

            if (!password_verify($password, $dataAkun['password'])) {
                $err[] = "password yang anda masukkan tidak sesuai.";
                session()->setFlashdata('username', $username);
                session()->setFlashdata('warning', $err);
                return redirect()->to("admin/login");
            };


            // if($remember_me == '1'){
            //     set_cookie("cookie_username", $username, 3600*24*30);
            //     set_cookie("cookie_password", $password, 3600*24*30);
            // }

            $akun = [
                'akun_username' => $dataAkun['username'],
                'akun_fullname' => $dataAkun['fullname'],
                'akun_email' => $dataAkun['email'],
            ];
            session()->set($akun);
            return redirect()->to("admin/sukses");
            // ->withCookies();


        }

        return view('admin/v_login', $data);
    }

    function sukses()
    {
        // print_r(session()->get());    
        return redirect()->to("admin/article");
    }

    function logout()
    {
        session()->destroy();
        if (session()->get('akun_username') != '') {
            session()->setFlashdata('success', "berhasil logout");
        }
        return redirect()->to("admin/login");
    }

    function lupapassword()
    {
        $err = [];
        if ($this->request->getMethod() == 'post') {
            $username = $this->request->getVar('username');
            if ($username == '') {
                $err[] = "silakan masukkan username atau email anda";
            }
            if (empty($err)) {
                $data = $this->m_admin->getData($username);
                if (empty($data)) {
                    $err[] = "akun yang kamu masukkan tidak terdata";
                }
            }
            if (empty($err)) {
                $email = $data['email'];
                $token = md5(date('ymdhis'));

                $link = site_url("admin/resetpassword/?email=$email&token=$token");
                $attachment = "";
                $to = $email;
                $title = "reset Password";
                $message = "berikut ini adalah link untuk melakukan reset password anda";
                $message .= "silahkan klik link berikut ini $link";

                kirim_email($attachment, $to, $title, $message);
                // exit();

                $dataUpdate = [
                    'email' => $email,
                    'token' => $token
                ];
                $this->m_admin->updateData($dataUpdate);
                session()->setFlashdata("success", "email recovery sudah dikirimkan");
            }
            if ($err) {
                session()->setFlashdata("username", $username);
                session()->setFlashdata("warning", $err);
            }
            return redirect()->to("admin/lupapassword");
        }
        return view('admin/v_lupapassword');
    }
    function resetpassword()
    {
        $err = [];
        $email = $this->request->getVar('email');
        $token = $this->request->getVar('token');

        if ($email != '' and $token != '') {
            $dataAkun = $this->m_admin->getData($email);
            if ($dataAkun['token'] != $token) {
                $err[] = "token tidak valid";
            };
        } else {
            $err[] = 'parameter yang dikirim tidak valid';
        }
        if ($err) {
            session()->setFlashdata('warning', $err);
        };
        if ($this->request->getMethod() == 'post') {
            $aturan = [
                'password' => [
                    'rules' => 'required|min_length[5]',
                    'errors' => [
                        'required' => 'password harus diisi',
                        'min_length' => 'panjang minimum 5 karakter'
                    ]
                ],
                'konfirmasi_password' => [
                    'rules' => 'required|min_length[5]|matches[password]',
                    'errors' => [
                        'required' => 'konfirmasi password harus diisi',
                        'min_length' => 'panjang minimum 5 karakter',
                        'matches' => 'konfirmasi password tidak identik'

                    ]
                ],
            ];
            if (!$this->validate($aturan)) {
                session()->setFlashdata('warning', $this->validation->getErrors());
            } else {
                $dataUpdate = [
                    'email' => $email,
                    'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
                    'token' => null
                ];
                $this->m_admin->updateData($dataUpdate);
                session()->setFlashdata('success', 'password telah berhasil diubah');
                return redirect()->to('admin/login');
            }
        }
        echo view("admin/v_resetpassword");
    }
}
