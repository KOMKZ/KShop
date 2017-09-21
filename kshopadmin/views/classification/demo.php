<?php
use common\assets\BootstrapTreeAsset;
BootstrapTreeAsset::register($this);
$url = $routes['classification_result_action'];
$js = <<<JS
$.get("{$url}", function(res){
	// console.log(res);
	var nodes = eval(res);
	if(nodes['code'] > 0){
		alert(nodes['message']);
	}else{
		$('#tree').treeview({data: nodes['data']});
	}
})
JS;
$this->registerJs($js);
?>
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
				</div>
			</div>
		</div>

	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="box box-default">
			<div class="box-header with-header">
				<div class="box-title">
					<?= Yii::t('app', '所有分类')?>
				</div>
			</div>
			<div class="box-body">
				<div id="tree">

				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="box box-default">
			<div class="box-header with-header">
				<div class="box-title">
					<?= Yii::t('app', '搜索结果')?>
				</div>
			</div>
			<div class="box-body">
				<div class="search-output">

				</div>
			</div>
		</div>
	</div>
</div>
