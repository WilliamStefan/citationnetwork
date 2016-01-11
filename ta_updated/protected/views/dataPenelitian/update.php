<?php
/* @var $this DataPenelitianController */
/* @var $model DataPenelitian */

$this->breadcrumbs=array(
	'Daftar Paper'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Perbaharui Paper',
);

$this->menu=array(
	array('label'=>'Daftar Paper', 'url'=>array('index')),
	array('label'=>'Tambah Paper', 'url'=>array('create')),
	array('label'=>'Lihat Paper', 'url'=>array('view', 'id'=>$model->id)),
	array('url'=>array('admin'), 'label'=>'Atur Paper', 'visible'=>Yii::app()->getModule('user')->isAdmin()),
);
?>

<h1>Perbaharui Paper <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>