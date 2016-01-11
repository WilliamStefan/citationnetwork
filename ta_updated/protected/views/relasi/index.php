<?php
/* @var $this RelasiController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Daftar Relasi',
);

$this->menu=array(
	array('label'=>'Tambah Relasi', 'url'=>array('create')),
	array('label'=>'Atur Relasi', 'url'=>array('admin'), 'visible'=>Yii::app()->getModule('user')->isAdmin()),
);
?>

<h1>Daftar Relasi</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
