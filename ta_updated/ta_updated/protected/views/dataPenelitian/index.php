<?php
/* @var $this DataPenelitianController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Daftar Paper',
);

$this->menu=array(
	array('label'=>'Tambah Paper', 'url'=>array('create')),
	array('url'=>array('admin'), 'label'=>'Atur Paper', 'visible'=>Yii::app()->getModule('user')->isAdmin()),
);
?>

<h1>Daftar Paper</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
