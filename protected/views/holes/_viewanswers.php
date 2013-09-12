<?php 

foreach($hole->requests_gibdd as $request){
   if($request->answers){
      foreach($request->answers as $answer){		
         echo CHtml::openTag('div', array('class'=>'after'));
         if($answer->comment)
            echo CHtml::tag('div', array('class'=>'comment'), $answer->comment);

			echo CHtml::openTag('h2'); 
         echo Yii::t('holes_view', 'HOLE_GIBDDREPLY_USER_DATE', array('{0}'=>$request->user->fullname, '{1}'=>date('d.m.Y',$answer->date)));
         if ($request->user_id==Yii::app()->user->id && $hole->STATE == Holes::STATE_GIBDDRE)
            echo ' '.CHtml::link(Yii::t('template', 'EDIT'), Array('gibddreply','id'=>$hole->ID,'answer'=>$answer->id), Array('class'=>'declarationBtn')).'<br />';
         echo CHtml::closeTag('h2');
         
			if ($answer->files_other){
            foreach($answer->files_other as $file){
               echo CHtml::openTag('p');  
               echo CHtml::link($file->file_name, $answer->filesFolder.'/'.$file->file_name, Array('class'=>'declarationBtn')); 
               if ($request->user_id==Yii::app()->user->id && $hole->STATE =='gibddre')
                  echo CHtml::link(Yii::t('template', 'DELETE_FILE'), Array('delanswerfile','id'=>$file->id), Array('class'=>'declarationBtn')).'<br />';
            }
            echo CHtml::closeTag('p').'<br />';
         }

         foreach($answer->files_img as $img){
            echo CHtml::openTag('p');
              
				if ($request->user_id==Yii::app()->user->id && $hole->STATE =='gibddre')
					echo CHtml::link(Yii::t('template', 'DELETE_IMAGE'), Array('delanswerfile','id'=>$img->id), Array('class'=>'declarationBtn')).'</br>';

				echo CHtml::link(CHtml::image($answer->filesFolder.'/thumbs/'.$img->file_name), 
               $answer->filesFolder.'/'.$img->file_name, 
					array('class'=>'holes_pict',
                  'rel'=>'answer_'.$answer->id, 
                  'title'=> Yii::t('template', 'UPLOAD_AT_DATE', array('{0}'=>Y::dateTimeFromTime($answer->createdate))), 
                  //'Ответ ГАИ от '.date(C_DATEFORMAT, $answer->date)
               ));
                
            echo CHtml::closeTag('p').'<br />';
			} 
		    echo CHtml::closeTag('div');
      }
   }
}

?>