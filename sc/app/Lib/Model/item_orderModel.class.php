<?php
class item_cateModel extends Model
{
	protected $_auto = array(
		array('token','gettoken',self::MODEL_INSERT,'callback'),
    );
function gettoken(){
if (isset($_SESSION['token']))
		return $_SESSION['token'];
else
		return $_GET['token'];


	}
   
}