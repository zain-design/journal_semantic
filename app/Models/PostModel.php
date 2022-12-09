<?php

namespace App\Models;


use CodeIgniter\Database\MySQLi\Builder;
use CodeIgniter\Model;
use PhpParser\Node\Expr\New_;

require 'vendor/autoload.php';
require '/html_tag_helpers.php';

\EasyRdf\RdfNamespace::set('wd', 'http://www.wikidata.org/entity/');
\EasyRdf\RdfNamespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
\EasyRdf\RdfNamespace::set('item', 'http://www.semanticweb.org/zainuzzuha/ontologies/2022/11/karya-ilmiah#');
$sparql = new \EasyRdf\Sparql\Client('http://localhost:3030/karya-ilmiah/query', 'http://localhost:3030/karya-ilmiah/update');


class PostModel extends Model
{
    function setTitleSeo($title)
    {
        $url = strip_tags($title);
        $url = preg_replace('/[^A-Za-z0-9]/', " ", $url);
        $url = trim($url);
        $url = preg_replace('/[^A-Za-z0-9]/', "-", $url);
        $url = strtolower($url);

        $builder->where('post_title', $title);
        $jumlah = $builder->countAllResults();
        if ($jumlah > 0) {
            $jumlah = $jumlah + 1;
            return $url . "-" . $jumlah;
        }
        return $url;
    }

    function insertPost($data, $post_type)
    {
        helper("global_fungsi_helper");

        $builder = $this->table($this->table);
        $data['post_type'] = $post_type;

        foreach ($data as $key => $value) {
            $data[$key] = purify($value);
        }

        if (isset($data['post_id'])) {
            $aksi = $builder->save($data);
            $id = $data['post_id'];
        } else {
            $data['post_title_seo'] = $this->setTitleSeo($data['post_title']);
            $aksi = $builder->save($data);
            $id = $builder->getInsertID();
        }
        if ($aksi) {
            return $id;
        } else {
            return false;
        };
    }

    // penting untuk menampilkan data
    function listPost($post_type, $jumlahBaris, $katakunci = null, $group_dataset = null)
    {
        $builder = $this->table($this->table);

        $arr_katakunci = explode(" ", $katakunci);
        $builder->groupStart();
        for ($x = 0; $x < count($arr_katakunci); $x++) {
            $builder->orLike('post_title', $arr_katakunci[$x]);
            $builder->orLike('post_description', $arr_katakunci[$x]);
            $builder->orLike('post_content', $arr_katakunci[$x]);
        }
        $builder->groupEnd();

        $builder->where('post_type', $post_type);
        $builder->orderBy('post_time', 'desc');

        $data['record'] = $builder->paginate($jumlahBaris, $group_dataset);
        $data['pager'] = $builder->pager;

        return $data;
    }

    function getPost($post_id)
    {
        $builder = $this->table($this->table);
        $builder->where('post_id', $post_id);
        $query = $builder->get();

        return $query->getRowArray();
    }

    function deletePost($post_id)
    {
        $builder = $this->table($this->table);

        $builder->where('post_id', $post_id);
        if ($builder->delete()) {
            return true;
        } else {
            return false;
        }
    }

    function getPostbySeo($post_title_seo)
    {
        $builder = $this->table($this->table);

        $builder->where('post_title_seo', $post_title_seo);
        $query = $builder->get();
        return $query->getRowArray();
    }
}
