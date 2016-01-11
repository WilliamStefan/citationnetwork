<?php
/* @var $this RelasiController */
/* @var $model Relasi */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'relasi-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'nama_relasi'); ?>
		<?php
		if($model->metadata_relasi)
		{
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name' => 'Relasi[id_relasi]',
			'id' => 'Relasi_relasi',			
			'value' => $model->metadata_relasi->deskripsi,
			'sourceUrl' => $this->createUrl('lookup'),
			'options' => array(
			'minLength' => '1',
			),
			'htmlOptions' => array(
			'style' => 'height:20px;'
			),
		));
		}
		else
		{
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name' => 'Relasi[id_relasi]',
			'id' => 'Relasi_relasi',			
			'sourceUrl' => $this->createUrl('lookup'),
			'options' => array(
			'minLength' => '1',
			),
			'htmlOptions' => array(
			'style' => 'height:20px;'
			),
		));
		}
		?>
		<!--<?php echo $form->textField($model,'id_relasi'); ?>-->
		<?php echo $form->error($model,'id_relasi'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'judul_paper_1'); ?>
		<?php
		if($model->data_penelitian)
		{
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'Relasi[id_paper_1]',
				'id' => 'Relasi_id_paper_1',
				'value' => $model->data_penelitian->judul,
				'sourceUrl' => $this->createUrl('lookup2'),
				'options' => array(
				'minLength' => '1',
				),
				'htmlOptions' => array(
				'style' => 'height:20px;'
				),
			));
		}
		else
		{
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'Relasi[id_paper_1]',
				'id' => 'Relasi_id_paper_1',
				'sourceUrl' => $this->createUrl('lookup2'),
				'options' => array(
				'minLength' => '1',
				),
				'htmlOptions' => array(
				'style' => 'height:20px;'
				),
			));
		}
		?>
		<!--<?php echo $form->textField($model,'id_paper_1'); ?>-->
		<?php echo $form->error($model,'id_paper_1'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'judul_paper_2'); ?>
		<?php
		if($model->data_penelitian2)
		{
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'Relasi[id_paper_2]',
				'id' => 'Relasi_id_paper_2',
				'value' => $model->data_penelitian2->judul,
				'sourceUrl' => $this->createUrl('lookup2'),
				'options' => array(
				'minLength' => '1',
				),
				'htmlOptions' => array(
				'style' => 'height:20px;'
				),
			));
		}
		else
		{
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'Relasi[id_paper_2]',
				'id' => 'Relasi_id_paper_2',
				'sourceUrl' => $this->createUrl('lookup2'),
				'options' => array(
				'minLength' => '1',
				),
				'htmlOptions' => array(
				'style' => 'height:20px;'
				),
			));
		}
		?>
		<!--<?php echo $form->textField($model,'id_paper_2'); ?>-->
		<?php echo $form->error($model,'id_paper_2'); ?>
	</div>
<!--
	<div class="row">
		<?php echo $form->labelEx($model,'creater'); ?>
		<?php echo $form->textArea($model,'creater',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'creater'); ?>
	</div>
-->
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->