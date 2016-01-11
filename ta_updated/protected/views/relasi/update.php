<?php
/* @var $this RelasiController */
/* @var $model Relasi */

$this->breadcrumbs=array(
	'Daftar Relasi'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Perbaharui Relasi',
);

$this->menu=array(
	array('label'=>'Daftar Relasi', 'url'=>array('index')),
	array('label'=>'Tambah Relasi', 'url'=>array('create')),
	array('label'=>'Lihat Relasi', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Atur Relasi', 'url'=>array('admin'), 'visible'=>Yii::app()->getModule('user')->isAdmin()),
);
?>

<h1>Perbaharui Relasi <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>