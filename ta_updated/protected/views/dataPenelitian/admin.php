<?php
/* @var $this DataPenelitianController */
/* @var $model DataPenelitian */

$this->breadcrumbs=array(
	'Daftar Paper'=>array('index'),
	'Atur Paper',
);

$this->menu=array(
	array('label'=>'Daftar Paper', 'url'=>array('index')),
	array('label'=>'Tambah Paper', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#data-penelitian-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Atur Paper</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'data-penelitian-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'judul',
		'peneliti',
		'tahun_publikasi',
		'masalah',
		'deskripsi_masalah',
		/*
		'keyword',
		'domain_data',
		'deskripsi_domain_data',
		'metode',
		'deskripsi_metode',
		'hasil',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
