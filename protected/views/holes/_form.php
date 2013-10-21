<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'holes-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
)); 
echo $form->errorSummary($model); ?>

	<? /*<input type="hidden" name="ID" value="<?= $F['ID']['VALUE'] ?>">
	 if($F['FIX_ID']): ?>
		<input type="hidden" name="FIX_ID" value="<?= $F['FIX_ID']['VALUE'] ?>">
	<? elseif($F['GIBDD_REPLY_ID']): ?>
		<input type="hidden" name="GIBDD_REPLY_ID" value="<?= $F['GIBDD_REPLY_ID']['VALUE'] ?>">
	<? endif;*/ ?>

	<!-- правая колоночка -->
	<div class="rCol side_section"> 
		<ul class="add_steps clear">
			<li class="step_1 clear">
				<div class="step_number clear">
					<span class="clear">1</span>
				</div>
				<p><?php echo Yii::t('template', 'HOW_ADD_STEP1')?></p>
			</li>
			<li class="step_2 clear">
				<div class="step_number clear">
					<span class="clear">2</span>
				</div>
				<p><?php echo Yii::t('template', 'HOW_ADD_STEP2')?></p>
			</li>
			<li class="step_3 clear">
				<div class="step_number clear">
					<span class="clear">3</span>
				</div>
				<p><?php echo Yii::t('template', 'HOW_ADD_STEP3')?></p>
			</li>
		</ul>
	</div>
	<!-- /правая колоночка -->

	<script type="text/javascript">
		$(document).ready( function(){

			$('.defect_type li input').click(function(){
				$('.defect_type li').removeClass('checked');
				$(this).parent('li').addClass('checked');
			});

		});
	</script>

	<!-- левая колоночка -->

	<div class="lCol main_section">
<?php if(!(Yii::app()->user->getId())){ ?>
<table>
<tr>
    <td><?= $form->labelEx($model,'EMAIL') ?></td><td><?= $form->textField($model,'EMAIL', array( "style"=>"width:250px")) ?></td>
    <?= $form->error($model,'EMAIL') ?>
</tr>
<tr>
    <td><?= $form->labelEx($model,'FIRST_NAME') ?></td><td><?= $form->textField($model,'FIRST_NAME',array( "style"=>"width:250px")) ?></td>
    <?= $form->error($model,'FIRST_NAME') ?>

    <td><?= $form->labelEx($model,'LAST_NAME') ?></td><td><?= $form->textField($model,'LAST_NAME',array( "style"=>"width:200px")) ?></td>
    <?= $form->error($model,'LAST_NAME') ?>
</tr>
</table>
<?php }else{ ?>
<?= $form->hiddenField($model,'EMAIL', array("value"=>"1")) ?>
<?= $form->error($model,'EMAIL') ?>

<?= $form->hiddenField($model,'FIRST_NAME',array("value"=>"1")) ?>
<?= $form->error($model,'FIRST_NAME') ?>

<?= $form->hiddenField($model,'LAST_NAME',array("value"=>"1")) ?>
<?= $form->error($model,'LAST_NAME') ?>
<?php } ?>
		<div class="form_top_bg clear">
			<div class="ya_map">
				<div class="bx-yandex-search-layout wide" style="padding-bottom: 0px;">
					<div class="bx-yandex-search-form" style="padding-bottom: 0px;">
							<p><?php echo Yii::t('template', 'ENTER_ADDRES_FOR_SEARCH')?></p>
							<input type="text" id="address_inp" name="address" class="textInput" value="" style="width: 300px;" />
							<input type="submit" value="<?php echo Yii::t('template', 'SEARCH')?>" onclick="jsYandexSearch_MAP_DzDvWLBsil.searchByAddress($('#address_inp').val()); return false;" />
							<a style="display:none;" id="clear_result_link" href="#" onclick="clearSerchResults('MAP_DzDvWLBsil', JCBXYandexSearch_arSerachresults); document.getElementById('address_inp').value=''; return false;"><?php echo Yii::t('template', 'CLEAR')?></a>				
					</div>		
					<div class="full_adress">
						<div class="bx-yandex-search-results" id="results_MAP_DzDvWLBsil"></div>
					</div>	
				</div>	
				<div class="f">
					<div class="full_adress">
						<span id="recognized_address_str" title="<?php echo Yii::t('template', 'SELECTED_REGION')?>"></span>
						<span id="other_address_str"></span>	
					</div>	
					<div class="bx-yandex-view-layout">
						<div class="bx-yandex-view-map">
							<?php 
                     $maptype =$model->isNewRecord ? 'addhole' : 'updatehole'; 
							Yii::app()->clientScript->registerScript('initmap',<<<EOD
							if (window.attachEvent) // IE
								window.attachEvent("onload", function(){init_MAP_DzDvWLBsil(null,'{$maptype}')});
							else if (window.addEventListener) // Gecko / W3C
								window.addEventListener('load', function(){init_MAP_DzDvWLBsil(null,'{$maptype}')}, false);
							else
								window.onload = function(){init_MAP_DzDvWLBsil(null,'{$maptype}')};
EOD
							,CClientScript::POS_HEAD);
							?>

							<input id="MAPLAT" name="MAPLAT" type="hidden" value="" />
							<input id="MAPZOOM" name="MAPZOOM" type="hidden" value="" />
							<input id="Exclude_id" type="hidden" value="<?php echo $model->ID; ?>" />
							<?php
							$this->widget('application.extensions.ymapmultiplot.YMapMultiplot', array(
									'key'=>$this->mapkey,
								   'id' => 'BX_YMAP_MAP_DzDvWLBsil',//id of the <div> container created
								   //'label' => 'Тест', //Title for bubble. Used if you are plotting multiple locations of same business
								   'address' =>  Array(), //Array of AR objects
								   'width'=>'100%',
								   'height'=>'400px',						   
								   //'notshow'=>true
							  ));
							?>
						</div>
					</div>
					<img src="/images/map_shadow.jpg" class="mapShadow" alt="" />

				</div>
				<?
				if(!$model->isNewRecord && $model->pictures_fresh && $model->STATE!=Holes::STATE_FIXED && !$model->GIBDD_REPLY_RECEIVED)
				{
					?>
					<div id="overshadow"><span class="command" onclick="document.getElementById('picts').style.display=document.getElementById('picts').style.display=='block'?'none':'block';"><?php echo Yii::t('template', 'INFO_CANDELETEPHOTO')?></span><div class="picts" id="picts"><?
					foreach($model->pictures_fresh as $i=>$picture){				
						echo '<br>'.$form->checkBox($model,"deletepict[$i]", array('class'=>'filter_checkbox','value'=>$picture->id)).' ';
						echo $form->labelEx($model,"deletepict[$i]", array('label'=>Yii::t('template', 'DELETEPICT'))).'<br><img src="'.$picture->medium.'"><br><br>';
					}
					echo '</div></div>';
				} ?>
				
				<?php if($model->COMMENT2) : ?>
				<!-- камент -->
				<div class="f">
					<?php echo $form->labelEx($model,'COMMENT2'); ?>
					<?php echo $form->textArea($model,'COMMENT2',array('rows'=>6, 'cols'=>50)); ?>
					<?php echo $form->error($model,'COMMENT2'); ?>
				</div>
				<? endif;  ?>
			</div>
			
			<!-- адрес -->
			<div class="f">
				<?php echo $form->labelEx($model,'ADDRESS'); ?>
				<?php echo $form->textField($model,'ADDRESS',array('class'=>'textInput')); ?>
				<?php echo $form->error($model,'ADDRESS'); ?>	
				<p class="tip">
               <?php echo Yii::t('template', 'ENTER_POINT_TO_MAP_DOBLECLICK')?>					
				</p>
			</div>
		</div>	
		<!-- тип дефекта -->
		<div class="f clearfix">
			<?php echo $form->labelEx($model,'TYPE_ID'); ?>

			<ul class="defect_type clearfix"> 
         <?php 
				$data = CHtml::listData(HoleTypes::getTypes(), 'id','alias');
				foreach($data as $id => $alias){
				   $name = Yii::t('holes','HOLES_TYPE_'.strtoupper($alias));

               echo CHtml::tag('li', array(), 
                  CHtml::radioButton(
                     'Holes[TYPE_ID]', 
                     $model->TYPE_ID == $id, 
                     array('value'=>$id, 'id'=>'type_'.$alias)
                  ).
                  CHtml::label($name, 'type_'.$alias)                  
               );
				}
			?> 
         </ul>

			<?php echo $form->error($model,'TYPE_ID'); ?>
		</div>
	
		
		<!-- Дата обнаружения -->
		<div class="f clearfix">
		<?php echo $form->labelEx($model,'DATE_CREATED'); ?>
      <?php echo CHtml::textField('defectdate', date(C_DATEFORMAT, $model->DATE_CREATED)); ?>
		<?php echo $form->error($model,'DATE_CREATED'); ?>
		</div>

      <script>
         $('#defectdate').datepicker({dateFormat: '<?php  echo C_DATEFORMAT_JS ?>'});
      </script>
         	
		<!-- фотки -->
		<div class="f clearfix">
			<?php echo $form->labelEx($model,'upploadedPictures'); ?>
			<?php $this->widget('CMultiFileUpload', array('accept'=>'gif|jpg|png', 'model'=>$model, 'attribute'=>'upploadedPictures', 'htmlOptions'=>array('class'=>'mf'), 'denied'=>Yii::t('mf','Невозможно загрузить этот файл'),'duplicate'=>Yii::t('mf','Файл уже существует'),'remove'=>Yii::t('mf','удалить'),'selected'=>Yii::t('mf','Файлы: $file'),)); ?>			
			<p class="tip">
            <?php echo Yii::t('template', 'ENTER_PHOTO_REMARK')?>	         
         </p>			
		</div>
		
		<!-- камент -->
		<div class="f">
			<?php echo $form->labelEx($model,'COMMENT1'); ?>
			<?php echo $form->textArea($model,'COMMENT1'); ?>
			<?php echo $form->error($model,'COMMENT1'); ?>
		</div>
		<?php echo $form->hiddenField($model,'LATITUDE'); ?>
		<?php echo $form->hiddenField($model,'LONGITUDE'); ?>
		<?php echo $form->hiddenField($model,'STR_SUBJECTRF'); ?>
		<?php echo $form->hiddenField($model,'ADR_CITY'); ?>

		<div class="addSubmit">
			<div onclick="$(this).parents('form').submit();">
				<a class="addFact"><i class="text"><?php echo Yii::t('template', 'SEND')?></i><i class="arrow"></i></a>
			</div>
			<p><?php echo Yii::t('template', 'INFO_AFTERSEND')?></p>
		</div>
	</div>
	<!-- /левая колоночка -->
	
<?php $this->endWidget(); ?>

</div><!-- form -->
