<?php
/* @var $this RelasiController */
/* @var $model Relasi */
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
		<?php echo $form->label($model,'nama_relasi'); ?>
		<?php echo $form->textField($model,'metadata_relasi_search'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'judul_paper_1'); ?>
		<?php echo $form->textField($model,'data_penelitian_search'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'judul_paper_2'); ?>
		<?php echo $form->textField($model,'data_penelitian2_search'); ?>
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