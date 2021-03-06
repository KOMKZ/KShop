<?php
namespace common\models\goods\ar;

use Yii;
use common\models\staticdata\ConstMap;
use yii\db\ActiveRecord;
use common\models\goods\query\GoodsAttrQuery;
use common\models\goods\query\GoodsOptionQuery;
use common\models\goods\ar\GoodsAttr;
/**
 *
 */
class GoodsRealAttr extends ActiveRecord
{
    const STATUS_VALID = 'valid';
    const STATUS_DELETE = 'delete';


    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $data = parent::toArray($fields, $expand, $recursive);
        $data = array_merge($data, $data['g_attr']);
        unset($data['g_attr']);
        $keepFeilds = [
            "g_atr_id" => null, "g_atr_opts" => null,
            "g_atr_code" => null, "g_atr_opt_img" => null,
            "g_atr_show_name" => null, "g_atr_name" => null
        ];
        foreach($data as $field => $val){
            if(!array_key_exists($field, $keepFeilds)){
                unset($data[$field]);
            }
        }
        return $data;
    }

    public static function tableName(){
        return "{{%goods_real_attr}}";
    }

    public function fields(){
        $fields = parent::fields();
        return array_merge($fields, [
            'g_atr_opts',
            'g_attr'
        ]);
    }

    public function getNext_opt_value(){
        $max = GoodsOptionQuery::find()
                               ->where([
                                   'g_id' => $this->g_id,
                                   'g_atr_id' => $this->g_atr_id
                               ])
                               ->max('g_opt_value');
        return $max + 1;
    }

    public function getG_atr_opts(){
        return $this->hasMany(GoodsRealOption::className(), [
            'g_id' => 'g_id',
            'g_atr_id' => 'g_atr_id'])
            ->select([
                "g_opt_value",
                "g_opt_name",
                "g_opt_img",
                "g_opt_id"
            ]);
    }
    public function getG_attr(){
        return $this->hasOne(GoodsAttr::className(), [
            'g_atr_id' => 'g_atr_id'
        ]);
    }


    public function scenarios(){
        return [
            'default' => [
                'g_atr_id', 'g_id', 'gr_status',
            ],
        ];
    }



    public function rules(){
        return [

            ['g_atr_id', 'required'],

            ['g_id', 'required'],

            ['gr_status', 'default', 'value' => static::STATUS_VALID]

        ];
    }




}
