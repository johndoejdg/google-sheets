<?php
/**
* @author sergmoro1@ya.ru
* @license MIT
* 
* @var id spreadsheet ID
*  
*/

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::$app->name . " - Google Sheet"; 

$this->params['breadcrumbs'] = [
	['label' => \Yii::t('app', 'Google Sheets'), 'url' => Url::to(['google/index'])],
	\Yii::t('app', 'view'),
];

?>

<iframe src='https://docs.google.com/spreadsheets/d/<?= $id ?>/edit' width='100%' height='600px'></iframe>

<p>
	<?= Html::a(\Yii::t('app', 'Back'), ['google/index'], [
		'class' => 'btn btn-default', 
		'style' => 'text-decoration: none',
	]); ?>
</p>
