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
				'actions'=>array('add','newAdd', 'territorialGibdd','smallhole','index','view', 'findSubject', 'findCity', 'map', 'ajaxMap', 'reply'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('getauth','TrackMail','update', 'personal','personalDelete','request','langChange','requestForm','sent','notsent','gibddreply', 'fix', 'defix', 'prosecutorsent', 'prosecutornotsent','delanswerfile','myarea', 'delpicture','selectHoles','sentMany','review'),
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
	public function actionGetauth(){
		$auth=$_POST['auth'];
		$lang=$_POST['lang'];
		$data=Authority::model()->findByPk(array("id"=>$auth,"lang"=>$lang));
		if($data->o_name===""){
			$data->o_name="Начальнику";
		}
		echo "{'address' : '".$data->address."', 'name' : '".$data->o_name."','index':'".$data->index."'}";
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

	public function actionTest(){

//select all holes uploaded more than 3 days ago and gibdd not sent since then
/*
		$holes = Holes::model()->with('requests_gibdd_not')->with('user')->findAll();
		foreach($holes as $hole){
			if(strlen($hole->user->email)>0){
			echo $hole->user->username." : ".$hole->user->name." - ".$hole->user->last_name." : ".$hole->ID." : ".$hole->ADDRESS." : ".$hole->user->email."<br>\n";
			}
		}
*/
//		$holes = Holes::model()->with('requests_gibdd')->with('answers')->with('hole')->findAll();

//request sent but answear is not recieved
/*
		$requests = HoleRequests::model()->with('answers')->with('hole')->findAll('type="gibdd"');
		foreach($requests as $req){
			if(!count($req->answers) && (time() - $req->date_sent)>345600){
				if(strlen($req->hole->user->email)>0){
					echo $req->hole->user->username." : ".$req->hole->user->name." - ".$req->hole->user->last_name." : ".$req->hole->ID." : ".$req->hole->user->email." : ".$req->hole->ADDRESS."<br>\n";
				}
			}
		}
*/
		return;
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
	public function actionSmallhole(){
		header('Access-Control-Allow-Origin: *');
		$model = new Holes;
		if(isset($_POST['umail'])){
			$users = UserGroupsUser::model()->findAllByAttributes(array(),"email=:email",array(":email"=>$_POST['umail']));
			if(count($users)==0){
				$umodel=new UserGroupsUser('autoregistration');
				$umodel->username=$_POST['umail'];
				$umodel->name=$_POST['uname'];
				$umodel->email=$_POST['umail'];
				$umodel->password=$this->randomPassword();
				if($umodel->save()){$model->USER_ID=$umodel->primaryKey;}
			}else{
				$model->USER_ID=$users[0]->id;
			}


			$model->LATITUDE = $_POST['poslat'];
			$model->LONGITUDE = $_POST['poslon'];
			$model->ADDRESS = $_POST['haddress'];
			$model->TYPE_ID = $_POST['deftype'];
			$model->DATE_CREATED = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
			$model->PREMODERATED = (Yii::app()->user->level > 50) ? 1 : 0; 	
//			$tran = $model->dbConnection->beginTransaction();
//			if ($model->validate()) {
				if($model->save()){
//					$tran->commit();
//					echo $model->primaryKey;
					if($model->savePictures()){
					echo "<script>window.parent.postMessage('Дефект завантажено на сайт УкрЯма. Вам відправлено електронного листа з посиланням для підтвердження адреси електронної пошти. Будь ласка, підтвердіть її для продовження роботи з дефектом. Дякуемо!','http://".parse_url($_SERVER['HTTP_REFERER'],PHP_URL_HOST)."');</script>";
					}else{echo "Couldn't save pictures";}
				}
//			}else{echo "Couldn't validate!";}

		}else{

			//выставляем центр на карте по координатам IP юзера
			$request = new CHttpRequest;
			$geoIp = new EGeoIP();
			$geoIp->locate($request->userHostAddress); 	
			//echo ($request->userHostAddress);
			if ($geoIp->longitude) $model->LATITUDE=$geoIp->longitude;
			if ($geoIp->latitude) $model->LONGITUDE=$geoIp->latitude;
			$page=$this->renderPartial("smallhole", array('model'=>$model),true);
			echo $page;
		}
	}
	public function actionNewAdd(){
      $this->layout = '//layouts/header_blank';
		$model = new Holes;
		$model->USER_ID = Yii::app()->user->id;
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
		$address=split(", ",$_POST['Holes']['ADDRESS']);
		foreach($address as $sub){
			$name=mb_strtolower($sub,'UTF-8');
			$region=Region::model()->find('LOWER(name) like :name',array(':name'=>$name));
			echo $region->name;
	}

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
/*
	if(isset($_POST['Holes'])) {
		$address=split(", ",$_POST['Holes']['ADDRESS']);
		foreach($address as $sub){
			$name=mb_strtolower($sub,'UTF-8');
			$region=Region::model()->find('LOWER(name) like :name',array(':name'=>$name));
			echo $region->name;
		}
	}
*/
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
	
	public function actionReply($id=null){
		$this->layout='//layouts/header_user';
		$hole=$this->loadModel($id);
		$this->render('reply',array('hole'=>$this->loadModel($id)));
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
	public function actionRequestForm($id)
	{
		$lang=$_POST['lang'];
		$holetype=$_POST['hole_type'];
		$auth=$_POST[$lang.'_auth'];
		$to_name=$_POST[$lang.'_to_name'];
		$to_address=$_POST[$lang.'_to_address'];
		$to_index=$_POST[$lang.'_to_index'];
		$from=$_POST[$lang.'_from'];
		$postaddress=$_POST[$lang.'_postaddress'];
		$signature=$_POST[$lang.'_signature'];

		$model=$this->loadModel($id);
		$pics=array();
		$photos="";
		$ulang=Yii::app()->user->getLanguage();
		if($lang=="ru"){
			Yii::app()->setLanguage("ru");
			$lang="ru";
		}else{
			Yii::app()->setLanguage("uk_ua");
			$lang="ua";
		}

		$_data = array(
			"ref" => "$id",
			"to_name" =>$to_name,
			"to_address"=>$to_address,
			"to_index"=>$to_index,
			"from_name"=>$from,
			"from_address"=>$postaddress,
			"when"=>strftime("%e ".Yii::t('month', date("n"))." %Y", $model->DATE_CREATED ? $model->DATE_CREATED : time()),
			"where"=>$request->address,
			"date"=>strftime("%e ".Yii::t('month', date("n"))." %Y", time()),
			"init"=>$signature,
			"c_photos"=>count($pics),
			"files"=>$photos
		);
				if($request->html)
				{
					header('Content-Type: text/html; charset=utf8', true);
					$printer = Yii::app()->Printer;
//					echo $printer->printHTML($_data, $formType, $lang);
					$name="$formType"."_$lang";
					$tplname = YiiBase::getPathOfAlias($printer->params['templates'])."/dyplates/gai_".$name.".php";
					$css = file_get_contents(YiiBase::getPathOfAlias($printer->params['templates'])."/dyplates/gai_".$formType.".css"); 
					$html = $this->renderFile($tplname,$_data,true);
					$html = "<style>$css</style>\n$html";
					echo $html;
					return;
				}//end print html
				else
				{//print pdf
					$printer = Yii::app()->Printer;
//					$filename="ukryama-".date("Y-m-d_G-i-s");
//					echo $printer->printPDF($_data, $formType, $lang, $filename);

					$name="$formType"."_$lang";
					$tplname = YiiBase::getPathOfAlias($printer->params['templates'])."/dyplates/gai_".$name.".php";
					if(file_exists($tplname)){
						$css = file_get_contents(YiiBase::getPathOfAlias($printer->params['templates'])."/dyplates/gai_".$formType.".css"); 
						$html = $this->renderFile($tplname,$_data,true);

						$outname="ukryama-".date("Y-m-d_G-i-s");
						echo $printer->printH2P($html, $css, $outname);
						return;
					}
				}//end print pdf

//		echo "Lang: $lang\n Holetype: $holetype\nAuth: $auth\nTo Name: $to_name\nTo Address: $to_address\nTo Index: $to_index\n";
//		echo "From: $from\nPost Address: $postaddress\nSignature: $signature\n";
/*
		if ($id){
				$gibdd=GibddHeads_ua::model()->findByPk((int)$id);
			$holemodel=Holes::model()->findAllByPk(explode(',',$holes));
			if ($type=='gibdd') 
				$this->renderPartial('_form_gibdd_manyholes',Array('holes'=>$holemodel, 'gibdd'=>$gibdd));
		}
		//else echo "Выбирите отдел ГИБДД";
*/
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
				$images=array();
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
							if($request->html){
								$photos =$photos."<tr><td colspan=2>".Yii::t('holes_view', 'PICTURE').' '.$pnum.' '.Yii::t('holes_view', 'PICTURE_TO').' №'.$id.'<br><img height="500px" src="'.$pfile.'"></td></tr><tr><td colspan=2 class="smv-spacer"></td></tr>'."\n";
							}else{
$photos =$photos."<tr><td colspan=2>".Yii::t('holes_view', 'PICTURE').' '.$pnum.' '.Yii::t('holes_view', 'PICTURE_TO').' №'.$id.'<br><img height="500px" src="data:image/jpg;base64,'.base64_encode(file_get_contents(Yii::getPathOfAlias('webroot').$pfile)).'"></td></tr><tr><td colspan=2 class="smv-spacer"></td></tr>'."\n";
							}
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
//					echo $printer->printHTML($_data, $formType, $lang);
					$name="$formType"."_$lang";
					$tplname = YiiBase::getPathOfAlias($printer->params['templates'])."/dyplates/gai_".$name.".php";
					$css = file_get_contents(YiiBase::getPathOfAlias($printer->params['templates'])."/dyplates/gai_".$formType.".css"); 
					$html = $this->renderFile($tplname,$_data,true);
					$html = "<style>$css</style>\n$html";
					echo $html;
					return;
				}//end print html
				else
				{//print pdf
					$printer = Yii::app()->Printer;
//					$filename="ukryama-".date("Y-m-d_G-i-s");
//					echo $printer->printPDF($_data, $formType, $lang, $filename);

					$name="$formType"."_$lang";
					$tplname = YiiBase::getPathOfAlias($printer->params['templates'])."/dyplates/gai_".$name.".php";
					if(file_exists($tplname)){
						$css = file_get_contents(YiiBase::getPathOfAlias($printer->params['templates'])."/dyplates/gai_".$formType.".css"); 
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

	public function trackMail($id){
		$http=new Http;
		$url="http://services.ukrposhta.com/barcodesingle/default.aspx?ctl00%24centerContent%24scriptManager=ctl00%24centerContent%24scriptManager%7Cctl00%24centerContent%24btnFindBarcodeInfo&__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwULLTEzNTgyOTE0MTcPZBYCZg9kFgICAw9kFgYCAQ8WAh4FY2xhc3MFBmxvZ29VS2QCAw8WAh4HVmlzaWJsZWgWAmYPZBYCAgEPZBYCAgEPDxYCHgRUZXh0BVXQktGW0LTRgdGC0LXQttC10L3QvdGPINC%2F0LXRgNC10YHQuNC70LDQvdC90Y8g0L%2FQvtGI0YLQvtCy0LjRhSDQstGW0LTQv9GA0LDQstC70LXQvdGMZGQCBQ9kFgYCAQ8PFgIfAgUe0KjQsNC90L7QstC90ZYg0LrQu9GW0ZTQvdGC0LghZGQCAg8WAh4JaW5uZXJodG1sBcEEJm5ic3A7Jm5ic3A7Jm5ic3A7Jm5ic3A7Jm5ic3A70JLQuCDQvNC%2B0LbQtdGC0LUg0LTRltC30L3QsNGC0LjRgdGPINC%2F0YDQviDQvNGW0YHRhtC10LfQvdCw0YXQvtC00LbQtdC90L3RjyDRgtCwINGB0YLQsNC9INC%2F0L7RiNGC0L7QstC%2B0LPQviDQstGW0LTQv9GA0LDQstC70LXQvdC90Y8sINGJ0L4gPGJyLz4g0YDQvtC30YjRg9C60YPRlNGC0YzRgdGPLCDRgyDQsdGD0LTRjC3Rj9C60LjQuSDQt9GA0YPRh9C90LjQuSDQtNC70Y8g0JLQsNGBINGH0LDRgS4gPGJyIC8%2BIDxiciAvPiZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwO9CG0L3RhNC%2B0YDQvNCw0YbRltGOINC80L7QttC90LAg0L7RgtGA0LjQvNCw0YLQuCDQv9GA0L46IDxiciAvPmQCAw9kFggCAQ8WAh8DBY0TJm5ic3A7Jm5ic3A7Jm5ic3A7Jm5ic3A7Jm5ic3A7LSDQstC90YPRgtGA0ZbRiNC90ZYg0YDQtdGU0YHRgtGA0L7QstCw0L3RliDQv9C%2B0YjRgtC%2B0LLRliDQstGW0LTQv9GA0LDQstC70LXQvdC90Y8sINGJ0L4g0L%2FQtdGA0LXRgdC40LvQsNGO0YLRjNGB0Y8g0LIg0LzQtdC20LDRhSDQo9C60YDQsNGX0L3QuDs8YnIvPg0KICAgJm5ic3A7Jm5ic3A7Jm5ic3A7Jm5ic3A7Jm5ic3A7LSDQvNGW0LbQvdCw0YDQvtC00L3RliDRgNC10ZTRgdGC0YDQvtCy0LDQvdGWINC%2F0L7RiNGC0L7QstGWINCy0ZbQtNC%2F0YDQsNCy0LvQtdC90L3Rjywg0YnQviDQv9C10YDQtdGB0LjQu9Cw0Y7RgtGM0YHRjyDQt9CwINC80LXQttGWINCj0LrRgNCw0ZfQvdC4Ozxici8%2BDQogICAmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDstINC80ZbQttC90LDRgNC%2B0LTQvdGWINGA0LXRlNGB0YLRgNC%2B0LLQsNC90ZYg0L%2FQvtGI0YLQvtCy0ZYg0LLRltC00L%2FRgNCw0LLQu9C10L3QvdGPLCDRidC%2BINC90LDQtNGW0LnRiNC70Lgg0LIg0KPQutGA0LDRl9C90YMuPGJyLz48YnIvPg0KICAgJm5ic3A7Jm5ic3A7Jm5ic3A7Jm5ic3A7Jm5ic3A70KPQstC10LTRltGC0YwsINCx0YPQtNGMLdC70LDRgdC60LAsINCx0LXQtyDQv9GA0L7Qv9GD0YHQutGW0LImbmJzcDsg0YLQsCDRltC90YjQuNGFINGB0LjQvNCy0L7Qu9GW0LIg0L%2FQvtCy0L3QuNC5ICZuYnNwOzxiPjEzLdGB0LjQvNCy0L7Qu9GM0L3QuNC5PC9iPiDQsdGD0LrQstC10L3Qvi3RhtC40YTRgNC%2B0LLQuNC5INC90L7QvNC10YAgKNGI0YLRgNC40YXQutC%2B0LTQvtCy0LjQuSDRltC00LXQvdGC0LjRhNGW0LrQsNGC0L7RgCkg0L%2FQvtGI0YLQvtCy0L7Qs9C%2BINCy0ZbQtNC%2F0YDQsNCy0LvQtdC90L3Rjywg0Y%2FQutC40Lkg0LfQsNC30L3QsNGH0LXQvdC%2BJm5ic3A7INC90LAmbmJzcDsg0JLQsNGI0L7QvNGDJm5ic3A7INGA0L7Qt9GA0LDRhdGD0L3QutC%2B0LLQvtC80YMmbmJzcDsg0LTQvtC60YPQvNC10L3RgtGWJm5ic3A7ICjQutCw0YHQvtCy0L7QvNGDINGH0LXQutGDLCDRgNC%2B0LfRgNCw0YXRg9C90LrQvtCy0ZbQuSDQutCy0LjRgtCw0L3RhtGW0Zcg0YLQvtGJ0L4pINGC0LAg0L3QsNGC0LjRgdC90ZbRgtGMINC90LAg0LrQvdC%2B0L%2FQutGDIMKr0J%2FQvtGI0YPQusK7INCw0LHQviDQutC70LDQstGW0YjRgyDCq0VudGVywrsuPGJyLz48YnIvPg0KICAgJm5ic3A7Jm5ic3A7Jm5ic3A7Jm5ic3A7Jm5ic3A70IbQtNC10L3RgtC40YTRltC60LDRgtC%2B0YAmbmJzcDsg0LzRltC20L3QsNGA0L7QtNC90L7Qs9C%2BINC%2F0L7RiNGC0L7QstC%2B0LPQviDQstGW0LTQv9GA0LDQstC70LXQvdC90Y8g0LzRltGB0YLQuNGC0YwgMTMg0YHQuNC80LLQvtC70ZbQsiwg0Lcg0L3QuNGFOiAxLdC5INGC0LAgMi3QuSDRgdC40LzQstC%2B0LvQuCDigJQg0LHRg9C60LLQuDsg0LcgMy3Qs9C%2BINC%2F0L4gMTEt0Lkg4oCUINGG0LjRhNGA0Lg7IDEyLdC5INGC0LAgMTMt0Lkg4oCUINCx0YPQutCy0LgsINGP0LrRliDQstGW0LTQvtCx0YDQsNC20LDRjtGC0Ywg0LrQvtC0INC60YDQsNGX0L3QuC3QstGW0LTQv9GA0LDQstC90LjQutCwICjQvdCw0L%2FRgNC40LrQu9Cw0LQsIFVBIOKAlCDQo9C60YDQsNGX0L3QsCwgUlUg4oCUINCg0L7RgdGW0Y8sIFVTIOKAlCDQodCo0JAsIElMIOKAlCDQhtC30YDQsNGX0LvRjCDRgtC%2B0YnQvikuPGJyLz48YnIvPg0KICAgJm5ic3A7Jm5ic3A7Jm5ic3A7Jm5ic3A7Jm5ic3A70IbQtNC10L3RgtC40YTRltC60LDRgtC%2B0YAg0LLQvdGD0YLRgNGW0YjQvdGM0L7Qs9C%2BINC%2F0L7RiNGC0L7QstC%2B0LPQviDQstGW0LTQv9GA0LDQstC70LXQvdC90Y8g0YHQutC70LDQtNCw0ZTRgtGM0YHRjyDQtyAxMyDRhtC40YTRgC48YnIvPjxici8%2BDQogICAmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDsmbmJzcDvQn9GA0LjQutC70LDQtCDQvdC%2B0LzQtdGA0LAg0LzRltC20L3QsNGA0L7QtNC90L7Qs9C%2BINCy0ZbQtNC%2F0YDQsNCy0LvQtdC90L3RjzogIENBMTIzNDU2Nzg5VUEgPGJyLz4NCiAgICZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwOyZuYnNwO9Cy0L3Rg9GC0YDRltGI0L3RjNC%2B0LPQviDQstGW0LTQv9GA0LDQstC70LXQvdC90Y86ICAwMTIzNDU2Nzg5MTIzZAIFDw8WAh8CBQrQn9C%2B0YjRg9C6ZGQCCQ9kFgJmD2QWAgIBDw8WAh8CBT%2FQl9Cw0YfQtdC60LDQudGC0LUsINCS0LDRiCDQt9Cw0L%2FQuNGCINC%2B0LHRgNC%2B0LHQu9GP0ZTRgtGM0YHRjyFkZAILDxYCHwMFmwLQhtC90YTQvtGA0LzQsNGG0ZbRjyDQv9GA0L4g0L3QsNGP0LLQvdGW0YHRgtGMINGC0LAg0YHRgtCw0L0g0L%2FQtdGA0LXRgdC40LvQsNC90L3RjyAg0L%2FQvtGI0YLQvtCy0LjRhSDQstGW0LTQv9GA0LDQstC70LXQvdGMINC%2F0L7RgdGC0ZbQudC90L4g0L7QvdC%2B0LLQu9GO0ZTRgtGM0YHRjyDQuSDQt9Cx0LXRgNGW0LPQsNGU0YLRjNGB0Y8g0LIg0YHQuNGB0YLQtdC80ZYg0L%2FRgNC%2B0YLRj9Cz0L7QvCA2INC80ZbRgdGP0YbRltCyINC3INC80L7QvNC10L3RgtGDINGA0LXRlNGB0YLRgNCw0YbRltGXZGTCkbz1y7PQThxiRimSt4almYGvlQ%3D%3D&ctl00%24centerContent%24txtBarcode=$id&__ASYNCPOST=true&ctl00%24centerContent%24btnFindBarcodeInfo=%D0%9F%D0%BE%D1%88%D1%83%D0%BA";
		$a= $http->http_request(array('url'=>$url,'return'=>'array', 'cookie'=>true));
		$cookie = $a['headers']['SET-COOKIE'];
		$url="http://services.ukrposhta.com/barcodesingle/DownloadInfo.aspx";
		$data= $http->http_request(array('url'=>$url, 'cookie'=>$cookie));
		
		$page=split("\n",$data);
		$print=0;
		foreach($page as $line){
			if($print){
				$result=strip_tags($line)."\n";
				$print=0;
			}
			if(strstr("$line","divInfo")){
				if(strstr("$line","</div>")){
					$result= strip_tags($line)."\n";
				}else{
					$print=1;
				}
			}
		}
		if(strstr($result,"вручене за довіреністю")){
			return date("Y-m-d",strtotime(mb_substr(strstr($result,"вручене за довіреністю "),23,10,'UTF-8')));
		}else{
			return 0;
		}
	}	
	public function actionTrackMail($id){
		$date=$this->trackMail($id);
		echo $date;
	}	

	public function actionSent($id)
	{
		$model=$this->loadModel($id);

		if(isset($_POST['auth'])){
			$auth=$_POST['auth'];
		}else{
			$auth=$_POST['auth2'];
		}
		$ref=$_POST['ref'];
		if(isset($_POST['when'])){
			if(strlen($_POST['when'])>0){
				$date= $_POST['when'];
				if($_POST['mailtype']==1){
					$hrs = new HoleRequestSent;
					$hrs->hole_id=$id;
					$hrs->user_id=Yii::app()->user->id;
					$hrs->status=1;
					$hrs->ddate=$date;

					$date= strtotime($date);
					$hrs->req=$model->sendRequest($date,$auth,$ref);
					$hrs->save();
				}else{
					$hrs = new HoleRequestSent;
					$hrs->status=2;
					$hrs->user_id=Yii::app()->user->id;
					$hrs->hole_id=$id;

					$date= strtotime($date);
					$hrs->req=$model->sendRequest($date,$auth,$ref);

					$hrs->save();
				}
//				$date= strtotime($date);
//				$model->makeRequest('gibdd',$date);
			}else{
				//do nothing
			}
		}elseif(strlen($_POST['when2'])>0){
		}else{
			//do nothing
		}

		if(isset($_POST['holesent'])){
			$data=$_POST['holesent'];
			$data['hole']=$id;
			$data['user']=Yii::app()->user->id;
			$hrs = new HoleRequestSent;

			$date= strtotime($_POST['when2']);
			$hrs->req=$model->sendRequest($date,$auth,$ref);
			$hrs->user_id=$data['user'];
			$hrs->rcpt=$data['rcpt'];
			$hrs->mailme=$data['mailme'];
			$hrs->hole_id=$data['hole'];
			$date=$this->trackMail($data['rcpt']);
			if($date){
				$hrs->status=1;
				$hrs->ddate=$date;
			}else{
				$hrs->status=0;
			}
			$hrs->save();
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
		$hrs = HoleRequestSent::model()->find("hole_id=:id and user_id=:user_id",array(":id"=>$id,":user_id"=>Yii::app()->user->id));
		if($hrs != null){
			$hrs->delete();
		}
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
			'types'=>HoleTypes::model()->findAll(Array('condition'=>'t.published=1 and t.lang="ua"')),
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
