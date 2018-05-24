<?php
return [
	'roles' => [
		['root', '超级用户'],
		['root_admin', '超级管理员'],
		['goods_admin', '商品管理员'],
		['normal_user', '普通用户'],
		['vistor', '游客']
	],
	'permissions' => [
		['goods/can-update-goods-price', '是否能够修改商品价格'],
		['can-delete-goods-sku', '是否能够删除商品sku'],
		['classification/*', '分类的全部权限'],
		['goods/*', '商品的全部权限']
	],
	'assign' => [
		['vistor', 'site/*']
		,['vistor', 'auth/login']
		,['vistor', 'auth/get-info']
		,['vistor', 'goods/view']
		,['vistor', 'goods/list']
		,['vistor', 'goods/view-sku']
		,['vistor', 'file/output']
		,['goods_admin', 'goods/*']
		,['goods_admin', 'file/*']
	]
];
