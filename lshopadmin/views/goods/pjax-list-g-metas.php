<?php
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\helpers\Html;
?>
<?php
echo GridView::widget([
	'dataProvider' => new ArrayDataProvider([
		'allModels' => $goods->g_metas,
		'pagination' => [
			'pageSize' => -1,
		],
	]),
	'columns' => [
		[ 'attribute' => 'g_atr_id'],
		[ 'attribute' => 'g_atr_name'],
		[ 'attribute' => 'gm_value'],
		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{view} {delete}',
			'header' => '操作',
			'buttons' => [
				'view' => function($url, $model, $key) use($goods){
					return Html::a("更新", '#', [
						'class' => 'btn btn-info btn-xs view-meta-btn',
						'data-id' => $model['gm_id'],
						'data-gid' => $goods['g_id']
					]);
				},
				'delete' => function($url, $model, $key) use ($goods, $urls){
					return Html::a('删除', Url::to([$urls['g-meta-delete'][0], 'gm_id' => $model['gm_id'], 'g_id' => $goods['g_id']]), [
						'class' => 'btn btn-danger btn-xs delete-meta-btn',
					]);
				}
			]
		],
	]
]);
?>
