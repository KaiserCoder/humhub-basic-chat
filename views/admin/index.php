<?php
use humhub\compat\CHtml;
use humhub\models\Setting;
use humhub\widgets\DataSaved;
use humhub\compat\CActiveForm;
use humhub\modules\ponychat\controllers\AdminController;
?>
<div class="panel panel-default">
	<div class="panel-heading"><strong>PonyChat</strong></div>
	<div class="panel-body">
		<?php $form = CActiveForm::begin(['id' => 'hhc-settings-form']); ?>
			<?=$form->errorSummary($model); ?>
			<div class="form-group">
				<?=$form->labelEx($model, 'banned'); ?>
				<?=$form->textField($model, 'banned', ['class' => 'form-control', 'readonly' => Setting::IsFixed('banned', 'ponychat')]); ?>
			</div>
			<p class="help-block">Noms des utilisateurs bannis. Ils doivent être séparés d'un espace.</p>
			<?= CHtml::submitButton('Sauvegarder', ['class' => 'btn btn-primary']); ?>
			<?= DataSaved::widget(); ?>
		<?php CActiveForm::end(); ?>
	</div>
</div>
