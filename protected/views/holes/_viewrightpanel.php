<div class="progress">

   <?php if($hole->WAIT_DAYS): ?>
   <div class="lc">
      <div class="wait">
         <span class="days"><?php echo $hole->WAIT_DAYS ?></span>
         <span class="day-note"><?php echo Yii::t('template', 'INFO_COUNT_DAYS_WAIT', array('{0}'=>Y::declOfDays($hole->WAIT_DAYS, false))) ?></span>
     </div>
   </div>
   <?php elseif($hole->PAST_DAYS): ?>
   <div class="lc">
      <div class="wait">
         <span class="days"><?php echo $hole->PAST_DAYS ?></span>
         <span class="day-note"><?php echo Yii::t('template', 'INFO_COUNT_PAST_DAYS', array('{0}'=>Y::declOfDays($hole->PAST_DAYS, false))) ?></span>
      </div>
   </div>
   <?php endif; ?>
<script>
$(window).keydown(function(e){
	if (e.keyCode==80 && e.ctrlKey){
		var c=document.getElementById('pdf_form');
		if(c){
			c.style.display='block';
			e.preventDefault();
		}
	}
	if (e.keyCode==27){
		var c=document.getElementById('pdf_form');
		if(c){
			c.style.display='none';
			e.preventDefault();
		}
	}


});
function langChange(val,btn){
	$(".lnbtn").removeClass("selbt")
	btn.className="lnbtn selbt";
	requestForm.HoleRequestForm_lang.value=val;
	$.post("/holes/langChange/<?= $hole->ID ?>",{'lang':val},function(data){
	var test = data.split("|");
	requestForm.HoleRequestForm_to_address.value=test[0];
	requestForm.HoleRequestForm_to_name.value=test[1]+" "+test[2];
	});
}
</script>

   <?php if(!Yii::app()->user->isGuest){ 
      switch($hole->STATE){
         case Holes::STATE_FRESH:
	         //if($hole->IsUserHole || Yii::app()->user->IsAdmin):
	         //endif; ?>
         	<div class="progress">
         		<div class="lc">
         			<a href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';c.focus()}return false;" class="printDeclaration"><?= Yii::t('holes_view', 'PRINT_CLAIM') ?></a>
         		</div>
         		<div class="cc">
         			<?php 
                  echo CHtml::tag('p', array(), CHtml::link(Yii::t('holes_view', 'CLAIM_TO_GAI_WAS_SEND'), array('sent', 'id'=>$hole->ID),array('class'=>"declarationBtn")));
                  if($hole->IsUserHole || Yii::app()->user->isAdmin)
   			         echo CHtml::tag('p', array(), CHtml::link(Yii::t('holes_view', 'SET_DEFECT_AS_FIXED'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn"))); ?>
         		</div>
         	</div>
         	<?php
         	break;


         case Holes::STATE_INPROGRESS:
            if($hole->request_gibdd): 
               if ($hole->IsUserHole || Yii::app()->user->level > 40) : ?>
						<div class="cc">
							<p><?php echo Yii::t('holes_view', 'INFO_IF_DEFECT_FIXED') ?></p>
							<p><?php echo CHtml::link(Yii::t('holes_view', 'SET_AS_FIXED'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
						</div>
               <?php endif; ?>	
						<div class="rc">
							<p><a class="declarationBtn" href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';c.focus()}return false;"><?php Yii::t('holes_view', 'PRINT_CLAIM') ?></a></p>
							<p><?php echo CHtml::link(Yii::t('holes_view', 'CANCEL_REQUEST_MY_CLAIM'), array('notsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
							<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_GIBDD_REPLY_RECEIVED'), array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
						</div>
					<?php else : ?>	
						<div class="cc">
							<p><a href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';c.focus();}return false;" class="declarationBtn"><?= Yii::t('holes_view', 'PRINT_CLAIM') ?></a></p>
							<p><?php echo CHtml::link(Yii::t('holes_view', 'CLAIM_TO_GAI_WAS_SEND'), array('sent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
						</div>
                  <div class="splitter"></div>
					<?php endif; 
	        	break;
					
               
         case Holes::STATE_GIBDDRE:
            if($hole->request_gibdd && $hole->request_gibdd->answers): ?>
					<div class="lc">
						<p><?php echo Yii::t('holes_view', 'INFO_IF_DEFECT_FIXED') ?></p>
						<p><?php echo CHtml::link(Yii::t('holes_view', 'SET_AS_FIXED'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
					</div>
					<div class="cc">
                  <p><?php echo CHtml::link(Yii::t('holes_view', 'NEW_RESPONSE_FROM_GIBDD'), array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
					</div>
                      <div class="splitter"></div>
					<div class="rc">
						<p><?php echo Yii::t('holes_view', 'INFO_IF_BAD_RESPONSE')?></p>
						<p><a href="#" onclick="var c=document.getElementById('prosecutor_form2');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;"><?php echo Yii::t('holes_view', 'INFO_TO_PROSECUTOR')?></a></p>
						<div class="pdf_form" id="prosecutor_form2"<?= isset($_GET['show_prosecutor_form2']) ? ' style="display: block;"' : '' ?>>								
						<?php $this->renderPartial('_form_prosecutor',Array('hole'=>$hole)); ?>	
						</div>
					</div>
				<?php else : ?>							
					<div class="lc">
					<?php if (!$hole->request_gibdd) : ?>
						<p><?php echo Yii::t('holes_view', 'INFO_REQUEST_TO_GIBDD')?></p>
					<?php elseif(!$hole->request_gibdd->answers) : ?>
						<p><?php echo CHtml::link(Yii::t('holes_view', 'CANCEL_REQUEST_MY_CLAIM'), array('notsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
					<?php endif; ?>	
					</div>
					<div class="cc">
					<p><a href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="declarationBtn"><?= Yii::t('holes_view', 'PRINT_CLAIM') ?></a></p>
					<?php if (!$hole->request_gibdd) : ?>
					<p><?php echo CHtml::link(Yii::t('holes_view', 'CLAIM_TO_GAI_WAS_SEND'), array('sent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
					<?php elseif(!$hole->request_gibdd->answers) : ?>									
						<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_GIBDD_REPLY_RECEIVED'), array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
					<?php else : ?>
					<p><?php echo CHtml::link(Yii::t('holes_view', 'NEW_RESPONSE_FROM_GIBDD'), array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
					<?php endif; ?>
				</div>
            <div class="splitter"></div>
			   <?php endif; 	
			   break;
					
         case Holes::STATE_ACHTUNG:
            if($hole->request_gibdd): ?>
					<div class="cc">
						<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_GIBDD_REPLY_RECEIVED'), array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
						<p><?php echo CHtml::link(Yii::t('holes_view', 'CANCEL_REQUEST_MY_CLAIM'), array('notsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
						<p><?= Yii::t('holes_view', 'INFO_IF_DEFECT_FIXED') ?></p>
						<p><?php echo CHtml::link(Yii::t('holes_view', 'SET_AS_FIXED'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
					</div>
                     <div class="splitter"></div>
					<div class="rc">
						<p><?php echo Yii::t('holes_view', 'IF_DEFECT_NOT_FIXED') ?></p>
						<p><a href="#" onclick="var c=document.getElementById('prosecutor_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="declarationBtn"><?= Yii::t('holes_view', 'WRITE_CLAIM_TO_PROSECUTOR') ?></a></p>
						<p><?php echo CHtml::link(Yii::t('holes_view', 'WAS_SEND_TO_PROSECUTOR'), array('prosecutorsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
					</div>
					<div class="pdf_form" id="prosecutor_form"<?= isset($_GET['show_prosecutor_form']) ? ' style="display: block;"' : '' ?>>
					<?php $this->renderPartial('_form_prosecutor_achtung',Array('hole'=>$hole)); ?>						
					</div>
   			<?php else : ?>						
					<div class="cc">
						<p><a href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="declarationBtn"><?= Yii::t('holes_view', 'PRINT_CLAIM') ?></a></p>
						<?php if (!$hole->request_gibdd) : ?>
						<p><?php echo CHtml::link(Yii::t('holes_view', 'CLAIM_TO_GAI_WAS_SEND'), array('sent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
						<?php else : ?>
						<p><?php echo CHtml::link(Yii::t('holes_view', 'CANCEL_REQUEST_MY_CLAIM'), array('notsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
						<?php endif; ?>
					</div>
               <div class="splitter"></div>
				<?php endif;	
				break;


   		case Holes::STATE_PROSECUTOR:
            if($hole->request_prosecutor): ?>
					<div class="lc">
						<p><?= Yii::t('holes_view', 'INFO_IF_DEFECT_FIXED') ?></p>
						<p><?php echo CHtml::link(Yii::t('holes_view', 'SET_AS_FIXED'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
					</div>
					<div class="cc">
						<p><?php echo CHtml::link(Yii::t('holes_view', 'SETNULL_REQUEST_TO_PROSECUTOR'), array('prosecutornotsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
					</div>
				<?php else : ?>							
					<div class="cc">
						<?php if($hole->request_gibdd): ?>
							<p><?php echo Yii::t('holes_view', 'INFO_IF_DEFECT_FIXED') ?></p>
							<p><?php echo CHtml::link(Yii::t('holes_view', 'SET_AS_FIXED'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
						<?php else: ?>	
						<p><?php echo Yii::t('holes_view', 'NEED_SEND_TO_PROSECUTOR') ?></p>
						<?php endif; ?>	
					</div>
               <div class="splitter"></div>
					<div class="rc">
						<p><?php echo Yii::t('holes_view', 'IF_DEFECT_NOT_FIXED') ?></p>
						<p><a href="#" onclick="var c=document.getElementById('prosecutor_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="declarationBtn"><?php echo Yii::t('holes_view', 'WRITE_CLAIM_TO_PROSECUTOR') ?></a></p>
						<p><?php echo CHtml::link(Yii::t('holes_view', 'WAS_REQUEST_TO_PROSECUTOR'), array('prosecutorsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
					</div>
					<div class="pdf_form" id="prosecutor_form"<?= isset($_GET['show_prosecutor_form']) ? ' style="display: block;"' : '' ?>>
					<?php $this->renderPartial('_form_prosecutor_achtung',Array('hole'=>$hole)); ?>													
					</div>
				<?php endif; 
				break;
            
         case Holes::STATE_FIXED:
         default:
				if(!$hole->pictures_fixed){
               echo CHtml::tag('p', array(), CHtml::link(Yii::t('holes_view', 'SETNULL_FIX_REQUEST'), array('defix', 'id'=>$hole->ID),array('class'=>"declarationBtn"))); 
               if ($hole->request_gibdd && !$hole->request_gibdd->answers) 
                  CHtml::tag('p', array(),CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_GIBDD_REPLY_RECEIVED'), array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn"))); 						
               else if($hole->request_gibdd && $hole->request_gibdd->answers);
						CHtml::tag('p', array(),CHtml::link(Yii::t('holes_view', 'NEW_RESPONSE_FROM_GIBDD'), array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")));						
            }
            break;
      }

		          
      if(Yii::app()->user->IsAdmin){?>
         <div class="splitter" style="padding-top: 15px;"></div>
         <p><span class="errortext"><?php echo Yii::t('template', 'INFO_YOU_HAS_ADMIN_RIGHT') ?><br/></span></p>
      <?php } ?>                
      <div class="pdf_form" id="pdf_form"<?= isset($_GET['show_pdf_form']) ? ' style="display: block;"' : '' ?>>
         <a href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="close">&times;</a>
         <div id="gibdd_form"></div>
         <?php $this->renderPartial('_form_gibdd', array('hole'=>$hole, 'gibdd'=>$hole->gibdd));?>
      </div>
   <?php 
   } // если пользователь не авторизирован
   else{ 
   ?>	         
   <div class="progress">
      <?php 
      echo CHtml::tag('p', array(), 
         Yii::t('template', 'INFO_FOR_REGISTER_01').
         CHtml::link(Yii::t('template', 'INFO_FOR_REGISTER_02'), array('review','id'=>$hole->ID),array('class'=>"declarationBtn")));             
      ?>
   </div>
<?php } ?>
</div>
