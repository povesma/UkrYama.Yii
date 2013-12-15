					<!-- Не исключена вероятность того, что на <a href="http://www.gosuslugi.ru/ru/chorg/index.php?ssid_4=4120&stab_4=4&rid=228&tid=2" target="_blank">сайте госуслуг</a> окажется немного полезной информации. -->
					<?php $form=$this->beginWidget('CActiveForm', array(
						'id'=>'request-form',
						'enableAjaxValidation'=>false,
						'action'=>Yii::app()->createUrl("holes/request", array("id"=>$hole->ID)),
						'htmlOptions'=> array ('onsubmit'=>"document.getElementById('pdf_form').style.display='none';",'name'=>"requestForm"),
					));
					$usermodel=Yii::app()->user->userModel;
					$model=new HoleRequestForm;

                    if (isset($usermodel->relProfile)) {
                        $model->from=$usermodel->relProfile->request_from ? $usermodel->relProfile->request_from : $usermodel->last_name.' '.$usermodel->name.' '.$usermodel->second_name;
                        $model->signature=$usermodel->relProfile->request_signature ? $usermodel->relProfile->request_signature : $usermodel->last_name.' '.substr($usermodel->name, 0, 2).($usermodel->name ? '.' : '').' '.substr($usermodel->second_name, 0, 2).($usermodel->second_name ? '.' : '');
                        $model->postaddress=$usermodel->relProfile->request_address ? $usermodel->relProfile->request_address : '';
                    }

                    $model->to_name=$gibdd ? $gibdd->post_dative.' '.$gibdd->fio_dative: '';
                    $model->to_address=$gibdd ? $gibdd->address : '';
                    $model->address=CHtml::encode($hole->ADDRESS);

					?>
						<h2><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM') ?></h2>
						<table>
							<tr>
							<th><?= $form->labelEx($model,"lang")?></th>
							<td colspan="2">
							<?= CHtml::button("Українською", Array('class'=>'lnbtn selbt', 'name'=>'uaBtn', 'onClick'=>'langChange("ua",this)'))." ".CHtml::button("По-русски", Array('class'=>'lnbtn', 'name'=>'ruBtn', 'onClick'=>'langChange("ru",this)')) ?><?= $form->hiddenField($model, "lang", array("value"=>"ua")) ?>
							</td>
							<td rowspan=8><div class="notes"><?= Yii::t('holes_view', 'ST1234_INSTRUCTION') ?></div></td>
							</tr>
							<tr>
								<th><?php echo $form->labelEx($model,'to_name'); ?></th>
								<td>
				                                	<?php echo $form->textField($model,'to_name',array('rows'=>3, 'cols'=>40)); ?>
                                					<span class="form-comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_TO_NAME_COMMENT') ?></span>
			                                	</td>
							</tr>
							<tr>
								<th><?php echo $form->labelEx($model,'to_address'); ?></th>
								<td>
				                                	<?php echo $form->textArea($model,'to_address',array('rows'=>3, 'cols'=>40)); ?>
                                					<span class="form-comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_TO_ADDRESS_COMMENT') ?></span>
			                                	</td>
							</tr>

							<tr>
								<th><?php echo $form->labelEx($model,'from'); ?></th>
								<td>
                                    <?php echo $form->textArea($model,'from',array('rows'=>3, 'cols'=>40)); ?>
                                    <span class="form-comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_FROM_COMMENT') ?></span>
                                </td>
							</tr>
							<tr>
								<th><?php echo $form->labelEx($model,'postaddress'); ?></th>
								<td>
                                    <?php echo $form->textArea($model,'postaddress',array('rows'=>3, 'cols'=>40)); ?>
                                    <span class="form-comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_POSTADDRESS_COMMENT') ?></span>
                                </td>
							</tr>
							<tr>
								<th><?php echo $form->labelEx($model,'address'); ?></th>
								<td>
                                    <?php echo $form->textArea($model,'address',array('rows'=>3, 'cols'=>40)); ?>
                                    <span class="form-comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_ADDRESS_COMMENT') ?></span>
                                </td>
							</tr>
							<? if($hole->type->alias == 'light'): ?>
								<tr>
									<th><?php echo $form->labelEx($model,'comment'); ?></th>
									<td>
                                        <?php echo $form->textArea($model,'comment',array('rows'=>3, 'cols'=>40)); ?>
                                        <span class="form-comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_COMMENT_COMMENT') ?></span>
                                    </td>
								</tr>
							<? endif; ?>
							<tr>
								<th><?php echo $form->labelEx($model,'signature'); ?></th>
								<td>
                                    <?php echo $form->textField($model,'signature',array('class'=>'textInput')); ?>
                                    <span class="form-comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_SIGNATURE_COMMENT') ?></span>
                                </td>
							</tr>
<script>
function setPic(id){
	var a = $("#chpk_"+id);
	var tic = $("#tic_"+id);
	if(a.prop('checked')){
		a.prop('checked', false);
		tic.hide();
		$('#pc').text($(".form_pics input:checkbox:checked").length);
	}else{
		a.prop('checked', true);
		tic.show();
		$('#pc').text($(".form_pics input:checkbox:checked").length);
	}
}
function picSelect(){
	if($(".form_pics").css("display")=="none"){
		a = $(".form_pics input:checkbox");
		$(".form_pics .tic").show();
		for(i=0;i<a.length;i++){a[i].checked=true;}
		$('#pc').text($(".form_pics input:checkbox:checked").length);
		$(".form_pics").show();
	}else{
		$(".form_pics").hide();
	}
}
</script>
							<tr><td colspan=2><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_PHOTO', array("{0}"=>"<span id='pc'>".count($hole->pictures_fresh)."</span>")) ?>
							<a href="#" onClick="picSelect()"><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_PHOTO_BUTTON') ?></a>
							</td></tr>
							<tr><td colspan=2 class="form_pics">
							<ul>
							<?php
							foreach($hole->pictures_fresh as $picture){
								echo "<li><input name='chpk[".$picture->id."]' id='chpk_".$picture->id."' type=checkbox checked><a href='#' onClick=setPic(".$picture->id.")><img class='t_pic' width=100px src='".$picture->small."' id='".$picture->id."'><img class='tic' src='/images/tic.png' id='tic_".$picture->id."'></a></li>\n";
							}
							?>
							</ul>
							</td></tr>
							<tr>
								<td colspan="2" class="action-cell">
									<?php echo CHtml::submitButton(Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT'), Array('class'=>'submit', 'name'=>'HoleRequestForm[pdf]')); ?>
									<?php echo CHtml::submitButton(Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT2'), Array('class'=>'submit', 'name'=>'HoleRequestForm[html]')); ?>
								</td>
							</tr>
						</table>
					<?php $this->endWidget(); ?>

