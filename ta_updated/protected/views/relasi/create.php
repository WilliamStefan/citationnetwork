<?php
/* @var $this RelasiController */
/* @var $model Relasi */

$this->breadcrumbs=array(
	'Daftar Relasi'=>array('index'),
	'Tambah Relasi',
);

$this->menu=array(
	array('label'=>'Daftar Relasi', 'url'=>array('index')),
	array('label'=>'Atur Relasi', 'url'=>array('admin'), 'visible'=>Yii::app()->getModule('user')->isAdmin()),
);
?>

<h1>Tambah Relasi</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>