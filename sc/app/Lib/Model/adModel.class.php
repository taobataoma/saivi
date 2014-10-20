<?php
class adModel extends RelationModel {
	
protected $_auto = array(
		array('token','gettoken',self::MODEL_INSERT,'callback'),
    );
function gettoken(){
		return $_SESSION['token'];
	}
    //关联关系
    protected $_link = array(
        'adbord' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'adboard',
            'foreign_key' => 'board_id',
        ),
    );
}