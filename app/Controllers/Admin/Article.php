<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PostModel;
use CodeIgniter\Exceptions\AlertError;
use CodeIgniter\Validation\StrictRules\Rules;

// use CodeIgniter\Config\Config;

class Article extends BaseController
{
    function __construct()
    {
        $this->validation = \config\Services::validation();
        $this->m_posts = new PostModel();
        helper('global_fungsi_helper');
        $this->halaman_controller = 'article';
        $this->halaman_label = 'article';
    }

    function index()
    {
        $data = [];

        if ($this->request->getVar('aksi') == 'hapus' && $this->request->getVar('post_id')) {
            $dataPost = $this->m_posts->getPost($this->request->getVar('post_id'));
            if ($dataPost['post_id']) {
                @unlink(LOKASI_UPLOAD . '/' . $dataPost['post_thumbnail']);
                $aksi   = $this->m_posts->deletePost($this->request->getVar('post_id'));
                if ($aksi == true) {
                    session()->setFlashdata('success', 'berhasil menghapus data');
                } else {
                    session()->setFlashdata('warning', 'gagal menghapus data');
                }
            }
            return redirect()->to('admin/' . $this->halaman_controller);
        }
        $data['templateJudul'] = 'Halaman ' . $this->halaman_label;
        $post_type = $this->halaman_controller;
        $jumlahBaris = 3;
        $katakunci = $this->request->getVar("katakunci");
        $group_dataset = "dt";
        $hasil = $this->m_posts->listPost($post_type, $jumlahBaris, $katakunci, $group_dataset);

        $data['record'] = $hasil['record'];
        $data['pager'] = $hasil['pager'];
        $data['katakunci'] = $katakunci;

        $currentPage = $this->request->getVar('page_dt');
        $data['nomor'] = nomor($currentPage, $jumlahBaris);


        echo view('admin/v_admin_header', $data);
        echo view('admin/v_article', $data);
        echo view('admin/v_admin_footer', $data);
    }

    function tambah()
    {
        $data = [];

        if ($this->request->getMethod() == 'post') {
            $data = $this->request->getVar();
            $aturan = [
                'post_title' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'title harus diisi'
                    ]
                ],
                'post_content' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'content harus diisi'
                    ]
                ],
                'post_thumbnail' => [
                    'rules' => 'is_image[post_thumbnail]',
                    'errors' => [
                        'is_image' => 'thumbnail harus berupa gambar'
                    ]
                ],
            ];

            $file = $this->request->getFile('post_thumbnail');

            if (!$this->validate($aturan)) {
                session()->setFlashdata('warning', $this->validation->getErrors());
            } else {
                $post_thumbnail = '';
                if ($file->getName()) {
                    $post_thumbnail = $file->getRandomName();
                }
                $record = [
                    'username' => session()->get('akun_username'),
                    'post_title' => $this->request->getVar('post_title'),
                    'post_status' => $this->request->getVar('post_status'),
                    'post_thumbnail' => $post_thumbnail,
                    'post_description' => $this->request->getVar('post_description'),
                    'post_content' => $this->request->getVar('post_content'),
                ];
                $aksi = $this->m_posts->insertPost($record, $post_type = $this->halaman_controller);
                if ($aksi != false) {
                    $page_id = $aksi;
                    if ($file->getName()) {
                        $lokasi_direktori = LOKASI_UPLOAD;
                        $file->move($lokasi_direktori, $post_thumbnail);
                    }
                    session()->setFlashdata('success', 'data berhasil dimasukkan');
                    return redirect()->to('admin/' . $this->halaman_controller . '/tambah');
                } else {
                    session()->setFlashdata('warning', 'data gagal dimasukkan');
                    return redirect()->to('admin/' . $this->halaman_controller . '/tambah');
                }
            }
        }

        echo view('admin/v_admin_header', $data);
        echo view('admin/v_article_tambah', $data);
        echo view('admin/v_admin_footer', $data);
    }

    function edit($post_id)
    {
        $data = [];
        $dataPost = $this->m_posts->getPost($post_id);
        if (empty($dataPost)) {
            return redirect()->to('admin/' . $this->halaman_controller);
        }
        $data = $dataPost;

        if ($this->request->getMethod() == 'post') {
            $data = $this->request->getVar();
            $aturan = [
                'post_title' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'title harus diisi'
                    ]
                ],
                'post_content' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'content harus diisi'
                    ]
                ],
                'post_thumbnail' => [
                    'rules' => 'is_image[post_thumbnail]',
                    'errors' => [
                        'is_image' => 'thumbnail harus berupa gambar'
                    ]
                ],
            ];

            $file = $this->request->getFile('post_thumbnail');

            if (!$this->validate($aturan)) {
                session()->setFlashdata('warning', $this->validation->getErrors());
            } else {
                $post_thumbnail = $dataPost['post_thumbnail'];
                if ($file->getName()) {
                    $post_thumbnail = $file->getRandomName();
                }
                $record = [
                    'username' => session()->get('akun_username'),
                    'post_title' => $this->request->getVar('post_title'),
                    'post_status' => $this->request->getVar('post_status'),
                    'post_thumbnail' => $post_thumbnail,
                    'post_description' => $this->request->getVar('post_description'),
                    'post_content' => $this->request->getVar('post_content'),
                    'post_id' => $post_id,
                ];
                $aksi = $this->m_posts->insertPost($record, $post_type = $this->halaman_controller);
                if ($aksi != false) {
                    $page_id = $aksi;
                    if ($file->getName()) {
                        if ($dataPost['post_thumbnail']) {
                            @unlink(LOKASI_UPLOAD . "/" . $dataPost['post_thumbnail']);
                        }
                        $lokasi_direktori = LOKASI_UPLOAD;
                        $file->move($lokasi_direktori, $post_thumbnail);
                    }
                    session()->setFlashdata('success', 'data berhasil diupdate');
                    return redirect()->to('admin/' . $this->halaman_controller . '/edit/' . $page_id);
                } else {
                    session()->setFlashdata('warning', 'data gagal diupdate');
                    return redirect()->to('admin/' . $this->halaman_controller . '/edit/' . $aksi);
                }
            }
        }

        echo view('admin/v_admin_header', $data);
        echo view('admin/v_article_tambah', $data);
        echo view('admin/v_admin_footer', $data);
    }
};
