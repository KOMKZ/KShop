<?php
use yii\bootstrap\ActiveForm;
use common\models\staticdata\ConstMap;
use common\assets\BootstrapTreeAsset;
use common\assets\DateTimePickerAsset;
use common\assets\JsonEditorAsset;
BootstrapTreeAsset::register($this);
DateTimePickerAsset::register($this);
JsonEditorAsset::register($this);
$url = $routes['classification_result_action'];
$clsMetaUrl = $routes['cls_meta_list_action'];
$js = <<<JS

// 选择分类 设置嗯分类相关信息，属性列表，表单分类字段名称
function set_cls_info_from_select(node){
	$('#goods-g_cls_id').val(node.g_cls_id);
	$('#goods-g_cls_id_label').val(node.g_cls_name);
	set_cls_attr_grid_html(node.g_cls_id);
}

// 设置属性网格列表html
function set_cls_attr_grid_html(id){
	$.get("{$clsMetaUrl}?id=" + id, function(res){
		$('#cls-metas-container').html(res);
	})
}

// 初始化分类元属性列表
set_cls_attr_grid_html($('#goods-g_cls_id').val());

// 初始化分类树
$.get("{$url}", function(res){
	var nodes = eval(res);
	if(nodes['code'] > 0){
		alert(nodes['message']);
	}else{
		$('#tree').treeview({
			data: nodes['data'],
			enableLinks: false,
			onNodeSelected: function(event, data){
				set_cls_info_from_select(data);
			},
			levels:2
		});
	}
});

// 定义搜索分类逻辑
var search = function(e) {
  var pattern = $('#input-search').val();
  var options = {
	ignoreCase: $('#chk-ignore-case').is(':checked'),
	exactMatch: $('#chk-exact-match').is(':checked'),
	revealResults: $('#chk-reveal-results').is(':checked')
  };
  var results = $('#tree').treeview('search', [ pattern, options ]);
  $('#tree').treeview('expandNode', [ results ,{}]);

  var output = '<p>' + results.length + ' matches found</p>';
  $.each(results, function (index, result) {
	output += '<p><a href="' + result.text + '">' + result.text + '</a></p>';
  });
  $('#search-output').html(output);
}
$('#btn-search').on('click', search);
$('#input-search').on('keyup', search);

$('#btn-clear-search').on('click', function (e) {
  $('#search-output').treeview('clearSearch');
  $('#input-search').val('');
  $('#search-output').html('');
});


// 设置日期时间字段控件
$('.datepicker').datetimepicker({
  language: 'zh-CN',
  format: 'yyyy-mm-dd hh:ii:ss'
});

// g_meta 结构 Json editor 初始化
JSONEditor.defaults.options.theme = 'bootstrap3';

var editor = new JSONEditor(document.getElementById('g-meta-json-widget'),{
  // The schema for the editor
  schema: {
	type: "array",
	format: "tabs",
	items: {
	  title: "元属性",
	  headerTemplate: "{{self.g_atr_id}} - {{self.g_atr_name}}",
	  "type": "object",
	  "properties": {
		"g_atr_id": {
		  "type": "integer"
		},
		"gm_value": {
			"type": "string"
		},
		"g_atr_name": {
			"type": "string"
		}
	  }
	}
  },

  no_additional_properties: true,
  disable_edit_json: false,
  required_by_default: true
});

// 添加属性到编辑元属性菜单当中
$('.add-to-as-goods-meta-btn').click(function(){
	alert();
});


JS;
$this->registerJs($js);


?>
<?php
$form = ActiveForm::begin();
?>
<div class="row">
	<div class="col-md-4">
		<div class="box box-default">
			<div class="box-header with-border">
				<div class="box-title">
					<?= Yii::t('app', '商品基础信息')?>
				</div>
			</div>
			<div class="box-body">
				<?php
				echo $form->field($model, 'g_primary_name')->textInput();
				echo $form->field($model, 'g_secondary_name')->textInput();
				echo $form->field($model, 'g_status')->dropDownList(ConstMap::getConst('g_status'));
				echo $form->field($model, 'g_cls_id')->textInput(['disabled' => true, 'value' => 33]);
				echo $form->field($model, 'g_cls_id_label')->textInput(['disabled' => true]);
				echo $form->field($model, 'g_start_at_string')->textInput(['class' => 'datepicker form-control']);
				echo $form->field($model, 'g_end_at_string')->textInput(['class' => 'datepicker form-control']);

				?>
			</div>
		</div>
	</div>
	<div class="col-md-8">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-default">
					<div class="box-header with-border">
						<div class="box-title">
							<?= Yii::t('app', "搜索分类")?>
						</div>
						<div class="box-body">
							<div class="row">
								<div class="col-md-6">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="input-search" class="sr-only">Search Tree:</label>
												<input type="input" class="form-control" id="input-search" placeholder="Type to search..." value="">
											</div>
											<div class="checkbox">
												<label>
													<input type="checkbox" class="checkbox" id="chk-ignore-case" value="false">
													Ignore Case
												</label>
											</div>
											<div class="checkbox">
												<label>
													<input type="checkbox" class="checkbox" id="chk-exact-match" value="false">
													Exact Match
												</label>
											</div>
											<div class="checkbox">
												<label>
													<input type="checkbox" class="checkbox" id="chk-reveal-results" value="false">
													Reveal Results
												</label>
											</div>
											<button type="button" class="btn btn-success" id="btn-search">Search</button>
											<button type="button" class="btn btn-default" id="btn-clear-search">Clear</button>
										</div>
									</div>
									<hr>
									<div class="col-md-12">
										<div id="search-output"></div>
									</div>
								</div>
								<div class="col-md-6">
									<div id="tree"></div>
								</div>
							</div>
						</div>
					</div>
				</div>


			</div>
		</div>
	</div>
	<div class="col-md-8">
		<div class="row">
			<div class="col-md-12" id="cls-metas-container">

			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<?php
		$form = ActiveForm::begin([

		]);
		?>
		<div class="box box-default">
			<div class="box-header with-border">
				<div class="box-title">
					<?= Yii::t('app', '编辑商品元属性')?>
				</div>
			</div>
			<div class="box-body">
				<div id="g-meta-json-widget">

				</div>
			</div>
		</div>
		<?php
		ActiveForm::end();
		?>
	</div>

	<div class="col-md-6">
		<div class="box box-default">
			<div class="box-header with-border">
				<div class="box-title">
					<?= Yii::t('app', '编辑商品sku/option属性')?>
				</div>
			</div>
			<div class="box-body">

			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="box box-default">
			<div class="box-header with-border">
				<div class="box-title">
					商品详细信息
				</div>
			</div>
		</div>
	</div>
</div>
<?php
ActiveForm::end();
?>
