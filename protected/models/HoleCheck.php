<?php
class HoleCheck extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	public function relations(){
		return array(
		);
	}
 
	public function tableName()
	{
		return '{{hole_check}}';
	}
}
?>
