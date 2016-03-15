<?php
/**
* @author sergmoro1@ya.ru
* @license MIT
* 
* @var dataProvider array data provider
* @var title 
* @var owner spreadsheets owner
*  
*/

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

$this->title = Yii::$app->name . " - Google Sheets"; 

$this->params['breadcrumbs'] = [
	\Yii::t('app', 'Google Sheets'),
];

$this->params['jumbotron'] = [
	'title' => \Yii::t('app', 'Google Sheets'), 
	'subtitle' => \Yii::t('app', 'index'),
	'image' => Url::toRoute('files/site/page/google-bg.jpg'),
];
?>

<h1><?= $title ?></h1>
<p><?= $owner ?></p>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'layout' => "{items}\n{summary}\n{pager}",
	'columns' => [
		'title',
		'author',
		'email',
		[
			'header' => \Yii::t('app', 'Updated'),
			'value' => function($data) {
				return date('Y-m-d', $data['updated_at']);
			}
		],

		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{update}',
			'options' => ['style' => 'width:5%;'],
			'buttons' => [
				'update' => function ($url, $data, $key) {
					return Html::a(
						$data['editable']
							? '<span class="glyphicon glyphicon-pencil"></span>'
							: '<span class="glyphicon glyphicon-eye-open"></span>',
						['view', 'id' => $data['id']]
					);
				},
			],
		],
	],
]); ?>