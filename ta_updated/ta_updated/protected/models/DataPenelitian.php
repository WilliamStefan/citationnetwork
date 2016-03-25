<?php

/**
 * This is the model class for table "data_penelitian".
 *
 * The followings are the available columns in table 'data_penelitian':
 * @property integer $id
 * @property string $judul
 * @property string $peneliti
 * @property integer $tahun_publikasi
 * @property string $masalah
 * @property string $deskripsi_masalah
 * @property string $keyword
 * @property string $domain_data
 * @property string $deskripsi_domain_data
 * @property string $metode
 * @property string $deskripsi_metode
 * @property string $hasil
 * @property string $creater
 */
class DataPenelitian extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $creater_search;
	public function tableName()
	{
		return 'data_penelitian';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('judul, peneliti, tahun_publikasi, masalah, deskripsi_masalah, keyword, domain_data, deskripsi_domain_data, metode, deskripsi_metode, hasil, creater', 'required'),
			array('tahun_publikasi', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, judul, peneliti, tahun_publikasi, masalah, deskripsi_masalah, keyword, domain_data, deskripsi_domain_data, metode, deskripsi_metode, hasil, creater, creater_search', 'safe', 'on'=>'search'),
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
			'datapenelitian'=>array(self::HAS_MANY, 'Relasi', array('id' => 'id_paper_1')),
			'datapenelitian2'=>array(self::HAS_MANY, 'Relasi', array('id' => 'id_paper_2')),
			'creater'=>array(self::BELONGS_TO, 'TblUsers', array('creater' => 'id')),
			
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'judul' => 'Judul',
			'peneliti' => 'Peneliti',
			'tahun_publikasi' => 'Tahun Publikasi',
			'masalah' => 'Masalah',
			'deskripsi_masalah' => 'Deskripsi Masalah',
			'keyword' => 'Keyword',
			'domain_data' => 'Domain Data',
			'deskripsi_domain_data' => 'Deskripsi Domain Data',
			'metode' => 'Metode',
			'deskripsi_metode' => 'Deskripsi Metode',
			'hasil' => 'Hasil',
			'creater' => 'Creater',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;
		$criteria->with=array('creater');
		$criteria->compare('id',$this->id);
		$criteria->compare('judul',$this->judul,true);
		$criteria->compare('peneliti',$this->peneliti,true);
		$criteria->compare('tahun_publikasi',$this->tahun_publikasi);
		$criteria->compare('masalah',$this->masalah,true);
		$criteria->compare('deskripsi_masalah',$this->deskripsi_masalah,true);
		$criteria->compare('keyword',$this->keyword,true);
		$criteria->compare('domain_data',$this->domain_data,true);
		$criteria->compare('deskripsi_domain_data',$this->deskripsi_domain_data,true);
		$criteria->compare('metode',$this->metode,true);
		$criteria->compare('deskripsi_metode',$this->deskripsi_metode,true);
		$criteria->compare('hasil',$this->hasil,true);
		$criteria->compare('creater.username',$this->creater_search,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DataPenelitian the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
