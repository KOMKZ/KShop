<?php
namespace common\models\notify\ar;

use yii;
use yii\db\ActiveRecord;

/**
 *
 */
class Notify extends ActiveRecord
{
    CONST OB_JSON = 1;

    CONST ROUTE_HTTP = 1;

    CONST S_NOT_SEND = 1;
    CONST S_SEND_COMPLETED = 2;
    CONST S_NO_FINISH = 4;
    CONST S_SEND_WITH_ERROR = 3;

    CONST STRING_SUCC = 'success';

    public static function tableName(){
        return "{{%notify_record}}";
    }
    public function rules(){
        // todo add rules
        return [
            [[
                'nr_index',
                'nr_ob_type',
                'nr_ob_data',
                'nr_created_time',
                'nr_updated_time',
                'nr_lastnotify_time',
                'nr_version',
                'nr_route',
                'nr_route_params',
                'nr_route_type',
                'nr_is_repete',
                'nr_status',
                'nr_timeout'
            ], 'safe']
        ];
    }


    public function getNr_res(){
        // todo fix tableName
        $cmd = Yii::$app->db->createCommand("
        select * from kshop_notify_result
        where nrt_nid = :p1
        order by nrt_id desc
        ");
        $cmd->bindValues([':p1' => $this->nr_id]);
        return $cmd->queryAll();
    }
    public function fields(){
        return array_merge(parent::fields(), ['nr_res']);
    }

}
