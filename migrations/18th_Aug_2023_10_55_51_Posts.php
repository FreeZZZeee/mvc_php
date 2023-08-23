<?php

use Migration\Migration;

defined("ROOTPATH") or exit('Доступ запрещен!');

class Posts
{
    use Migration;

    public function up(): void
    {

        $this->addColumn('id int(11) NOT NULL AUTO_INCREMENT');
        $this->addColumn('createdAt datetime NULL');
        $this->addColumn('updatedAt datetime NULL');
        $this->addPrimaryKey('id');

//        $this->addUniqueKey();

        $this->createTable('posts');

        $this->addData('createdAt' ,date("Y-m-d H:i:s"));
        $this->addData('updatedAt' ,date("Y-m-d H:i:s"));

        $this->insertData('posts');
    }

    public function down(): void
    {
        $this->dropTable('posts');
    }
}

