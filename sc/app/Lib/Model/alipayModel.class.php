<?php
class alipayModel extends RelationModel
{
	protected $_auto = array(
		array('token','gettoken',self::MODEL_INSERT,'callback'),
    );
function gettoken(){
		return $_SESSION['token'];
	}
   
}