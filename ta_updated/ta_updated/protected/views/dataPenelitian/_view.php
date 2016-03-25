<?php
/* @var $this DataPenelitianController */
/* @var $data DataPenelitian */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('judul')); ?>:</b>
	<?php echo CHtml::encode($data->judul); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('peneliti')); ?>:</b>
	<?php echo CHtml::encode($data->peneliti); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('tahun_publikasi')); ?>:</b>
	<?php echo CHtml::encode($data->tahun_publikasi); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('masalah')); ?>:</b>
	<?php echo CHtml::encode($data->masalah); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('deskripsi_masalah')); ?>:</b>
	<?php echo CHtml::encode($data->deskripsi_masalah); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('keyword')); ?>:</b>
	<?php echo CHtml::encode($data->keyword); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('domain_data')); ?>:</b>
	<?php echo CHtml::encode($data->domain_data); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('deskripsi_domain_data')); ?>:</b>
	<?php echo CHtml::encode($data->deskripsi_domain_data); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('metode')); ?>:</b>
	<?php echo CHtml::encode($data->metode); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('deskripsi_metode')); ?>:</b>
	<?php echo CHtml::encode($data->deskripsi_metode); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('hasil')); ?>:</b>
	<?php echo CHtml::encode($data->hasil); ?>
	<br />

	*/ ?>

</div>