<?php
/* @var $this RelasiController */
/* @var $model Relasi */

$this->breadcrumbs=array(
	'Daftar Relasi'=>array('index'),
	'Atur Relasi',
);

$this->menu=array(
	array('label'=>'Daftar Relasi', 'url'=>array('index')),
	array('label'=>'Tambah Relasi', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#relasi-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Atur Relasi</h1>

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
	'id'=>'relasi-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		 array(
			'name'=>'nama_relasi', 
            'value'=>'$data->metadata_relasi->deskripsi',
            'filter'=>CHtml::activeTextField($model,'metadata_relasi_search'),
        ),
		 array(
			'name'=>'judul_paper_1', 
            'value'=>'$data->data_penelitian->judul',
            'filter'=>CHtml::activeTextField($model,'data_penelitian_search'),
        ),
		 array(
			'name'=>'judul_paper_2', 
            'value'=>'$data->data_penelitian2->judul',
            'filter'=>CHtml::activeTextField($model,'data_penelitian2_search'),
        ),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
