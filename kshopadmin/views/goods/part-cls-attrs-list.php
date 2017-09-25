<?php
use yii\grid\GridView;
use common\models\staticdata\ConstMap;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;
?>
<div class="box box-default">
	<div class="box-header with-border">
		<div class="box-title">
			<?= Yii::t('app', '可选属性列表')?>
		</div>
	</div>
	<div class="box-body">
		<?php
		if(!$error && $model){
			$map = [
				'g_atr_type' => ConstMap::getConst('g_atr_type')
				,'g_atr_cls_type' => ConstMap::getConst('g_atr_cls_type')
				,'g_atr_opt_img' => ConstMap::getConst('g_atr_opt_img')
			];
			echo GridView::widget([
				'caption' => sprintf('分类：%s 子分类元属性', $model->g_cls_name),
				'dataProvider' => $clsMetasProvider,
				'columns' => [
					['class' => CheckboxColumn::className()]
					,['attribute' => 'g_atr_id']
					,['attribute' => 'g_atr_name']
					,['attribute' => 'g_atr_show_name']
					,[
						'attribute' => 'g_atr_opt_img'
						,'value' => function($model, $key, $index, $column) use($map){
							return $map['g_atr_opt_img'][$model['g_atr_opt_img']];
						}
					]
					,[
						'attribute' => 'g_atr_type'
						,'value' => function($model, $key, $index, $column) use($map){
							return $map['g_atr_type'][$model['g_atr_type']];
						}
					]
					,[
						'attribute' => 'g_atr_cls_type'
						,'value' => function($model, $key, $index, $column) use($map){
							return $map['g_atr_cls_type'][$model['g_atr_cls_type']];
						}
					]
					,[
						'class' => 'yii\grid\ActionColumn',
						'header' => '操作',
						// 'buttonOptions' => ['target' => '_blank'],
						'template' => '{add-to-as-goods-meta}',
						'urlCreator' => function ($action, $model, $key, $index){
							return '#';
						},
						'buttons' => [
							'add-to-as-goods-meta' => function($url, $model, $key){
								return Html::a(Yii::t('app', '添加为商品元属性'), $url, [
									'class' => 'btn btn-primary btn-xs add-to-as-goods-meta-btn'
								]);
							},
						],
					]
				]
			]);


			echo GridView::widget([
				'caption' => sprintf('分类：%s 子分类属性', $model->g_cls_name),
				'dataProvider' => $clsAttrsProvider,
				'columns' => [
					['class' => CheckboxColumn::className()]
					,['attribute' => 'g_atr_id']
					,['attribute' => 'g_atr_name']
					,['attribute' => 'g_atr_show_name']
					,[
						'attribute' => 'g_atr_opt_img'
						,'value' => function($model, $key, $index, $column) use($map){
							return $map['g_atr_opt_img'][$model['g_atr_opt_img']];
						}
					]
					,[
						'attribute' => 'g_atr_type'
						,'value' => function($model, $key, $index, $column) use($map){
							return $map['g_atr_type'][$model['g_atr_type']];
						}
					]
					,[
						'attribute' => 'g_atr_cls_type'
						,'value' => function($model, $key, $index, $column) use($map){
							return $map['g_atr_cls_type'][$model['g_atr_cls_type']];
						}
					]
					,[
						'class' => 'yii\grid\ActionColumn',
						'header' => '操作',
						// 'buttonOptions' => ['target' => '_blank'],
						'template' => '{add-to-as-goods-attr}',
						'urlCreator' => function ($action, $model, $key, $index){
							return '#';
						},
						'buttons' => [
							'add-to-as-goods-attr' => function($url, $model, $key){
								return Html::a(Yii::t('app', '添加为商品属性'), $url, [
									'class' => 'btn btn-primary btn-xs add-to-as-goods-attr-btn'
								]);
							},
						],
					]

				]
			]);

		}elseif(!$error){
			echo Yii::t('app', '没有选择分类');
		}else{
			echo $error;
		}
		?>
	</div>
</div>
