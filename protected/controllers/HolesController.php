<?php

class HolesController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/main';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'userGroupsAccessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('add','newAdd', 'territorialGibdd','index','view', 'findSubject', 'findCity', 'map', 'ajaxMap'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('update', 'personal','personalDelete','request','langChange','requestForm','sent','notsent','gibddreply', 'fix', 'defix', 'prosecutorsent', 'prosecutornotsent','delanswerfile','myarea', 'delpicture','selectHoles','sentMany','review'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('delete', 'moderate'),
				'groups'=>array('root', 'admin', 'moder'), 
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin', 'itemsSelected'),
				'groups'=>array('root',), 
			),
			array('deny',  // deny all users
				'users'=>array('*'),  
			),
		);
	}
	
	public function actionFindSubject()
	{
	
		$q = $_GET['term'];
       if (isset($q)) {
           $criteria = new CDbCriteria;           
           $criteria->params = array(':q' => '%'.trim($q).'%');
           $criteria->condition = 'name LIKE (:q)'; 
           $RfSubjects = RfSubjects::model()->findAll($criteria); 
 
           if (!empty($RfSubjects)) {
               $out = array();
               foreach ($RfSubjects as $p) {
                   $out[] = array(
                       // expression to give the string for the autoComplete drop-down
                       //'label' => preg_replace('/('.$q.')/i', "<strong>$1</strong>", $p->name_full),  
                       'label' =>  $p->name_full,  
                       'value' => $p->name_full,
                       'id' => $p->id, // return value from autocomplete
                   );
               }
               echo CJSON::encode($out);
               Yii::app()->end();
           }
       }
	}
	
	public function actionFindCity()
		{
		
			$q = $_GET['Holes']['ADR_CITY'];
		   if (isset($q)) {
			   $criteria = new CDbCriteria;           
			   $criteria->params = array(':q' => trim($q).'%');
			   if (isset($_GET['Holes']['ADR_SUBJECTRF']) && $_GET['Holes']['ADR_SUBJECTRF']) $criteria->condition = 'ADR_CITY LIKE (:q) AND ADR_SUBJECTRF='.$_GET['Holes']['ADR_SUBJECTRF']; 
			   else $criteria->condition = 'ADR_CITY LIKE (:q)'; 
			   $criteria->group='ADR_CITY';
			   $Holes = Holes::model()->findAll($criteria); 
	 
			   if (!empty($Holes)) {
				   $out = array();
				   foreach ($Holes as $p) {
					   $out[] = array(
						   // expression to give the string for the autoComplete drop-down
						   //'label' => preg_replace('/('.$q.')/i', "<strong>$1</strong>", $p->name_full),  
						   'label' =>  $p->ADR_CITY,    
						   'value' => $p->ADR_CITY,
					   );
				   }
				   echo CJSON::encode($out);
				   Yii::app()->end();
			   }
		   }
		}	

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
      //$this->layout = '//layouts/header_blank';

		$cs=Yii::app()->getClientScript();
      $cs->registerCssFile(Yii::app()->request->baseUrl.'/css/hole_view.css'); 
      $cs->registerScriptFile('http://api-maps.yandex.ru/1.1/index.xml?key='.$this->mapkey);
      $jsFile = CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'view_script.js');
      $cs->registerScriptFile($jsFile);
        
		$this->render('view',array(
			'hole'=>$this->loadModel($id),
		));
	}
	
	public function actionReview($id)
	{
		$this->redirect(Array('view','id'=>(int)$id));
	}	

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionNewAdd(){
      $this->layout = '//layouts/header_blank';
		$model = new Holes;
		$this->render('holeform', array('model'=>$model));
	}
	public function actionAdd()
	{
      $this->layout = '//layouts/header_blank';
		$model=new Holes;
		$model->USER_ID = Yii::app()->user->id;
		$model->DATE_CREATED = time();
		$cs=Yii::app()->getClientScript();
      $cs->registerCssFile(Yii::app()->request->baseUrl.'/css/add_form.css');

		if(isset($_POST['Holes'])){
			$model->attributes = $_POST['Holes'];
         		if($model->USER_ID===0 || $model->USER_ID === null){
				$users = UserGroupsUser::model()->findAllByAttributes(array(),"email=:email",array(":email"=>$_POST['Holes']['EMAIL']));
				if(count($users)==0){
					$umodel=new UserGroupsUser('autoregistration');
					$umodel->username=$_POST['Holes']['EMAIL'];
					$umodel->name=$_POST['Holes']['FIRST_NAME'];
					$umodel->last_name=$_POST['Holes']['LAST_NAME'];
					$umodel->email=$_POST['Holes']['EMAIL'];
					$umodel->password=$this->randomPassword();
					if($umodel->save()){$model->USER_ID=$umodel->primaryKey;}
					}else{
						$model->USER_ID=$users[0]->id;
					}
			}
         $model->DATE_CREATED = strtotime($_POST['defectdate']);
         if (!$model->DATE_CREATED)
            $model->DATE_CREATED = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
			if ($model->DATE_CREATED < time()-(7 * 86400))
            $model->addError("DATE_CREATED",Yii::t('template', 'DATE_CANT_BE_PAST', array('{attribute}'=>$model->getAttributeLabel('DATE_CREATED')))); 

         $subj=RfSubjects::model()->SearchID(trim($model->STR_SUBJECTRF));
			if($subj) 
            $model->ADR_SUBJECTRF=$subj;
			else 
            $model->ADR_SUBJECTRF=0;
			$model->ADR_CITY=trim($model->ADR_CITY);
			
			$model->PREMODERATED = (Yii::app()->user->level > 50) ? 1 : 0; 

         
         $tran = $model->dbConnection->beginTransaction();
			if ($model->validate(null, false)) {
				if($model->save() && $model->savePictures()){
               $tran->commit();
					$this->redirect(array('view','id'=>$model->ID));
            }               
			}
		}
		else {
			//выставляем центр на карте по координатам IP юзера
			$request = new CHttpRequest;
			$geoIp = new EGeoIP();
			$geoIp->locate($request->userHostAddress); 	
			//echo ($request->userHostAddress);
			if ($geoIp->longitude) $model->LATITUDE=$geoIp->longitude;
			if ($geoIp->latitude) $model->LONGITUDE=$geoIp->latitude;
		}

		$this->render('add',array(
			'model'=>$model,			
		));
	}
	
	private function randomPassword() {
	    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	    for ($i = 0; $i < 8; $i++) {
	        $n = rand(0, count($alphabet)-1);
        	$pass[$i] = $alphabet[$n];
	    }
	    return $pass;
	}

	//Список ГИБДД возле ямы
	public function actionTerritorialGibdd()
	{
	if(isset($_POST['Holes'])) {
		$address=split(", ",$_POST['Holes']['ADDRESS']);
		foreach($address as $sub){
			$name=mb_strtolower($sub,'UTF-8');
			$region=Region::model()->find('LOWER(name) like :name',array(':name'=>$name));
			echo $region->name;
		}
	}
/*
		if(isset($_POST['Holes'])) {
			$model=new Holes;
			$model->attributes=$_POST['Holes'];
			//думаю, 4 первых буквы хватает для однозначного определения области
			$s=mb_substr(mb_strtolower(trim($model->STR_SUBJECTRF),'UTF-8'),0,6,'UTF-8');
			$subj=RfSubjects::model()->find('LOWER(name_full) LIKE :name', array(':name'=>'%'.$s.'%'));

			if(Yii::app()->user->getLanguage()=="ru"){
				$data=GibddHeads_ru::model()->findAll('subject_id=:id',array(':id'=>$subj->id));
			}else{
				$data=GibddHeads_ua::model()->findAll('subject_id=:id',array(':id'=>$subj->id));
			}
		    foreach($data as $value) { 
				echo CHtml::tag('option',
					array('value'=>$value->id),CHtml::encode($value->gibdd_name),true);
		    }
			
		}
*/
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$this->layout='//layouts/header_user';
		
		$model=$this->loadChangeModel($id);
		
      if($model->STATE != Holes::STATE_FRESH)	
         throw new CHttpException(403,'Редактирование не нового дефекта запрещено');


		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$cs=Yii::app()->getClientScript();
      $cs->registerCssFile(Yii::app()->request->baseUrl.'/css/add_form.css');

		if(isset($_POST['Holes']))
		{
			$model->attributes=$_POST['Holes'];
         $model->DATE_CREATED = strtotime($_POST['defectdate']);
			if ($model->DATE_CREATED < time()-(7 * 86400))
            $model->addError("DATE_CREATED",Yii::t('template', 'DATE_CANT_BE_PAST', array('{attribute}'=>$model->getAttributeLabel('DATE_CREATED')))); 

			if ($model->STR_SUBJECTRF){
				$subj=RfSubjects::model()->SearchID(trim($model->STR_SUBJECTRF));
				if($subj) $model->ADR_SUBJECTRF=$subj;
			}
			if ($model->validate(null, false)) {
   			if($model->save() && $model->savePictures())
   				$this->redirect(array('view','id'=>$model->ID));
         }
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}
	
	
	public function actionGibddreply($id=null, $holes=null)
	{
		$this->layout='//layouts/header_user';
		$count=0;
		$firstAnswermodel=Array();
		$models=Array();
		if (!$holes){
         $model=$this->loadModel($id);
         $model->scenario='gibdd_reply';
         if($model->STATE!=Holes::STATE_INPROGRESS && $model->STATE!=Holes::STATE_ACHTUNG && !$model->request_gibdd)	
            throw new CHttpException(403,'Доступ запрещен.');
         $models[]=$model;
		}	
		else{ 
         $models=Holes::model()->findAllByPk(explode(',',$holes));
      }
		
      foreach ($models as $i=>$model){
			if($model->STATE!=Holes::STATE_INPROGRESS && $model->STATE!=Holes::STATE_ACHTUNG && !$model->request_gibdd) {
			   unset ($models[$i]); continue;
         }
			$answer=new HoleAnswers;
         $answer->date = time();
			if (isset($_GET['answer']) && $_GET['answer'])
				$answer=HoleAnswers::model()->findByPk((int)$_GET['answer']);

			$answer->request_id=$model->request_gibdd->id;
	
			if(isset($_POST['HoleAnswers'])){					
				$answer->attributes=$_POST['HoleAnswers'];
            $answer->date = strtotime($_POST['answerdate']);
   		//	if ($model->date < $model->request_gibdd->DATE_STATUS))
          //     $model->addError("DATE_CREATED",Yii::t('template', 'DATE_CANT_BE_PAST', array('{attribute}'=>$model->getAttributeLabel('DATE_CREATED')))); 


            //if (isset($_POST['HoleAnswers']['results'])) $answer->results=$_POST['HoleAnswers']['results'];
				$answer->request_id=$model->request_gibdd->id;
				            
				if ($firstAnswermodel) 
               $answer->firstAnswermodel=$firstAnswermodel;
               
            $tran = $answer->dbConnection->beginTransaction();
				if($answer->save()){
					if ($model->STATE==Holes::STATE_INPROGRESS || $model->STATE==Holes::STATE_ACHTUNG)
						$model->STATE=Holes::STATE_GIBDDRE;
					$model->GIBDD_REPLY_RECEIVED=1;
					if (!$model->DATE_STATUS) $model->DATE_STATUS=time();
					if ($model->update()){					
						if ($count==0) 
                     $firstAnswermodel=$answer;
						$count++;
						$links[]=CHtml::link($model->ADDRESS,Array('view','id'=>$model->ID));
                  $tran->commit();						
						if (!$holes) 
                     $this->redirect(array('view','id'=>$model->ID));						
					}
				}					
				
			}
			else {
				if (!$answer->isNewRecord) 
               $answer->results=CHtml::listData($answer->results,'id','id');
			}
		}
		
		if ($holes && $count) {
         if($count) 
            Yii::app()->user->setFlash('user', 'Успешная загрузка ответа ГАИ на ямы: <br/>'.implode('<br/>',$links).'<br/><br/><br/>');
         else 
            Yii::app()->user->setFlash('user', 'Произошла ошибка! Ни одного ответа не загружено');
         $this->redirect(array('personal')); 
		}	

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$cs=Yii::app()->getClientScript();
      $cs->registerCssFile(Yii::app()->request->baseUrl.'/css/add_form.css');
      $cs->registerScriptFile('http://api-maps.yandex.ru/1.1/index.xml?key='.$this->mapkey);
		
		$this->render('gibddreply',array(
			'models'=>$models,
			'answer'=>$answer,
			'jsplacemarks'=>'',
		));
	}
	
	public function actionFix($id)
	{
		$this->layout='//layouts/header_user';
		
		$model=$this->loadModel($id);
		$fixmodel=new HoleFixeds;
		$fixmodel->user_id = Yii::app()->user->id;
		$fixmodel->hole_id = $model->ID;
		$fixmodel->date_fix = time();
  

		if (!$model->isUserHole && Yii::app()->user->level < 50){
			if ($model->STATE==Holes::STATE_FIXED || !$model->request_gibdd || !$model->request_gibdd->answers || $model->user_fix)
				throw new CHttpException(403,'Доступ запрещен.');
		}		
		elseif ($model->STATE==Holes::STATE_FIXED && $model->user_fix)
				throw new CHttpException(403,'Доступ запрещен.');		
			
		$model->scenario='fix';
		
		$cs=Yii::app()->getClientScript();
      $cs->registerCssFile(Yii::app()->request->baseUrl.'/css/add_form.css');
      $cs->registerScriptFile('http://api-maps.yandex.ru/1.1/index.xml?key='.$this->mapkey);

		if(isset($_POST['Holes']))
		{
			$model->STATE=Holes::STATE_FIXED;
			$model->COMMENT2=$_POST['Holes']['COMMENT2'];
         $fixmodel->comment = $model->COMMENT2; 
			$model->DATE_STATUS=time();
         $fixmodel->date_fix = strtotime($_POST['fixdate']);   
         
         $tran = $model->dbConnection->beginTransaction();
         
         
			if ($model->save() && $model->savePictures() && $fixmodel->save()){		
            $tran->commit();
				$this->redirect(array('view','id'=>$model->ID));
			}
		}

		$this->render('fix_form',array(
			'model'=>$model,	
         'fixmodel'=>$fixmodel,
			'newimage'=>new PictureFiles
		));
	}	
	
	public function actionDefix($id)
	{
		$model=$this->loadModel($id);
		if (!$model->user_fix && Yii::app()->user->level < 80)
			throw new CHttpException(403,'Доступ запрещен.');
			
		$model->updateSetinprogress();
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=>$model->ID));
	}	

	//удаление ямы админом или модером
	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest && (isset($_POST['id']) || (isset($_POST['DELETE_ALL']) && $_POST['DELETE_ALL'])))
		{
			if (!isset($_POST['DELETE_ALL'])){
			$id=$_POST['id'];
			// we only allow deletion via POST request
			$model=$this->loadModel($id);
			if (isset($_POST['banuser']) && $_POST['banuser']){
				$reason="Забанен";
				$period=100000;
					$usermodel = UserGroupsUser::model()->findByPk($model->USER_ID); 
					$usermodel->setScenario('ban');
					// check if you are trying to ban a user with an higher level
					if ($usermodel->relUserGroupsGroup->level >= Yii::app()->user->level)
						Yii::app()->user->setFlash('user', 'Вы не можете банить пользователей с уровнем выше или равным вашему.');
					else {
						$usermodel->ban = date('Y-m-d H:i:s', time() + ($period * 86400));
						$usermodel->ban_reason = $reason;
						$usermodel->status = UserGroupsUser::BANNED;
						if ($usermodel->update())
							Yii::app()->user->setFlash('user', '{$usermodel->username}\ акаунт забанен до {$usermodel->ban}.');
						else
							Yii::app()->user->setFlash('user', 'Произошла ошибка попробуйте немного позднее');
					}
				}
				
				$model->delete();
			}
			else {
				$holes=Holes::model()->findAll('id IN ('.$_POST['DELETE_ALL'].')');
				$ok=0;
				foreach ($holes as $model)
					if ($model->delete()) $ok++;
				if ($ok==count($holes))  echo 'ok';
			}			

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect($_SERVER['HTTP_REFERER']);
		}
		elseif (Yii::app()->user->groupName=='root'){
			$model=Holes::model()->findByPk((int)$_GET['id']);
			if ($model) $model->delete();
			}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	
	//удаление ямы пользователем
	public function actionPersonalDelete($id)
	{
        $model=$this->loadChangeModel($id);
        $currentUser = UserGroupsUser::model()->findByPk(Yii::app()->user->id);

        if ($currentUser && (($currentUser->id == $model->user->id) || ($currentUser->level > 1))) {
            $model->delete();
        }
        else {
            throw new CHttpException(403,'Доступ запрещен.');
        }

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_POST['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('personal'));
	}	
	
	//форма ГИБДД
	public function actionRequestForm($id, $type, $holes)
	{
		if ($id){
				$gibdd=GibddHeads_ua::model()->findByPk((int)$id);
			$holemodel=Holes::model()->findAllByPk(explode(',',$holes));
			if ($type=='gibdd') 
				$this->renderPartial('_form_gibdd_manyholes',Array('holes'=>$holemodel, 'gibdd'=>$gibdd));
		}
		//else echo "Выбирите отдел ГИБДД";
	}	

	//генерация запросов в ГАИ
	public function actionLangchange($id=null){
		$lang=$_POST['lang'];
		if($id){$model=$this->loadModel($id);};
		$head=$model->gibdd->id;
		if($lang=="ru"){
			$gibdd=GibddHeads_ru::model()->findByPk($head);
		}else{
			$gibdd=GibddHeads_ua::model()->findByPk($head);
		}
		echo $gibdd->address."|".$gibdd->post_dative."|".$gibdd->fio_dative;
	}

	public function actionRequest($id=null)
	{
			if ($id) $model=$this->loadModel($id);
			else $model=new Holes;
			$request=new HoleRequestForm;
			if(isset($_POST['HoleRequestForm']))
			{

				$request->attributes=$_POST['HoleRequestForm'];
				$pics=array_keys($_POST['chpk']);
				$_images = array();
				setlocale(LC_ALL, 'ru_RU.UTF-8');

				$photos = "";
				$pnum=1;

				foreach($model->pictures_fresh as $picture){
					$pid = $picture->id;
					foreach($pics as $pic){
						if($pic==$pid){
							$pfile=$picture->original;
							$image=Yii::app()->image->load(Yii::getPathOfAlias('webroot').$pfile);

							if($image->__get("height")>$image->__get("width")){
								$image->rotate(-90);
								$fname=$pfile;

								preg_match('/[^?]*/', $fname, $matches);
							        $string = $matches[0];
								$pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);
							        $filenamepart = $pattern[count($pattern)-1][0];
								preg_match('/[^?]*/', $filenamepart, $matches);
								$lastdot = $pattern[count($pattern)-1][1];
								$filename = substr($string, 0, $lastdot-1);
							        $pfile=$filename.".rotated.".$matches[0];
								$image->save(Yii::getPathOfAlias('webroot').$pfile);
							}
							$photos =$photos."<tr><td colspan=2>".Yii::t('holes_view', 'PICTURE').' '.$pnum.' '.Yii::t('holes_view', 'PICTURE_TO').' №'.$id.'<br><img height="500px" src="'.$pfile.'"></td></tr><tr><td colspan=2 class="smv-spacer"></td></tr>'."\n";
						$pnum++;
						}
					}
				}
				$lang=Yii::app()->user->getLanguage();
				if($request->lang=="ru"){
					Yii::app()->setLanguage("ru");
					$lang="ru";
				}else{
					Yii::app()->setLanguage("uk_ua");
					$lang="ua";
				}
				$_data = array(
					"ref" => "$id",
					"to_name" => $request->to_name,
					"to_address"=>$request->to_address,
					"from_name"=>$request->from,
					"from_address"=>$request->postaddress,
					"when"=>strftime("%e ".Yii::t('month', date("n"))." %Y", $model->DATE_CREATED ? $model->DATE_CREATED : time()),
					"where"=>$request->address,
					"date"=>strftime("%e ".Yii::t('month', date("n"))." %Y", time()),
					"init"=>$request->signature,
					"c_photos"=>count($pics),
					"files"=>$photos
				);
				if($request->form_type=="prosecutor2"){
					$formType="prosecutor2";
				}else{
					$formType=$model->type['alias'];
				}

				if($request->html)
				{
					header('Content-Type: text/html; charset=utf8', true);
					$printer = Yii::app()->Printer;
					echo $printer->printHTML($_data, $formType, $lang);
					return;
				}//end print html
				else
				{//print pdf
					$printer = Yii::app()->Printer;
//					$filename="ukryama-".date("Y-m-d_G-i-s");
//					echo $printer->printPDF($_data, $formType, $lang, $filename);
					$name="$formType.$lang";
					$tplname = YiiBase::getPathOfAlias($printer->params['templates'])."/templates/".$name.".tpl.php";
					if(file_exists($tplname)){
						$css = file_get_contents(YiiBase::getPathOfAlias($printer->params['templates'])."/css/".$formType.".css"); 
						$html = $this->renderFile($tplname,$_data,true);
						$outname="ukryama-".date("Y-m-d_G-i-s");
						echo $printer->printH2P($html, $css, $outname);
						return;
					}
				}//end print pdf
			}		
	}		

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->layout='//layouts/header_default';
		
		$model=new Holes('search');		
		
		$model->unsetAttributes();  // clear any default values
		$model->PREMODERATED=1;
		if(isset($_POST['Holes']) || isset($_GET['Holes']))
			$model->attributes= Yii::app()->request->getParam('Holes');//isset($_POST['Holes']) ? $_POST['Holes'] : $_GET['Holes'];

		//if ($model->ADR_CITY=="Город") 
      //   $model->ADR_CITY='';

		$dataProvider=$model->search();

		$this->render('index',array(
			'model'=>$model,
			'dataProvider'=>$dataProvider,
		));
	}
	
	public function actionModerate($id)
	{
		if (!isset($_GET['PREMODERATE_ALL'])){
			$model=$this->loadModel($id);
			if (!$model->PREMODERATED) {
				$model->PREMODERATED=1;
				if ($model->update()) echo 'ok';
				}
			elseif (isset($_GET['ajax']) && $_GET['ajax']=='holes-grid'){
				$model->PREMODERATED=0;
				if ($model->update()) echo 'ok';	
			}
		}
		else {
			$holes=Holes::model()->findAll('id IN ('.$_GET['PREMODERATE_ALL'].')');
			$ok=0;
			foreach ($holes as $model)
			if (!$model->PREMODERATED) {
				$model->PREMODERATED=1;
				if ($model->update()) $ok++;
				}
			if ($ok==count($holes))  echo 'ok';
		}
	}
	
	public function actionSent($id)
	{
		$model=$this->loadModel($id);
		$model->makeRequest('gibdd');

			
		if(isset($_POST['holesent'])){
			$data=$_POST['holesent'];
			$data['hole']=$id;
			if($data['mailme']=="on"){
				if(strlen(Yii::app()->user->email)>0){
					$data['user']=Yii::app()->user->id;
				}
			}
			$a= var_export($data, true)."\n";
			file_put_contents(Yii::getPathOfAlias('webroot')."/upload/logs/rcpt.log",$a,FILE_APPEND);

		}
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=>$model->ID));
	}
	
	public function actionSentMany($holes)
	{		
		$holesmodels=Holes::model()->findAllByPk(explode(',',$holes));
		$count=0;
		$links=Array();
		foreach ($holesmodels as $model){
			if ($model->makeRequest('gibdd')) {
				$count++;
				$links[]=CHtml::link($model->ADDRESS,Array('view','id'=>$model->ID));
				}
		}		
		if($count) Yii::app()->user->setFlash('user', 'Успешное изменение статуса ям: <br/>'.implode('<br/>',$links).'<br/><br/><br/>');
		else Yii::app()->user->setFlash('user', 'Произошла ошибка! Ни одной ямы не изменено');
		if(!isset($_GET['ajax']))
			$this->redirect(array('personal'));
	}		
	
	public function actionProsecutorsent($id)
	{
		$model=$this->loadModel($id);
		$model->makeRequest('prosecutor');
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=>$model->ID));
	}
	
	public function actionProsecutornotsent($id)
	{
		$model=$this->loadModel($id);
		$model->updateRevokep();
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=>$model->ID));
	}		
	
	public function actionNotsent($id)
	{
		$model=$this->loadModel($id);
		$model->updateRevoke();
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=>$model->ID));
	}	
	
	//удаление изображения
	public function actionDelpicture($id)
	{
			$picture=HolePictures::model()->findByPk((int)$id);
			
			if (!$picture)
				throw new CHttpException(404,'The requested page does not exist.');
				
			if ($picture->user_id!=Yii::app()->user->id && Yii::app()->user->level < 80 && $picture->hole->USER_ID!=Yii::app()->user->id)
				throw new CHttpException(403,'Доступ запрещен.');
				
			$picture->delete();			
			
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=>$picture->hole->ID));
		
	}		
	
	//удаление файла ответа гибдд
	public function actionDelanswerfile($id)
	{
			$file=HoleAnswerFiles::model()->findByPk((int)$id);
			
			if (!$file)
				throw new CHttpException(404,'The requested page does not exist.');
				
			if ($file->answer->request->user_id!=Yii::app()->user->id && !Yii::app()->user->isModer && $file->answer->request->hole->STATE !=Holes::STATE_GIBDDRE)
				throw new CHttpException(403,'Доступ запрещен.');
				
			$file->delete();			
			
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=>$file->answer->request->hole->ID));
		
	}	
	
	public function actionPersonal()
	{
		$this->layout='//layouts/header_user';
	
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		$user=$this->user;
		
		if(isset($_POST['Holes']) || isset($_GET['Holes']))
			$model->attributes=isset($_POST['Holes']) ? $_POST['Holes'] : $_GET['Holes'];
		
      $cs=Yii::app()->getClientScript();
      $cs->registerCssFile(Yii::app()->request->baseUrl.'/css/holes_list.css');        
      $cs->registerCssFile(Yii::app()->request->baseUrl.'/css/hole_view.css');
      $cs->registerScriptFile(CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'holes_selector.js'));
      $cs->registerScriptFile('http://www.vertstudios.com/vertlib.min.js');        
      $cs->registerScriptFile(CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'StickyScroller'.DIRECTORY_SEPARATOR.'StickyScroller.min.js'));
		$cs->registerScriptFile(CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'StickyScroller'.DIRECTORY_SEPARATOR.'GetSet.js'));
		//$holes=Array();
		//$all_holes_count=0;		
			
		$this->render('personal',array(
			'model'=>$model,
			'user'=>$user
		));
	}
	
	public function actionSelectHoles($del=false)
	{
		$gibdds=Array();
		$del=filter_var($del, FILTER_VALIDATE_BOOLEAN);	
		if (isset($_POST['holes'])) $holestr=$_POST['holes'];
		else $holestr=''; 
		if ($holestr=='all' && $del) {
			Yii::app()->user->setState('selectedHoles', Array());
			//Yii::app()->end();
			}
		else{	
			$holes=explode(',',$holestr);
			for ($i=0;$i<count($holes);$i++) {$holes[$i]=(int)$holes[$i]; if(!$holes[$i]) unset($holes[$i]);}
			
			$selected=Yii::app()->user->getState('selectedHoles', Array());
			if (!$del){
				$newsel=array_diff($holes, $selected);
				$selected=array_merge($selected, $newsel);
			}
			else {	
				$newsel=array_intersect($selected, $holes);
				foreach ($newsel as $key=>$val) unset($selected[$key]);
			}
			Yii::app()->user->setState('selectedHoles', $selected);

				if ($selected) $gibdds=GibddHeads_ua::model()->with('holes')->findAll('holes.id IN ('.implode(',',$selected).')');

		}
		$this->renderPartial('_selected', Array('gibdds'=>$gibdds,'user'=>Yii::app()->user->userModel));
		
		//print_r(Yii::app()->user->getState('selectedHoles'));
	}	
	
	public function actionMyarea()
	{
		$user=Yii::app()->user;
		$area=$user->userModel->hole_area;
		if (!$area)	$this->redirect(array('/profile/myarea'));
		
		$this->layout='//layouts/header_user';
	
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		
		if(isset($_POST['Holes']) || isset($_GET['Holes']))
			$model->attributes=isset($_POST['Holes']) ? $_POST['Holes'] : $_GET['Holes'];
		
		
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile(Yii::app()->request->baseUrl.'/css/holes_list.css');
		$cs->registerCssFile(Yii::app()->request->baseUrl.'/css/hole_view.css');
        $cs->registerScriptFile(CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'holes_selector.js'));
		$cs->registerScriptFile('http://www.vertstudios.com/vertlib.min.js');        
        $cs->registerScriptFile(CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'StickyScroller'.DIRECTORY_SEPARATOR.'StickyScroller.min.js'));
		$cs->registerScriptFile(CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'StickyScroller'.DIRECTORY_SEPARATOR.'GetSet.js'));              
		
		$holes=Array();
		$all_holes_count=0;		
					
		$this->render('myarea',array(
			'model'=>$model,
			'user'=>$user,
			'area'=>$area
		));
	}		
	
	public function actionMap()
	{
		$this->layout='//layouts/header_blank';
	
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_POST['Holes']))
			$model->attributes=$_POST['Holes'];
			if ($model->ADR_CITY=="Город") $model->ADR_CITY='';
			
		$this->render('map',array(
			'model'=>$model,
			'types'=>HoleTypes::model()->findAll(Array('condition'=>'t.published=1', 'order'=>'ordering')),
		));
	}
	
	public function actionAjaxMap()
	{
		$criteria=new CDbCriteria;
		/// Фильтрация по масштабу позиции карты
		
		if (isset($_GET['zoom'])) $ZOOM=$_GET['zoom'];
		else $ZOOM=14;
		
		if ($ZOOM < 3) { $_GET['left']=-190; $_GET['right']=190;}

		if (!isset ($_GET['bottom']) || !isset ($_GET['left']) || !isset ($_GET['right']) || !isset ($_GET['top'])) Yii::app()->end();
		
		if (isset ($_GET['bottom'])) $criteria->addCondition('LATITUDE > '.(float)$_GET['bottom']);
		if (isset ($_GET['left'])) $criteria->addCondition('LONGITUDE > '.(float)$_GET['left']);	 	
		if (isset ($_GET['right'])) $criteria->addCondition('LONGITUDE < '.abs((float)$_GET['right']));		
		if (isset ($_GET['top'])) $criteria->addCondition('LATITUDE < '.abs((float)$_GET['top']));		
		if (isset ($_GET['exclude_id']) && $_GET['exclude_id']) $criteria->addCondition('ID != '.(int)$_GET['exclude_id']); 
		if (!Yii::app()->user->isModer) $criteria->compare('PREMODERATED',1);
	
		/// Фильтрация по состоянию ямы
		if(isset($_GET['Holes']['STATE']) && $_GET['Holes']['STATE'])
		{
			$criteria->addInCondition('STATE', $_GET['Holes']['STATE']);
		}
		
		/// Фильтрация по типу ямы
		if(isset($_GET['Holes']['type']) && $_GET['Holes']['type'])
		{
			$criteria->addInCondition('TYPE_ID', $_GET['Holes']['type']);
		}
		
		$criteria->with=Array('type');
		
		$markers = Holes::model()->findAll($criteria);	
		

		
		if ($ZOOM >=14) $ZOOM=30;
				
		$singleMarkers = array();
		$clusterMarkers = array();
		
		// Minimum distance between markers to be included in a cluster, at diff. zoom levels
		$DISTANCE = (7000000 >> $ZOOM) / 100000;
		
		// Loop until all markers have been compared.
		while (count($markers)) {
			$marker  = array_pop($markers);
			$cluster = array();
		
			// Compare against all markers which are left.
			foreach ($markers as $key => $target) {
				$pixels = abs($marker->LONGITUDE-$target->LONGITUDE) + abs($marker->LATITUDE-$target->LATITUDE);
		
				// If the two markers are closer than given distance remove target marker from array and add it to cluster.
				if ($pixels < $DISTANCE) {
					unset($markers[$key]);
					$cluster[] = $target;
				}
			}
		
			// If a marker has been added to cluster, add also the one we were comparing to.
			if (count($cluster) > 0) {
				$cluster[] = $marker;
				$clusterMarkers[] = $cluster;
			} else {
				$singleMarkers[] = $marker;
			}
		}
		
		
		$markers=Array();
		foreach($singleMarkers as &$hole)
		{
			if(!isset($_REQUEST['skip_id']) || $_REQUEST['skip_id'] != $hole['ID'])
			{
				$markers[]=Array('id'=>$hole->ID, 'type'=>$hole->type->alias, 'lat'=>$hole->LONGITUDE, 'lng'=>$hole->LATITUDE, 'state'=>$hole->STATE);				
			}
		}
		
		$clusters=Array();
		foreach($clusterMarkers as $markerss)
		{
			$lats=Array();
			$lngs=Array();
				foreach($markerss as &$hole)
					{
						$lats[]=$hole->LONGITUDE;
						$lngs[]=$hole->LATITUDE;
					}
			sort($lats);
			sort($lngs);
			$center_lat=($lats[0]+$lats[count($lats)-1])/2;
			$center_lng=($lngs[count($lngs)-1]+$lngs[0])/2;
			
			
				$clusters[]=Array('count'=>count($markerss), 				
				'lat'=>$center_lat, 'lng'=>$center_lng, 
				);				
				
		}
		echo $_GET['jsoncallback'].'({"clusters": '.CJSON::encode($clusters).', "markers": '.CJSON::encode($markers).' })';
		
		
		Yii::app()->end();		
		
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		
		if (isset($_GET['pageSize'])) {
			Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
			unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
		}
		
		$this->layout='//layouts/header_user';
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Holes']))
			$model->attributes=$_GET['Holes'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	
	public function actionItemsSelected()
	{
	if (isset ($_POST['submit_mult']) && isset($_POST['itemsSelected'])) {
		if ($_POST['submit_mult']=='Удалить'){
			foreach ( $_POST['itemsSelected'] as $id){
				$model=Holes::model()->findByPk((int)$id);
				if ($model) $model->delete();
			}
		}

		if ($_POST['submit_mult']=='Отмодерировать'){
			foreach ( $_POST['itemsSelected'] as $id){
				$model=Holes::model()->findByPk((int)$id);
				if ($model) {
				$model->PREMODERATED=1;
				$model->update();
				}
			}
		}

		if ($_POST['submit_mult']=='Демодерировать'){
			foreach ( $_POST['itemsSelected'] as $id){
				$model=Holes::model()->findByPk((int)$id);
				if ($model) {
				$model->PREMODERATED=0;
				$model->update();
				}
			}
		}
    }
		if (!isset($_GET['ajax'])) $this->redirect($_SERVER['HTTP_REFERER']);
	}	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Holes::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	//Лоадинг модели для пользовательских изменений
	public function loadChangeModel($id)
	{
		$model=Holes::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		elseif(!$model->IsUserHole && !Yii::app()->user->level>80)	
			throw new CHttpException(403,'Доступ запрещен.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='holes-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
