<?php
/* @var $this DataPenelitianController */
/* @var $model DataPenelitian */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'judul'); ?>
		<?php echo $form->textArea($model,'judul',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'peneliti'); ?>
		<?php echo $form->textArea($model,'peneliti',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'tahun_publikasi'); ?>
		<?php echo $form->textField($model,'tahun_publikasi'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'masalah'); ?>
		<?php echo $form->textArea($model,'masalah',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'deskripsi_masalah'); ?>
		<?php echo $form->textArea($model,'deskripsi_masalah',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'keyword'); ?>
		<?php echo $form->textArea($model,'keyword',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'domain_data'); ?>
		<?php echo $form->textArea($model,'domain_data',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'deskripsi_domain_data'); ?>
		<?php echo $form->textArea($model,'deskripsi_domain_data',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'metode'); ?>
		<?php echo $form->textArea($model,'metode',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'deskripsi_metode'); ?>
		<?php echo $form->textArea($model,'deskripsi_metode',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'hasil'); ?>
		<?php echo $form->textArea($model,'hasil',array('rows'=>6, 'cols'=>50)); ?>
	</div>
	
	<div class="row">
		<?php echo $form->label($model,'pembuat'); ?>
		<?php echo $form->textArea($model,'creater_search'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->