<?php

/**
 * This is the model class for table "{{holes}}".
 *
 * The followings are the available columns in table '{{holes}}':
 * @property string $ID
 * @property string $USER_ID
 * @property double $LATITUDE
 * @property double $LONGITUDE
 * @property string $ADDRESS
 * @property string $STATE
 * @property string $DATE_CREATED
 * @property string $DATE_SENT
 * @property string $DATE_STATUS
 * @property string $COMMENT1
 * @property string $COMMENT2
 * @property string $TYPE_ID
 * @property string $ADR_SUBJECTRF
 * @property string $ADR_CITY
 * @property string $COMMENT_GIBDD_REPLY
 * @property integer $GIBDD_REPLY_RECEIVED
 * @property integer $PREMODERATED
 * @property string $DATE_SENT_PROSECUTOR
 */
class Holes extends CActiveRecord
{
   const STATE_FRESH = 'fresh';
   const STATE_INPROGRESS = 'inprogress';
   const STATE_ACHTUNG = 'achtung';
   const STATE_GIBDDRE = 'gibddre';
   const STATE_FIXED = 'fixed';
   const STATE_PROSECUTOR = 'prosecutor';
	/**
	 * Returns the static model of the specified AR class.
	 * @return Holes the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public $WAIT_DAYS; 	
	public $PAST_DAYS;	
	public $NOT_PREMODERATED;	
	public $STR_SUBJECTRF;	
	public $deletepict=Array();
	public $counts;
	public $state_to_filter;
	public $time;
	public $limit;
	public $offset=0;
	public $type_alias;
	public $showUserHoles;
	public $username;
	public $FIRST_NAME;
	public $LAST_NAME;
	public $EMAIL;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{holes}}';
	}
	
	public $ADR_CITY='Город';
	
	public function getParams(){
		return array(
					'big_sizex'      => 1024,
					'big_sizey'      => 1024,
					'medium_sizex'   => 600,
					'medium_sizey'   => 450,
					'small_sizex'    => 240,
					'small_sizey'    => 160,
					'premoderated'   => 0,
					'min_delay_time' => 60
		);
	}	

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('USER_ID, LATITUDE, LONGITUDE, ADDRESS, DATE_CREATED, TYPE_ID, gibdd_id', 'required'),
			array('GIBDD_REPLY_RECEIVED, PREMODERATED, TYPE_ID, NOT_PREMODERATED, createdate, updatedate', 'numerical', 'integerOnly'=>true),
			array('LATITUDE, LONGITUDE', 'numerical'),
			array('USER_ID, STATE, DATE_CREATED, DATE_SENT, DATE_STATUS, ADR_SUBJECTRF, DATE_SENT_PROSECUTOR', 'length', 'max'=>10),
			array('ADR_CITY', 'length', 'max'=>50),
			array('STR_SUBJECTRF, username', 'length'),
			array('COMMENT1, COMMENT2, COMMENT_GIBDD_REPLY, deletepict, upploadedPictures, request_gibdd, showUserHoles', 'safe'),	
			array('upploadedPictures', 'file', 'types'=>'jpg, jpeg, png, gif','maxFiles'=>10, 'allowEmpty'=>true, 'on' => 'update, import, fix'),
			array('upploadedPictures', 'file', 'types'=>'jpg, jpeg, png, gif','maxFiles'=>10, 'allowEmpty'=>false, 'on' => 'insert'),
         
         array('DATE_CREATED', 'compare', 'compareValue'=>time(), 'operator'=>'<=', 'allowEmpty'=>false , 
            'message'=>Yii::t('template', 'DATE_CANT_BE_FUTURE'),
         ),
         /*array('DATE_CREATED', 'compare', 'compareValue'=>time() - (7 * 86400), 'operator'=>'>', 'allowEmpty'=>false , 
            'message'=>Yii::t('template', 'DATE_CANT_BE_PAST'),
         ),*/

         
			//array('upploadedPictures', 'required', 'on' => 'insert', 'message' => 'Необходимо загрузить фотографии'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID, USER_ID, LATITUDE, LONGITUDE, ADDRESS, STATE, DATE_CREATED, DATE_SENT, DATE_STATUS, COMMENT1, COMMENT2, TYPE_ID, ADR_SUBJECTRF, ADR_CITY, COMMENT_GIBDD_REPLY, GIBDD_REPLY_RECEIVED, PREMODERATED, DATE_SENT_PROSECUTOR', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'subject'=>array(self::BELONGS_TO, 'RfSubjects', 'ADR_SUBJECTRF'),
			'requests'=>array(self::HAS_MANY, 'HoleRequests', 'hole_id'),
			'pictures'=>array(self::HAS_MANY, 'HolePictures', 'hole_id', 'order'=>'pictures.type, pictures.ordering'),
			'pictures_fresh'=>array(self::HAS_MANY, 'HolePictures', 'hole_id', 'condition'=>'pictures_fresh.type="fresh"','order'=>'pictures_fresh.ordering'),
			'pictures_fixed'=>array(self::HAS_MANY, 'HolePictures', 'hole_id', 'condition'=>'pictures_fixed.type="fixed"','order'=>'pictures_fixed.ordering'),
			'user_pictures_fixed'=>array(self::HAS_MANY, 'HolePictures', 'hole_id', 'condition'=>'user_pictures_fixed.type="fixed" AND user_pictures_fixed.user_id='.Yii::app()->user->id,'order'=>'user_pictures_fixed.ordering'),
			'request_gibdd'=>array(self::HAS_ONE, 'HoleRequests', 'hole_id', 'condition'=>'request_gibdd.type="gibdd" AND request_gibdd.user_id='.Yii::app()->user->id),
			'request_prosecutor'=>array(self::HAS_ONE, 'HoleRequests', 'hole_id', 'condition'=>'request_prosecutor.type="prosecutor" AND user_id='.Yii::app()->user->id),
			'requests_gibdd'=>array(self::HAS_MANY, 'HoleRequests', 'hole_id', 'condition'=>'requests_gibdd.type="gibdd"','order'=>'requests_gibdd.date_sent ASC'),
			'request_sent'=>array(self::HAS_MANY, 'HoleRequestSent', 'hole_id','order'=>'request_sent.ddate ASC'),
			'requests_prosecutor'=>array(self::HAS_MANY, 'HoleRequests', 'hole_id', 'condition'=>'requests_prosecutor.type="prosecutor"','order'=>'date_sent ASC'),
			'fixeds'=>array(self::HAS_MANY, 'HoleFixeds', 'hole_id','order'=>'fixeds.date_fix DESC'),
			'user_fix'=>array(self::HAS_ONE, 'HoleFixeds', 'hole_id', 'condition'=>'user_fix.user_id='.Yii::app()->user->id),
			'type'=>array(self::BELONGS_TO, 'HoleTypes', 'TYPE_ID'),
			'user'=>array(self::BELONGS_TO, 'UserGroupsUser', 'USER_ID'),		
//			'gibdd'=>Yii::app()->user->getLanguage()=="ua"?array(self::BELONGS_TO, 'GibddHeads_ua', 'gibdd_id'):array(self::BELONGS_TO, 'GibddHeads_ru', 'gibdd_id'),
			'gibdd'=>array(self::BELONGS_TO, 'GibddHeads_ua', 'gibdd_id'),
			'selected_lists'=>array(self::MANY_MANY, 'UserSelectedLists',
               '{{user_selected_lists_holes_xref}}(hole_id,list_id)'),
            'comments_cnt'=> array(self::STAT, 'Comment', 'owner_id', 'condition'=>'owner_name="Holes" AND status < 2'),   
            'comments'=> array(self::HAS_MANY, 'Comment', 'owner_id', 'condition'=>'owner_name="Holes"'), 
         
		);
	}

      
	public function behaviors(){
      return array( 'CAdvancedArBehavior' => array(
         'class' => 'application.extensions.CAdvancedArBehavior'));
   }

	public static function getAllstates()	{
   	$arr=Array();
   	$arr['fresh']      = Yii::t('holes','HOLES_STATE_FRESH_FULL');
   	$arr['inprogress'] = Yii::t('holes','HOLES_STATE_INPROGRESS_FULL');
   	$arr['fixed']      = Yii::t('holes','HOLES_STATE_FIXED_FULL');
   	$arr['achtung']    = Yii::t('holes','HOLES_STATE_ACHTUNG_FULL');
   	$arr['gibddre']    = Yii::t('holes','HOLES_STATE_GIBDDRE_FULL');
   	$arr['prosecutor'] = Yii::t('holes','HOLES_STATE_PROSECUTOR_FULL');
   	return $arr;
	}
	
	public static function getAllstatesShort()	{
   	$arr=Array();
   	$arr['fresh']      = Yii::t('holes','HOLES_STATE_FRESH_SHORT');
   	$arr['inprogress'] = Yii::t('holes','HOLES_STATE_INPROGRESS_SHORT');
   	$arr['fixed']      = Yii::t('holes','HOLES_STATE_FIXED_SHORT');
   	$arr['achtung']    = Yii::t('holes','HOLES_STATE_ACHTUNG_SHORT');
   	$arr['gibddre']    = Yii::t('holes','HOLES_STATE_GIBDDRE_SHORT');
   	$arr['prosecutor'] = Yii::t('holes','HOLES_STATE_PROSECUTOR_SHORT');
   	return $arr;
	}	
	
	public static function getAllstatesMany()	{
   	$arr=Array();
   	$arr['fresh']      = Yii::t('holes','HOLES_STATE_FRESH_MANY');
   	$arr['inprogress'] = Yii::t('holes','HOLES_STATE_INPROGRESS_MANY');
   	$arr['fixed']      = Yii::t('holes','HOLES_STATE_FIXED_MANY');
   	$arr['achtung']    = Yii::t('holes','HOLES_STATE_ACHTUNG_MANY');
   	$arr['gibddre']    = Yii::t('holes','HOLES_STATE_GIBDDRE_MANY');
   	$arr['prosecutor'] = Yii::t('holes','HOLES_STATE_PROSECUTOR_MANY');
	  return $arr;
	}	
	
	public function getStateName()	
	{	
		return $this->AllstatesShort[$this->STATE];
	}
	
	public function getIsSelected()	
	{	
		foreach (Yii::app()->user->getState('selectedHoles', Array()) as $id) 
			if ($id==$this->ID) return true;
		return false;	
	}	
	
	public function getFixByUser($id)	
	{	
		foreach ($this->fixeds as $fix){
			if ($fix->user_id==$id) return $fix;
		}
		return null;
	}	
	
	const EARTH_RADIUS_KM = 6373;
	public function getTerritorialGibdd()	
	{	
		if (!$this->subject) return Array();
		$longitude=$this->LONGITUDE;
		$latitude=$this->LATITUDE;		
		$numerator = 'POW(COS(RADIANS(lat)) * SIN(ABS(RADIANS('.$longitude.')-RADIANS(lng))),2)';		
		$numerator .= ' + POW(
		COS(RADIANS('.$latitude.')) * SIN(RADIANS(lat)) - SIN(RADIANS('.$latitude.'))
		* COS(RADIANS(lat))*COS(ABS(RADIANS('.$longitude.')-RADIANS(lng)))
		,2)';
		$numerator = 'SQRT('.$numerator.')';		
		$denominator = 'SIN(RADIANS(lat))*SIN(RADIANS('.$latitude.')) +
		COS(RADIANS(lat))*COS(RADIANS('.$latitude.'))*
		COS(ABS(RADIANS('.$longitude.')-RADIANS(lng)))';		
		$condition = 'ATAN('.$numerator.'/('.$denominator.')) * '.self::EARTH_RADIUS_KM;
		
		$criteria=new CDbCriteria;
		$criteria->select=Array('*', $condition.' as distance');				
		$criteria->condition='lat > 0 AND lng > 0';	
		$criteria->addCondition('moderated = 1 OR author_id='.Yii::app()->user->id);
		if ($this->subject) $criteria->addCondition('subject_id='.$this->subject->id);
		$criteria->order='ABS(distance) ASC';		
		$criteria->having='ABS(distance) < 1000';
		$criteria->limit=5;

		if(Yii::app()->user->getLanguage()=="ru"){
			$gibdds=GibddHeads_ru::model()->findAll($criteria);
			if ($this->subject) array_unshift ($gibdds, $this->subject->gibdd_ru);
		}elseif(Yii::app()->user->getLanguage()=="ua"){
			$gibdds=GibddHeads_ua::model()->findAll($criteria);
			if ($this->subject) array_unshift ($gibdds, $this->subject->gibdd_ua);
		}

		return $gibdds;
	}
		
	
	public function getUpploadedPictures(){
		return CUploadedFile::getInstancesByName('');
	}
	
	public function savePictures(){						
		foreach ($this->deletepict as $pictid) {
			$pictmodel=HolePictures::model()->findByPk((int)$pictid);  
			if ($pictmodel)$pictmodel->delete();
		}

		$imagess=$this->UpploadedPictures;
		$id=$this->ID;
		$prefix='';			
      $path = $_SERVER['DOCUMENT_ROOT'].Yii::app()->params['imagePath'];			
		if (!is_dir($path.'original/'.$id)){
			if(!mkdir($path.'original/'.$id))
			{
				$this->addError('upploadedPictures', Yii::t('errors', 'GREENSIGHT_ERROR_CANNOT_CREATE_DIR'));
				return false;
			}
			if(!mkdir($path.'medium/'.$id))
			{
				unlink($path.'original/'.$id);
				$this->addError('upploadedPictures',Yii::t('errors', 'GREENSIGHT_ERROR_CANNOT_CREATE_DIR'));
				return false;
			}
			if(!mkdir($path.'small/'.$id))
			{
				unlink($path.'original/'.$id);
				unlink($path.'medium/'.$id);
				$this->addError('upploadedPictures',Yii::t('errors', 'GREENSIGHT_ERROR_CANNOT_CREATE_DIR'));
				return false;
			}
		}						

		$_params=$this->params;
		$file_counter = 0;
		$k = $this->ID;			
						
        foreach ($imagess as $_file){
			if(!$_file->hasError)
			{	
				$imgname=rand().'.jpg';
				$image = $this->imagecreatefromfile($_file->getTempName(), $_image_info);
				if(!$image)
				{
					$this->addError('pictures',Yii::t('errors', 'GREENSIGHT_ERROR_UNSUPPORTED_IMAGE_TYPE'));
					return false;
				}
				$aspect = max($_image_info[0] / $_params['big_sizex'], $_image_info[1] / $_params['big_sizey']);
				if($aspect > 1)
				{
					$new_x    = floor($_image_info[0] / $aspect);
					$new_y    = floor($_image_info[1] / $aspect);
					$newimage = imagecreatetruecolor($new_x, $new_y);
					imagecopyresampled($newimage, $image, 0, 0, 0, 0, $new_x, $new_y, $_image_info[0], $_image_info[1]);
					imagejpeg($newimage, $path.'original/'.$id.'/'.$imgname);
				}
				else
				{
					imagejpeg($image, $path.'original/'.$id.'/'.$imgname);
				}
	
				$aspect   = max($_image_info[0] / $_params['medium_sizex'], $_image_info[1] / $_params['medium_sizey']);
				$new_x    = floor($_image_info[0] / $aspect);
				$new_y    = floor($_image_info[1] / $aspect);
				$newimage = imagecreatetruecolor($new_x, $new_y);
				imagecopyresampled($newimage, $image, 0, 0, 0, 0, $new_x, $new_y, $_image_info[0], $_image_info[1]);
				imagejpeg($newimage, $path.'medium/'.$id.'/'.$imgname);
				imagedestroy($newimage);
				$aspect   = min($_image_info[0] / $_params['small_sizex'], $_image_info[1] / $_params['small_sizey']);
				$newimage = imagecreatetruecolor($_params['small_sizex'], $_params['small_sizey']);
				imagecopyresampled
				(
					$newimage,
					$image,
					0,
					0,
					$_image_info[0] > $_image_info[1] ? floor(($_image_info[0] - $aspect * $_params['small_sizex']) / 2) : 0,
					$_image_info[0] < $_image_info[1] ? floor(($_image_info[1] - $aspect * $_params['small_sizey']) / 2) : 0,
					$_params['small_sizex'],
					$_params['small_sizey'],
					ceil($aspect * $_params['small_sizex']),
					ceil($aspect * $_params['small_sizey'])
				);
				imagejpeg($newimage, $path.'small/'.$id.'/'.$imgname);
				imagedestroy($newimage);
				imagedestroy($image);
							
				$imgmodel=new HolePictures;
				$imgmodel->type=$this->scenario=='fix'?'fixed':'fresh'; 
				$imgmodel->filename=$imgname;
				$imgmodel->hole_id=$this->ID;
				$imgmodel->user_id=Yii::app()->user->id;
				$imgmodel->ordering=$imgmodel->lastOrder+1;
				$imgmodel->save();
			}
		}
		return true;			
	}

	public static function imagecreatefromfile($file_name, &$_image_info = array())
	{
		$_image_info = getimagesize($file_name, $_image_additional_info);
		$_image_info['additional'] = $_image_additional_info;
		switch($_image_info['mime'])
		{
			case 'image/jpeg':
			case 'image/pjpg':
			{
				$operator = 'imagecreatefromjpeg';
				break;
			}
			case 'image/gif':
			{
				$operator = 'imagecreatefromgif';
				break;
			}
			case 'image/png':
			case 'image/x-png':
			{
				$operator = 'imagecreatefrompng';
				break;
			}
			default:
			{
				return false;
			}
		}
		return $operator($file_name);
	}	
	
	
	public function updateToprosecutor(){
	
	if ($this->STATE!='achtung') return false;
	$this->DATE_STATUS= time();
	$this->DATE_SENT_PROSECUTOR = time();
	$this->STATE='prosecutor';
	$this->update();
	return true;
	}
	
	public function updateRevokep(){
	
	if ($this->request_prosecutor) {
		$this->request_prosecutor->delete();	
		if (!count(HoleRequests::model()->findAll('hole_id='.$this->ID.' AND type="prosecutor"'))) {
						$this->DATE_STATUS= time();
						$this->DATE_SENT_PROSECUTOR = null;
						$this->STATE='achtung';
						$this->update();
					}
		return true;			
		}
	else return false;	

	}	
	
	public function makeRequest($type,$date){
		$attr='request_'.$type;
		if (!$this->$attr){
			$request=new HoleRequests;
			$request->attributes=Array(
							'hole_id'=>$this->ID,
							'user_id'=>Yii::app()->user->id,
							//'gibdd_id'=>$this->subject ? $this->subject->gibdd->id : 0,
							'gibdd_id'=>$this->gibdd_id,
							'date_sent'=>$date,
							'type'=>$type,
							);
			if ($request->save()){
			if ($type=='gibdd') if ($this->updateSetinprogress()) return true;
			elseif ($type=='prosecutor') if ($this->updateToprosecutor()) return true;
			}
		}
		elseif ($type=='prosecutor' && $this->STATE=='achtung') $this->updateToprosecutor();
		return true;
	}

	
	public function updateSetinprogress()
	{
		if($this->STATE != Holes::STATE_FRESH && !($this->STATE == Holes::STATE_FIXED && !sizeof($this->user_pictures_fixed)))
				{
					return false;
				}
		else {			
			if ($this->user_fix) $this->user_fix->delete();			
			if (count ($this->fixeds) == 0) {
					$this->DATE_STATUS=time();
					if($this->STATE == Holes::STATE_FRESH)  
					{
						if (!$this->DATE_SENT) {
							$this->DATE_SENT = time(); 						
						}
						$this->STATE = Holes::STATE_INPROGRESS;										
					}
					else
					{
						if($this->DATE_SENT)
						{
							$this->STATE = Holes::STATE_INPROGRESS;
						}						
						if($this->DATE_SENT < time() - 37 * 86400)
						{
							$this->STATE = Holes::STATE_ACHTUNG;
						}
						if($this->GIBDD_REPLY_RECEIVED)
						{
							$this->STATE = Holes::STATE_GIBDDRE;
						}
						if($this->DATE_SENT_PROSECUTOR)
						{
							$this->STATE = Holes::STATE_PROSECUTOR;
						}
						if(!$this->DATE_SENT)
						{
							$this->STATE = Holes::STATE_FRESH;
							if ($this->request_gibdd) $this->request_gibdd->delete();
						}
					}
				}
				
			if ($this->update()) return true;
			else return false;
		}	
	}
	
	public function updateRevoke()
	{
		if(!$this->request_gibdd || $this->request_gibdd->answer)
			{
				return false;	
			}
			$this->DATE_STATUS = time();
			$this->request_gibdd->delete();
			if (!count(HoleRequests::model()->findAll('hole_id='.$this->ID.' AND type="gibdd"'))) {
				$this->DATE_SENT = null;
				$this->DATE_STATUS = time();
				$this->STATE = Holes::STATE_FRESH;
				}
			else {
				$this->DATE_SENT = $this->requests_gibdd[0]->date_sent;
			}	
		if ($this->update()) return true;
		else return false;
	}		
	
	
	public function afterFind(){
		//вычисляем количество дней с момента отправки
		if(($this->STATE == Holes::STATE_INPROGRESS || $this->STATE == Holes::STATE_ACHTUNG) && $this->DATE_SENT && !$this->STATE != Holes::STATE_GIBDDRE)
		{
			$this->WAIT_DAYS = 31 - ceil((time() - $this->DATE_SENT) / 86400);	
		}
			
		//отмечаем яму если просроченна
		if ($this->WAIT_DAYS < 0 && $this->STATE == Holes::STATE_INPROGRESS) {
			$this->STATE = Holes::STATE_ACHTUNG;
			$this->update();
		}
		elseif ($this->STATE == Holes::STATE_ACHTUNG && $this->WAIT_DAYS > 0){
			$this->STATE = Holes::STATE_INPROGRESS;
			$this->update();			
		}
		
		if ($this->WAIT_DAYS<0) { 
			$this->PAST_DAYS=abs($this->WAIT_DAYS);
			$this->WAIT_DAYS=0;
		}		
	}
	
	public function beforeDelete(){
		//сначала удаляем все картинки
		foreach ($this->pictures as $picture) $picture->delete();			
		
		//Потом удаляем все запросы вместе со всем содержимым
		foreach ($this->requests as $request) $request->delete();
		
		//Потом все отметки об исправленности
		foreach ($this->fixeds as $fixed) $fixed->delete();
		
		$this->selected_lists=Array();
		$this->update();

		return true;
	}	
	
	public function beforeSave(){
      parent::beforeSave();
   
      if ($this->isNewRecord){
         
			//$this->USER_ID = Yii::app()->user->id;	
			$this->createdate = time();  
      }
      $this->updatedate = time();  
   
      $this->ADR_CITY=trim($this->ADR_CITY);
	
   	return true;
	}		
	
	public function getIsUserHole(){				
      if ($this->USER_ID==Yii::app()->user->id) 
         return true;
      else 
         return false;
	}	
	
	public function getmodering(){
		if ($this->PREMODERATED) {$publtext='снять модерацию'; $pubimg='published.png';}
		else {$publtext='отмодерировать';  $pubimg='unpublished.png';}
		return '<a class="publish ajaxupdate" title="'.$publtext.'" href="'.Yii::app()->getController()->CreateUrl("moderate", Array('id'=>$this->ID)).'">
			<img src="/images/'.$pubimg.'" alt="'.$publtext.'"/>
			</a>';
	}	

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ID' => 'ID',
			'USER_ID' => Yii::t('template', 'USER'),
			'LATITUDE' => Yii::t('template', 'LATITUDE'),
			'LONGITUDE' => Yii::t('template', 'LONGITUDE'),
			'ADDRESS' => Yii::t('template', 'DEFECT_ADDRESS'),
			'gibdd_id'=>Yii::t('template', 'DEPARTMENT'),
			'STATE' => Yii::t('holes', 'WIDGET_STATUS_DEFECT'),
			'createdate' => Yii::t('template', 'CREATEDATE'),
			'updatedate' => Yii::t('template', 'UPDATEDATE'),
			'DATE_CREATED' => Yii::t('template', 'DATE_CREATED'),
			'DATE_SENT' => Yii::t('template', 'DATE_SENT_TO_GIBDD'),
			'DATE_STATUS' => Yii::t('template', 'DATE_STATUS'),
			'COMMENT1' => Yii::t('template', 'COMMENTS'),
			'COMMENT2' => Yii::t('template', 'COMMENTS'),
			'FIRST_NAME' => Yii::t('template', 'FIRST_NAME'),
			'LAST_NAME' => Yii::t('template', 'LAST_NAME'),
			'EMAIL' => Yii::t('template', 'EMAIL'),
			'TYPE_ID' => Yii::t('holes', 'WIDGET_TYPE_DEFECT'),
			'ADR_SUBJECTRF' => Yii::t('holes', 'WIDGET_DEFAULT_REGION'),
			'ADR_CITY' => Yii::t('holes', 'WIDGET_DEFAULT_CITY'),
			'COMMENT_GIBDD_REPLY' => Yii::t('template', 'COMMENT_GIBDD_REPLY'),
			'GIBDD_REPLY_RECEIVED' => Yii::t('template', 'GIBDD_REPLY_RECEIVED'),
			'PREMODERATED' => Yii::t('template', 'PREMODERATED'),
			'NOT_PREMODERATED' =>  Yii::t('template', 'NOT_PREMODERATED'),
			'DATE_SENT_PROSECUTOR' => Yii::t('template', 'DATE_SENT_PROSECUTOR'),		 
         
         'deletepict'=> Yii::t('template', 'DELETEPICT'), 
			'replуfiles'=> Yii::t('template', 'INFO_REPLYFILES'), 
			'upploadedPictures'=>$this->scenario=='fix' ? Yii::t('template', 'INFO_UPLOADPICT_FIX') : Yii::t('template', 'INFO_UPLOADPICT'),
		);
	}
   
  
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function getisEmptyAttribs()	
	{
		$ret=true;
		foreach ($this->attributes as $attr){
			if($attr) $ret=false;
		}
		return $ret;

	}
	
	public function userSearch()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		$userid=Yii::app()->user->id;
		$criteria=new CDbCriteria;
		//$criteria->with=Array('pictures_fresh','pictures_fixed');
		$criteria->with=Array('type','pictures_fresh', 'comments_cnt');
		$criteria->compare('t.ID',$this->ID,false);
		if (!$this->showUserHoles || $this->showUserHoles==1) $criteria->compare('t.USER_ID',$userid,false);
		elseif ($this->showUserHoles==2) {
			$criteria->with=Array('type','pictures_fresh','requests');
			$criteria->addCondition('t.USER_ID!='.$userid);
			$criteria->compare('requests.user_id',$userid,true);
			$criteria->together=true;
		}		
		$criteria->compare('t.STATE',$this->STATE,true);	
		$criteria->compare('t.TYPE_ID',$this->TYPE_ID,false);
		$criteria->compare('type.alias',$this->type_alias,true);	
		//
		//$criteria->addCondition('t.USER_ID='.$userid);
	
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				        'pageSize'=>$this->limit ? $this->limit : 12,				        
				    ),
			'sort'=>array(
			    'defaultOrder'=>'t.DATE_CREATED DESC',
				)
		));
	}	
	
	public function areaSearch($user)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		
		$area=$user->userModel->hole_area;
		
		$userid=$user->id;
		
		$criteria=new CDbCriteria;
                $criteria->with=Array('type','pictures_fresh', 'comments_cnt');	
		$criteria->compare('t.ID',$this->ID,false);

		foreach ($area as $shape){
			$criteria->addCondition('LATITUDE >= '.$shape->points[0]->lat
			.' AND LATITUDE <= '.$shape->points[2]->lat
			.' AND LONGITUDE >= '.$shape->points[0]->lng
			.' AND LONGITUDE <= '.$shape->points[2]->lng, 'OR');
			}

		if ($this->showUserHoles==1) $criteria->compare('t.USER_ID',$userid,false);
		elseif ($this->showUserHoles==2) {
			$criteria->with=Array('type','pictures_fresh','requests');
			$criteria->addCondition('t.USER_ID!='.$userid);
			$criteria->compare('requests.user_id',$userid,true);
			$criteria->together=true;
			}		
		$criteria->compare('t.STATE',$this->STATE,true);	
		$criteria->compare('t.TYPE_ID',$this->TYPE_ID,false);
		$criteria->compare('type.alias',$this->type_alias,true);	
		//
		//$criteria->addCondition('t.USER_ID='.$userid);
	
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				        'pageSize'=>$this->limit ? $this->limit : 12,				        
				    ),
			'sort'=>array(
			    'defaultOrder'=>'t.DATE_CREATED DESC',
				)
		));
	}	
	
	
	
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		//$criteria->with=Array('pictures_fresh','pictures_fixed');
		$criteria->with=Array('type','pictures_fresh', 'comments_cnt');
		$criteria->compare('t.ID',$this->ID,false);
		$criteria->compare('t.USER_ID',$this->USER_ID,false);
		$criteria->compare('t.LATITUDE',$this->LATITUDE);
		$criteria->compare('t.LONGITUDE',$this->LONGITUDE);
		$criteria->compare('t.ADDRESS',$this->ADDRESS,true);
		$criteria->compare('t.STATE',$this->STATE,true);
		$criteria->compare('t.DATE_CREATED',$this->DATE_CREATED,true);
		$criteria->compare('t.DATE_SENT',$this->DATE_SENT,true);
		$criteria->compare('t.DATE_STATUS',$this->DATE_STATUS,true);
		$criteria->compare('t.COMMENT1',$this->COMMENT1,true);
		$criteria->compare('t.COMMENT2',$this->COMMENT2,true);
		$criteria->compare('t.TYPE_ID',$this->TYPE_ID,false);
		$criteria->compare('type.alias',$this->type_alias,true);
		$criteria->compare('t.ADR_SUBJECTRF',$this->ADR_SUBJECTRF,false);
		$criteria->compare('t.ADR_CITY',$this->ADR_CITY,true);
		$criteria->compare('t.COMMENT_GIBDD_REPLY',$this->COMMENT_GIBDD_REPLY,true);
		$criteria->compare('t.GIBDD_REPLY_RECEIVED',$this->GIBDD_REPLY_RECEIVED);
		if ($this->NOT_PREMODERATED) $criteria->compare('PREMODERATED',0);
		if (!Yii::app()->user->isModer) $criteria->compare('PREMODERATED',$this->PREMODERATED,true);
		$criteria->compare('DATE_SENT_PROSECUTOR',$this->DATE_SENT_PROSECUTOR,true);
		//$criteria->together=true;
	
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				        'pageSize'=>$this->limit ? $this->limit : 27,				        
				    ),
			'sort'=>array(
			    'defaultOrder'=>'t.ID DESC',
				)
		));
	}
	
	public function searchInAdmin()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		//$criteria->with=Array('pictures_fresh','pictures_fixed');
		$criteria->with=Array('type','user','subject', 'gibdd');
		$criteria->compare('t.ID',$this->ID,false);
		$criteria->compare('user.username',$this->username,true);
		$criteria->compare('t.LATITUDE',$this->LATITUDE);
		$criteria->compare('t.LONGITUDE',$this->LONGITUDE);
		$criteria->compare('t.ADDRESS',$this->ADDRESS,true);
		$criteria->compare('t.STATE',$this->STATE,true);
		if ($this->DATE_CREATED) {
			$DATE_CREATED=CDateTimeParser::parse($this->DATE_CREATED, 'dd.MM.yyyy');
			$criteria->addCondition('t.DATE_CREATED >='.$DATE_CREATED.' AND t.DATE_CREATED <='.($DATE_CREATED+86400));
			}		
		$criteria->compare('t.DATE_SENT',$this->DATE_SENT,true);
		$criteria->compare('t.DATE_STATUS',$this->DATE_STATUS,true);
		$criteria->compare('t.COMMENT1',$this->COMMENT1,true);
		$criteria->compare('t.COMMENT2',$this->COMMENT2,true);
		$criteria->compare('t.TYPE_ID',$this->TYPE_ID,false);
		$criteria->compare('type.alias',$this->type_alias,true);
		$criteria->compare('subject.name_full',$this->ADR_SUBJECTRF,true);
		$criteria->compare('gibdd.name',$this->gibdd_id,true);
		$criteria->compare('t.ADR_CITY',$this->ADR_CITY,true);
		$criteria->compare('t.COMMENT_GIBDD_REPLY',$this->COMMENT_GIBDD_REPLY,true);
		$criteria->compare('t.GIBDD_REPLY_RECEIVED',$this->GIBDD_REPLY_RECEIVED);
		$criteria->compare('t.PREMODERATED',$this->PREMODERATED,true);
		$criteria->compare('t.DATE_SENT_PROSECUTOR',$this->DATE_SENT_PROSECUTOR,true);
		$criteria->together=true;
	
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				        'pageSize'=> Yii::app()->user->getState('pageSize',20),			        
				    ),
			'sort'=>array(
			    'defaultOrder'=>'t.DATE_CREATED DESC',
				)
		));
	}	
}
