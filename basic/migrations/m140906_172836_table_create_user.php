<?php

use yii\db\Migration;

class m140906_172836_table_create_user extends Migration
{
    public function up()
    {
        $this->createTable('user', [
            'id' => 'pk',
            'username' => 'varchar(24) NOT NULL',
            'password' => 'varchar(128) NOT NULL',
            'authkey' => 'varchar(255) NOT NULL',
            'accessToken' => 'varchar(255)'
        ]);

        $this->insert('user', [
            'username' => 'admin',
            'password' => Yii::$app->getSecurity()->generatePasswordHash('admin'),
            'authkey' => uniqid()
        ]);
    }

    public function down()
    {
        $this->dropTable('user');
    }
}
