<?php
/**
 * Author: Eugine Terentev <eugine@terentev.net>
 */
namespace common\widgets;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

class ArrayObjInput extends InputWidget{
    public $attributes;

    public function init(){
        parent::init();
        parent::init();
        // ArrayObjInputAsset::register($this->getView());
        if($this->hasModel()){
            $this->name = Html::getInputName($this->model, $this->attribute);
            $this->value = Html::getAttributeValue($this->model, $this->attribute);
        }
        $js = <<<js


            function Table() {
                //sets attributes
                this.header = [];
                this.data = [[]];
                this.tableClass = ''
            }
            Table.prototype.setHeader = function(keys) {
                //sets header data
                this.header = keys
                return this
            }

            Table.prototype.setData = function(data) {
                //sets the main data
                this.data = data
                return this
            }
            Table.prototype.setTableClass = function(tableClass) {
                //sets the table class name
                this.tableClass = tableClass
                return this
            }

            Table.prototype.build = function(container) {

                //default selector
                container = container || '.table-container'

                //creates table
                var table = $('<table></table>').addClass(this.tableClass)

                var tr = $('<tr></tr>') //creates row
                var th = $('<th></th>') //creates table header cells
                var td = $('<td></td>') //creates table cells

                var header = tr.clone() //creates header row

                //fills header row
                this.header.forEach(function(d) {
                    header.append(th.clone().text(d))
                })

                //attaches header row
                table.append($('<thead></thead>').append(header))

                //creates
                var tbody = $('<tbody></tbody>')
                //fills out the table body
                this.data.forEach(function(d) {
                    var row = tr.clone() //creates a row
                    d.forEach(function(e,j) {
                        if('string' == typeof e){
                            row.append(td.clone().text(e)) //fills in the row
                        }else{
                            row.append(td.clone().html(e))
                        }
                    })
                    tbody.append(row) //puts row on the tbody
                })

                $(container).append(table.append(tbody)) //puts entire table in the container

                return this
            }

            function getRows(container){
                var rows = [];
                var keys = {};
                $(container).find(".array-obj-input-row").each(function(){
                    var row = []
                    $(this).children().each(function(){
                        row.push($(this).val());
                        var attrName = $(this).attr('data-name');
                        keys[attrName] = attrName
                    });
                    row.push($('<button class="btn btn-default btn-xs">删除</button>'))
                    rows.push(row)
                });
                var rk = [];
                $.each(keys, function(i){
                    rk.push(keys[i])
                })
                rk.push('操作');
                return {rows:rows, key: rk}
            }


            $('.array-obj-input-box').each(function(){
                var data = getRows($(this));
                console.log(data)
                var table = new Table()
                //sets table data and builds it
                table
                    .setHeader(data.key)
                    .setData(data.rows)
                    .setTableClass('table')
                    .build($(this).find('.array-obj-input-list'))
            });

            $(document).ready(function(){
                $(document).on('click', '.array-obj-input-add', function(e){
                    e.preventDefault();
                    return false;
                });
            })
js;
        $this->getView()->registerJs($js);
    }
    public function run(){
        $tpl = "{control}<hr/>{input}{inputHide}{inputList}";
        $control = "";
        $control .= Html::beginTag('div', ['class'=>'input-group']);
        $control .= Html::button("增加输入", ['class' => "btn btn-default array-obj-input-add", 'data-container' => $this->id]);
        $control .= Html::endTag('div');

        $input =  "";
        $baseName = sprintf("%s[%s]", $this->name, count($this->value));
        foreach($this->attributes as $attrName => $attr){
            $inputName = sprintf("%s[%s]", $baseName, $attrName);
            $input .= Html::beginTag('div', ['class'=>'form-group']);
            $input .= Html::tag('label', $attr['label']);
            $input .= Html::input($attr['inputType'], '', null, ['class' => 'form-control', 'data-name' => $inputName]);
            $input .= Html::endTag('div');
        }
        $inputHide = "";
        $inputHide .= Html::beginTag('div', ['class'=>'input-hide']);

        foreach($this->value as $key => $item){
            $baseName = sprintf("%s[%s]", $this->name, $key);
            $inputHide .= Html::beginTag('div', ['class'=>'array-obj-input-row']);
            foreach($item as $attrName => $value){
                $inputName = sprintf("%s[%s]", $baseName, $attrName);
                $inputHide .= Html::input('textInput', $inputName, $value, [
                    'style' => 'display:none;',
                    'data-name' => $attrName
                ]);
            }
            $inputHide .= Html::endTag('div');
        }
        $inputHide .= Html::endTag('div');

        $inputList = "<div class='array-obj-input-list'></div>";

        $content = Html::beginTag('div', ['class'=>'array-obj-input-box', 'id' => $this->id]);
        $content .= strtr($tpl, [
            '{control}' => $control,
            '{input}' => $input,
            '{inputHide}' => $inputHide,
            '{inputList}' => $inputList
        ]);
        $content .= Html::endTag('div');
        return $content;
    }
}
