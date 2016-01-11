<?php
/* @var $this DataPenelitianController */
/* @var $model DataPenelitian */

$this->breadcrumbs=array(
	'Daftar Paper'=>array('index'),
	$model->id,
);

//$post = DataPenelitian::model()->findByPk(Yii::app()->request->getParam('id'));
		//echo($post['creater']);
$user_updater='';
		$post = DataPenelitian::model()->findByPk(Yii::app()->request->getParam('id'));
                if(Yii::app()->user->getId() === $post['creater']) {
                        $user_updater = Yii::app()->user->name;
                }
$this->menu=array(
	array('label'=>'Daftar Paper', 'url'=>array('index')),
	array('label'=>'Tambah Paper', 'url'=>array('create')),
	
	array('label'=>'Perbaharui Paper', 'url'=>array('update', 'id'=>$model->id), 'visible'=>Yii::app()->user->getName()==$user_updater),
	
	//array('label'=>'Delete DataPenelitian', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('url'=>array('admin'), 'label'=>'Atur Paper', 'visible'=>Yii::app()->getModule('user')->isAdmin()),
);
?>

<h1>Lihat Paper #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'judul',
		'peneliti',
		'tahun_publikasi',
		'masalah',
		'deskripsi_masalah',
		'keyword',
		'domain_data',
		'deskripsi_domain_data',
		'metode',
		'deskripsi_metode',
		'hasil',
	),
)); ?>
