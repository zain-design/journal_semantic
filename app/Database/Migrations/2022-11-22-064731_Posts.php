<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Posts extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
            'post_id'=>[
                'type'=>'INT',
                'constraint'=>5,
                'unsigned'=>true,
                'auto_increment'=>true
            ],
            'username'=>[
                'type'=>'VARCHAR',
                'constraint'=>25,
            ],
            'post_title'=>[
                'type'=>'VARCHAR',
                'constraint'=>255,
            ],
            'post_thumbnail'=>[
                'type'=>'VARCHAR',
                'constraint'=>255,
            ],
            'post_description'=>[
                'type'=>'VARCHAR',
                'constraint'=>255,
            ],
            'post_content'=>[
                'type'=>'longtext',
            ],
            'post_title_seo'=>[
                'type'=>'VARCHAR',
                'constraint'=>255,
                'unique'=>true
            ],
            'post_status'=>[
                'type'=>'enum',
                'constraint'=>['active', 'inactive'],
                'default'=> 'active',
            ],
            'post_type'=>[
                'type'=>'enum',
                'constraint'=>['article', 'pages'],
                'default'=> 'article',
            ],
            'post_time timestamp default now()'
        ]);
        $this->forge->addForeignKey('username', 'admin', 'username');
        $this->forge->addKey('post_id', TRUE);
        $this->forge->createTable('posts');
    }
    
    public function down()
    {
        //
        $this->forge->dropTable('posts');
    }
}
