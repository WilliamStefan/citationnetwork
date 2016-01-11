<?php
/* @var $this DataPenelitianController */
/* @var $model DataPenelitian */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'data-penelitian-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'judul'); ?>
		<?php echo $form->textArea($model,'judul',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'judul'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'peneliti'); ?>
		<?php echo $form->textArea($model,'peneliti',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'peneliti'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'tahun_publikasi'); ?>
		<?php echo $form->textField($model,'tahun_publikasi'); ?>
		<?php echo $form->error($model,'tahun_publikasi'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'masalah'); ?>
		<?php echo $form->textArea($model,'masalah',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'masalah'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'deskripsi_masalah'); ?>
		<?php echo $form->textArea($model,'deskripsi_masalah',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'deskripsi_masalah'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'keyword'); ?>
		<?php echo $form->textArea($model,'keyword',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'keyword'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'domain_data'); ?>
		<?php echo $form->textArea($model,'domain_data',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'domain_data'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'deskripsi_domain_data'); ?>
		<?php echo $form->textArea($model,'deskripsi_domain_data',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'deskripsi_domain_data'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'metode'); ?>
		<?php echo $form->textArea($model,'metode',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'metode'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'deskripsi_metode'); ?>
		<?php echo $form->textArea($model,'deskripsi_metode',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'deskripsi_metode'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'hasil'); ?>
		<?php echo $form->textArea($model,'hasil',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'hasil'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->