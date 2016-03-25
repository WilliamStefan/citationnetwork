<?php
/* @var $this RelasiController */
/* @var $model Relasi */

$this->breadcrumbs=array(
	'Daftar Relasi'=>array('index'),
	$model->id,
);

$user_updater='';
	$post = Relasi::model()->findByPk(Yii::app()->request->getParam('id'));
	if(Yii::app()->user->getId() === $post['creater']) {
			$user_updater = Yii::app()->user->name;
	}

$this->menu=array(
	array('label'=>'Daftar Relasi', 'url'=>array('index')),
	array('label'=>'Tambah Relasi', 'url'=>array('create')),
	array('label'=>'Perbaharui Relasi', 'url'=>array('update', 'id'=>$model->id), 'visible'=>Yii::app()->user->getName()==$user_updater),
	//array('label'=>'Delete Relasi', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Atur Relasi', 'url'=>array('admin'), 'visible'=>Yii::app()->getModule('user')->isAdmin()),
);
?>

<h1>Lihat Relasi #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array(
			'label'=>'Deskripsi',
			'value'=>$model->metadata_relasi->deskripsi
			),
		array(
			'label'=>'Judul Paper 1',
			'value'=>$model->data_penelitian->judul
			),
		array(
			'label'=>'Judul Paper 2',
			'value'=>$model->data_penelitian2->judul
			),
		//'creater',
	),
)); ?>
