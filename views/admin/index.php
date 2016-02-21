<?php
use humhub\compat\CHtml;
use humhub\models\Setting;
use humhub\widgets\DataSaved;
use humhub\compat\CActiveForm;
use humhub\modules\humhubchat\controllers\AdminController;
?>
<div class="panel panel-default">
	<div class="panel-heading"><strong>PonyChat</strong></div>
	<div class="panel-body">
		<?php $form = CActiveForm::begin(['id' => 'hhc-settings-form']); ?>
			<?=$form->errorSummary($model); ?>
			<div class="form-group">
				<?=$form->labelEx($model, 'theme'); ?>
				<?=$form->dropDownList($model, 'theme', AdminController::getThemes(), ['class' => 'form-control', 'readonly' => Setting::IsFixed('theme', 'humhubchat')]); ?>
			</div>
			<div class="form-group">
				<?=$form->labelEx($model, 'timeout'); ?>
				<?=$form->textField($model, 'timeout', ['class' => 'form-control', 'readonly' => Setting::IsFixed('timeout', 'humhubchat')]); ?>
			</div>
			<p class="help-block">Nombre de jours après lesquels les messages seront supprimés</p>
			<?= CHtml::submitButton('Sauvegarder', ['class' => 'btn btn-primary']); ?>
			<?= DataSaved::widget(); ?>
		<?php CActiveForm::end(); ?>
	</div>
</div>
