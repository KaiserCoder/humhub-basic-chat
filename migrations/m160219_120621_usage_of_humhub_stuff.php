<?php

use yii\db\Migration;
use humhub\models\Setting;
use humhub\modules\ponychat\models\UserChatMessage;

class m160219_120621_usage_of_humhub_stuff extends Migration
{

    public function up()
    {
        $this->dropTable(UserChatMessage::tableName());
        
        $this->createTable(UserChatMessage::tableName(), [
            'id' => $this->primaryKey(),
            'message' => $this->text()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'created_by' => $this->integer()->notNull()
        ]);
        
        Setting::Set('theme', 'theme_bright.css', 'ponychat');
        Setting::Set('timeout', '1', 'ponychat');
    }

    public function down()
    {
        $this->dropTable(UserChatMessage::tableName());
        $this->createTable(UserChatMessage::tableName(), [
            'id' => $this->primaryKey(),
            'author' => $this->string(16)->notNull(),
            'gravatar' => $this->string(255)->notNull(),
            'text' => $this->string(255)->notNull(),
            'ts' => $this->timestamp()->notNull()->defaultValue('CURRENT_TIMESTAMP')
        ]);
        return true;
    }
}
