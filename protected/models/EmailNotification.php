<?php

/**
* This is the model class for table "email_notification".
*
* The followings are the available columns in table 'email_notification':
    * @property integer $id
    * @property string $email_code
    * @property string $about
    * @property string $subject
    * @property string $body
*/
class EmailNotification extends Model
{
/**
* Returns the static model of the specified AR class.
* @param string $className active record class name.
* @return EmailNotification the static model class
*/
public static function model($className=__CLASS__)
{
return parent::model($className);
}

/**
* @return string the associated database table name
*/
public function tableName()
{
return 'email_notification';
}

/**
* @return array validation rules for model attributes.
*/
public function rules()
{
// NOTE: you should only define rules for those attributes that
// will receive user inputs.
return array(
    array('email_code, about, subject, body', 'required'),
    array('email_code', 'length', 'max'=>100),
// The following rule is used by search().
// Please remove those attributes that should not be searched.
array('id, email_code, about, subject, body', 'safe', 'on'=>'search'),
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
);
}

/**
* @return array customized attribute labels (name=>label)
*/
public function attributeLabels()
{
return array(
    'id' => 'ID',
    'email_code' => 'Email Code',
    'about' => 'About',
    'subject' => 'Subject',
    'body' => 'Body',
);
}

/**
* Retrieves a list of models based on the current search/filter conditions.
* @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
*/
public function search()
{
// Warning: Please modify the following code to remove attributes that
// should not be searched.

$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('email_code',$this->email_code,true);
		$criteria->compare('about',$this->about,true);
		$criteria->compare('subject',$this->subject,true);
		$criteria->compare('body',$this->body,true);

return new CActiveDataProvider($this, array(
'criteria'=>$criteria,
));
}
}