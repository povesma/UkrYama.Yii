<?php
$this->pageTitle=Yii::app()->name.' :: '.Yii::t('template', 'DEFECT_CARD');

$this->widget('application.extensions.fancybox.EFancyBox', array(
      'target'=>'.holes_pict',
      'config'=>array('attr'=>'hole',),
   )
);
?>

<div class="head">
	<div class="container">
		<div class="lCol">
         <?php echo CHtml::link(CHtml::image(Yii::app()->request->baseUrl."/images/logo.png", $this->pageTitle), "/", array('class'=>'logo', 'title'=>Yii::t('template','GOTO_MAIN'))); ?>
		</div>
         
		<div class="rCol">
         <div class="r">
            <?php 
//		if ( Yii::app()->user->isGuest || ($hole->user->getParam('id') && Yii::app()->user->getId() && Yii::app()->user->getId()!=$hole->user->id)):
//		if ( Yii::app()->user->isGuest || ( Yii::app()->user->getId() && Yii::app()->user->getId()!=$hole->user->id)):
            ?>
            <div class="add-by-user">
      			<span><?php echo Yii::t('template', 'DEFECT_ADDEDBY')?></span>
        	      <?php echo CHtml::link(CHtml::encode($hole->user->getParam('showFullname') ? $hole->user->Fullname : $hole->user->username), array('/profile/view', 'id'=>$hole->user->id),array('class'=>""));?>
            </div>
            <?php //endif;?>
            <div class="control">
               <!-- RIGHT PANEL -->
               <?php $this->renderPartial('_viewrightpanel', array('hole'=>$hole)) ?>
            </div>
         </div>
         <div class="h">
            <div id="ymapcontainer_big">
            	<div align="right"><span class="close" onclick="document.getElementById('ymapcontainer_big').style.display='none';$('#col').css('marginBottom',0)">&times;</span></div>
            	<div id="ymapcontainer_big_map"></div>
            </div>

            <?if($hole['LATITUDE'] && $hole['LONGITUDE']):?><div id="ymapcontainer" class="ymapcontainer"></div><?endif;?>
            <script type="text/javascript">
            	var map_centery = <?= $hole['LATITUDE'] ?>;
            	var map_centerx = <?= $hole['LONGITUDE'] ?>;
            	var map = new YMaps.Map(YMaps.jQuery("#ymapcontainer")[0]);
            	YMaps.Events.observe(map, map.Events.DblClick, function () { toggleMap(); } );
            	map.enableScrollZoom();
            	map.setCenter(new YMaps.GeoPoint(map_centerx, map_centery), 14);
            	var s = new YMaps.Style();
            	s.iconStyle = new YMaps.IconStyle();
            	s.iconStyle.href = "/images/st1234/<?= $hole->type->alias;?>_<?= $hole['STATE'] ?>.png";
            	s.iconStyle.size = new YMaps.Point(54, 61);
            	s.iconStyle.offset = new YMaps.Point(-30, -61);
            	var placemark = new YMaps.Placemark(new YMaps.GeoPoint(map_centerx, map_centery), { hideIcon: false, hasBalloon: false, style: s } );
            	YMaps.Events.observe(placemark, placemark.Events.Click, function () { toggleMap(); } );
            	map.addOverlay(placemark);
            </script>

            <div class="info">
               <div>
                  <span class="date"><?php echo CHtml::encode(Y::dateFromTime($hole->DATE_CREATED)); ?></span>
                  <?php
                  $userGroup = UserGroupsUser::model()->findByPk(Yii::app()->user->id);
                  if (isset($userGroup->level) && $userGroup->level > 1):?>
                  <div class="edit-container">
                    <?php 
                     if ($hole->STATE == Holes::STATE_FRESH)
                        echo CHtml::link(Yii::t('holes_view', 'EDIT'), array('update', 'id'=>$hole->ID)); 
                     echo CHtml::link(Yii::t('holes_view', 'DELETE'), 
                                 array('personalDelete', 'id'=>$hole->ID), 
                                 array('onclick'=>'return confirm("'.Yii::t('holes_view', 'DELETE_DEFECT_CONFIRM').'");', 'class'=>'delete')); 
                    ?>
                  </div>
                  <?php endif;?>
               </div>
               
            	<p class="type type_<?= $hole->type->alias ?>"><?php echo $hole->type->getName(); ?></p>
            	<p class="address"><?= CHtml::encode($hole->ADDRESS) ?></p>
            	<p class="status">
               	<span class="bull <?= $hole->STATE ?>">&bull;</span>
               	<span class="state">
               		<?php 
                     echo CHtml::tag('b', array(), CHtml::encode($hole->StateName)).' '; 

                     $arr[] = array('name'=>CHtml::tag('b', array(), Yii::t('holes_view', 'HOLE_CREATED_INFO')));
                     $arr[] = array('date'=>Y::dateTimeFromTime($hole->createdate), 'name'=>Yii::t('holes_view', 'HOLE_CREATED'));
                     $arr[] = array('date'=>Y::dateFromTime($hole->DATE_CREATED), 'name'=>Yii::t('holes_view', 'HOLE_FIND'));

                     $requests = $hole->requests_gibdd;
		     $requestStatus = $hole->request_sent;
		     if(!$requestStatus[0]->status && count($requestStatus)){
			$requestStatus[0]->updateMail();
		     }
                     if ($requests){
                        foreach ($requests as $request){
                           $arr[] = array('name'=>CHtml::tag('b', array(),
                              Yii::t('holes_view', 'HOLE_REQUEST_USER', array('{0}'=>$request->user->getFullname()))), 
                              'date'=>Y::dateFromTime($request->date_sent));
				if($requestStatus[0]->status){
						if($requestStatus[0]->status!=2){
							$arr[] = array('name'=>Yii::t('holes_view', 'HOLE_REQUEST_DELIVERDATE'), 'date'=>Y::dateFromTime($requestStatus[0]->ddate));
						}
			        }else{
					$arr[] = array('name'=>Yii::t('holes_view', 'HOLE_REQUEST_DELIVERDATE'), 'date'=>Yii::t('holes_view', 'HOLE_REQUEST_NOTDELIVERED'));
				}
				
                           if($request->answers) foreach($request->answers as $answer){
                              $arr[] = array('name'=>Yii::t('holes_view', 'HOLE_ANSWER_DATE'), 'date'=>Y::dateFromTime($answer->date));
                              $arr[] = array('name'=>Yii::t('holes_view', 'HOLE_ANSWER_CREATEDATE'), 'date'=>Y::dateTimeFromTime($answer->createdate));
                              
                           }
                        }
                     }
                     
                     $requests = $hole->requests_prosecutor;
                     if ($requests){
                        foreach ($requests as $request){
                           $arr[] = array('name'=>CHtml::tag('b', array(), 
                              Yii::t('holes_view', 'HOLE_REQUEST_PROSECUTOR_USER', array('{0}'=>$request->user->getFullName()))), 
                              'date'=>Y::dateFromTime($request->date_sent));
                           if($request->answers){
                              foreach($request->answers as $answer){
                                 $arr[] = array('name'=>Yii::t('holes_view', 'HOLE_ANSWER_PROSECUTOR_DATE'), 'date'=>Y::dateFromTime($answer->date));
                                 $arr[] = array('name'=>Yii::t('holes_view', 'HOLE_ANSWER_PROSECUTOR_CREATEDATE'), 'date'=>Y::dateTimeFromTime($answer->createdate));
                              }                              
                           }
                        }
                     }                     
                     
                     $fixeds = $hole->fixeds;
                     if ($fixeds) foreach ($fixeds as $fix){
                           $arr[] = array('name'=>CHtml::tag('b', array(), 
                              Yii::t('holes_view', 'HOLE_FIX_USER', array('{0}'=>$fix->user->FullName))), 
                              );
                        $arr[] = array('name'=>Yii::t('holes_view', 'HOLE_FIX_DATE'), 'date'=>Y::dateFromTime($fix->date_fix));
                        $arr[] = array('name'=>Yii::t('holes_view', 'HOLE_FIX_CREATEDATE'), 'date'=>Y::dateTimeFromTime($fix->createdate));
                     }
                     
                     
                     $dataProvider = new CArrayDataProvider($arr, array('pagination'=>false));

                     $this->widget('zii.widgets.grid.CGridView', array(
                         'dataProvider' => $dataProvider,
                         'summaryText' => '', // 1st way
                         'hideHeader'=>true,
                         'template' => '{items}{pager}', // 2nd way
                         'columns' => array(array('name'=>'name', 'type'=>'raw'), 'date'),
                     ));
                     
                     
                     /*
                     Дата обнаружения дефекта; дата загрузки,
                     Дата отправки в ГАИ жалобы и сохраненная копия жалобы. Для пользователя, который отправил жалобу, она доступна полностью, для других пользователей - без его персональных данных.
                     Дата получения ответа из ГАИ, дата загрузки ответа из ГАИ
                     (если ответов несколько, то несколько таких строчек). По клику на строчку открывается ответ ГАИ
                     Даты всех дальнейших действий (отправка в прокуратуру)
                     Дата исправления дефекта; дата загрузки на сайт отметки об исправлении.
                     */


                   /*  if($hole->STATE == Holes::STATE_PROSECUTOR && $hole->DATE_STATUS){
                        echo Yii::t('holes_view', 'REQUEST_TO_PROSECUTOR_SENT', array('{0}'=>Y::dateFromTime($hole->DATE_STATUS)));
                     }
                     elseif($hole->DATE_SENT) {
               			if (count($hole->requests_gibdd) == 1)
               				echo CHtml::encode(Y::dateFromTime($hole->DATE_SENT)).' отправлен запрос в ГАИ';
               			else{
               				echo CHtml::encode(Y::dateFromTime($hole->DATE_SENT)).' был отправлен первый запрос в ГАИ <br/>';
                           echo CHtml::link('история запросов', '#', array('onclick'=>"$('#requests_gibdd_history').toggle('slow'); return false;"));
                           
                           echo CHtml::openTag('div', array('id'=>'requests_gibdd_history', 'style'=>'display:none;'));
                           echo CHtml::openTag('ui');
                           foreach ($hole->requests_gibdd as $request){
                              if ($request->user){
                                 CHtml::openTag('li');
                                 echo date(C_DATEFORMAT, $request->date_sent);
                                 $userlink=CHtml::link(CHtml::encode($request->user->getParam('showFullname') ? $request->user->Fullname : ($request->user->name ? $request->user->name : $request->user->username)), array('/profile/view', 'id'=>$request->user->id),array('class'=>"")).'отправил запрос в ГАИ';
                                 echo $userlink;
    					               if ($hole->STATE == Holes::STATE_FIXED && $fix=$hole->getFixByUser($request->user->id)){ 
    					                  echo'<br />'.date(C_DATEFORMAT, $fix->date_fix).$userlink.'отметил факт исправления дефекта';
                                 }
                                 CHtml::closeTag('li');
                              }
                           }
                           echo'<li>==========</li>';
                           echo CHtml::closeTag('ul');
                           echo CHtml::closeTag('div');
                        } 
                     } 
                                          
               		if($hole->STATE == Holes::STATE_FIXED && $hole->DATE_STATUS)
               			CHtml::encode(Y::dateFromTime($hole->DATE_STATUS)).' отмечен факт исправления дефекта'
                        
                        */
                     ?>
               	</span>
            	</p>
            
               <?php if(!$hole->PREMODERATED) { ?>
               <p><font class="errortext premoderate"><?php echo  Yii::t('holes_view', 'PREMODRATION_WARNING');?></font><br/></p>
               <? } ?>

               <div class="social">
                  <div class="like">
            			<!-- Facebook like -->
            			<div id="fb_like">
            				<noindex><iframe src="http://www.facebook.com/plugins/like.php?href=<?=Yii::app()->request->hostInfo?>/<?=Yii::app()->request->pathInfo?>&amp;layout=button_count&amp;show_faces=false&amp;width=180&amp;action=recommend&amp;font&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:180px; height:21px;" allowTransparency="true"></iframe></noindex>
            			</div>
            			<!-- Vkontakte like -->
            			<noindex>
                        <div id="vk_like"></div>
                        <script type="text/javascript">VK.Widgets.Like("vk_like", {type: "button", verb: 1});</script>
                     </noindex>
                  </div>
                  <div class="share">
                     <span><?php echo Yii::t('template', 'SHARE')?></span>
                     <div class="likenew">
                        <noindex><script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
                        <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="button" data-yashareQuickServices="vkontakte,facebook,twitter,lj">
                        </div></noindex><br />
                     </div>
			         </div>
			      </div>			
            </div><!-- info -->		
         </div><!-- h --> 	
      </div><!-- rCol -->
   </div><!-- container -->
</div><!-- head -->


<div class="mainCols" id="col">
	<div class="lCol">
		<div class="comment">
			<?php echo $hole['COMMENT1'] ?>
		</div>
   </div>
   <div class="rCol">
      <div class="b">
         <div class="before">
			<?php 
            if($hole->pictures_fresh){  // было
               echo CHtml::tag('h2', array(), Yii::t('holes_view', 'HOLE_ITWAS'));
   			   foreach($hole->pictures_fresh as $i=>$picture){     
                  $imageContent = CHtml::tag('span', array(/*'class'=>'zoo1m-pic'*/)).CHtml::image($picture->small);
   			      echo CHtml::link($imageContent, $picture->medium,
   					    array('class'=>'holes_pict',
                        'rel'=>'hole', 
                        'title'=>CHtml::encode($hole->ADDRESS))
                  );
               } 
            }
         ?>
         </div>
         
         <!-- ANSWERS PANEL -->
         <?php $this->renderPartial('_viewanswers', array('hole'=>$hole)) ?>
      
   		<?php //стало
         if($hole['STATE'] == Holes::STATE_FIXED){
            echo CHtml::openTag('div', array('class'=>'after'));
            if($hole->pictures_fixed){ 
   				echo CHtml::tag('h2', array(), Yii::t('holes_view', 'HOLE_ITBECAME'));
               foreach($hole->pictures_fixed as $i=>$picture){					
                  if ($picture->user_id==Yii::app()->user->id || Yii::app()->user->level > 80 || $hole->IsUserHole)
                     echo CHtml::link(Yii::t('template', 'DELETE_IMAGE'), Array('delpicture','id'=>$picture->id), Array('class'=>'declarationBtn')).'<br />';
   				
                  echo CHtml::link(CHtml::image($picture->medium), 
                     $picture->original, 
                     array('class'=>'holes_pict','rel'=>'hole_fixed', 'title'=>CHtml::encode($hole->ADDRESS).' - исправлено')
                  ); 
               }
            }
   			if($hole['COMMENT2']){
               echo CHtml::tag('div', array('class'=>'comment'), $hole['COMMENT2']);
            }
            CHtml::closeTag('div');
         }?>
      
   	</div>
   </div>
   <div class="rCol">
      <div class="b">
      <?php
          $this->widget('comments.widgets.ECommentsListWidget', array(
              'model' => $hole,
          ));
      ?>
      </div>
   </div>
</div>
