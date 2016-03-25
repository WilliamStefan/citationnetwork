<?php
/* @var $this DataPenelitianController */
/* @var $model DataPenelitian */

$this->breadcrumbs=array(
	'Daftar Paper'=>array('index'),
	'Tambah Paper',
);

$this->menu=array(
	array('label'=>'Daftar Paper', 'url'=>array('index')),
	array('url'=>array('admin'), 'label'=>'Atur Paper', 'visible'=>Yii::app()->getModule('user')->isAdmin()),
);
?>

<h1>Tambah Paper</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>