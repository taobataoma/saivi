<?php
/**
 *网站后台
 *@package 
 *@author 
 **/
class SystemAction extends BackAction{
	public $server_url;
	public $key;
	public $topdomain;
	public $dirtype;
	public function _initialize() {
		parent::_initialize();
		$this->server_url=trim(C('server_url'));
		if (!$this->server_url){
			$this->server_url='http://up.Saivi.cn/';
		}

		$this->key=trim(C('server_key'));
		$this->topdomain=trim(C('server_topdomain'));
		if (!$this->topdomain){
			$this->topdomain=$this->getTopDomain();
		}
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/Lib')&&is_dir($_SERVER['DOCUMENT_ROOT'].'/Lib')){
			$this->dirtype=2;
		}else {
			$this->dirtype=1;
		}
		$Model = new Model();
		//检查system表是否存在
		$Model->query("CREATE TABLE IF NOT EXISTS `".C('DB_PREFIX')."system_info` (`lastsqlupdate` INT( 10 ) NOT NULL ,`version` VARCHAR( 10 ) NOT NULL) ENGINE = MYISAM CHARACTER SET utf8");
		$Model->query("CREATE TABLE IF NOT EXISTS `".C('DB_PREFIX')."update_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg` varchar(600) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8");
	}
	public function index(){
		$where['display']=1;
		$where['status']=1;
		$order['sort']='asc';
		$nav=M('node')->where($where)->order($order)->select();
		$this->assign('nav',$nav);
		$this->display();
	}

	public function menu(){
		if(empty($_GET['pid'])){
			$where['display']=2;
			$where['status']=1;
			$where['pid']=2;
			$where['level']=2;
			$order['sort']='asc';
			$nav=M('node')->where($where)->order($order)->select();
			$this->assign('nav',$nav);
		}
		$this->display();
	}

	public function main(){
		/*
		require_once('test.php');
		if (!class_exists('test')){
		$canEnUpdate=0;
		}else {
		$canEnUpdate=1;
		}
		*/
		$canEnUpdate=1;
		$this->assign('canEnUpdate',$canEnUpdate);
		//
		//
		$updateRecord=M('System_info')->order('lastsqlupdate DESC')->find();
		if ($updateRecord['lastsqlupdate']>$updateRecord['version']){
			$updateRecord['version']=$updateRecord['lastsqlupdate'];
		}
		$this->assign('updateRecord',$updateRecord);
		$this->display();
	}
	//
	public function _needUpdate(){
		$Model = new Model();
		$updateRecord=M('System_info')->order('lastsqlupdate DESC')->find();
		if (!$updateRecord){
			$Model->query('INSERT INTO `'.C('DB_PREFIX').'system_info` (`lastsqlupdate`, `version`) VALUES(0, \'0\')');
		}
		//
		$key=$this->key;
		$url=$this->server_url.'server.php?key='.$key.'&lastversion='.$updateRecord['version'].'&domain='.$this->topdomain.'&dirtype='.$this->dirtype;
		$remoteStr=@Saivi_getcontents($url);
		//
		$rt=json_decode($remoteStr,1);
		return $rt;
	}
	public function _needSqlUpdate(){
		$updateRecord=M('System_info')->order('lastsqlupdate DESC')->find();
		//
		$key=$this->key;
		$url=$this->server_url.'sqlserver.php?key='.$key.'&lastsqlupdate='.$updateRecord['lastsqlupdate'].'&domain='.$this->topdomain.'&dirtype='.$this->dirtype;
		$remoteStr=Saivi_getcontents($url);
		//
		$rt=json_decode($remoteStr,1);
		return $rt;
	}
	public function checkUpdate(){
		$rt=$this->_needUpdate();
		$needUpdate=0;
		if ($rt['success']<1){
			$sqlrt=$this->_needSqlUpdate();
			if ($sqlrt['success']<1){
			}else {
				$needUpdate=1;
			}
		}else {
			$needUpdate=1;
		}
		$this->assign('needUpdate',$needUpdate);

		$this->display();
	}
	
	protected function deldir($dir){
		$result = true;
		$dh = opendir($dir);
		while($file=readdir($dh)){
			if($file!="." && $file!=".."){
				$fullpath=$dir."/".$file;
				if(!is_dir($fullpath)){
					$result = unlink($fullpath);					
				}else{
					$this->deldir($fullpath);
				}
			}
			rmdir($fullpath);
		}
		closedir($dh);
		return $result;
	}
	
	public function clear(){
		$this->display();
	}
	
	public function del(){
		
		$dir = './Conf/logs';
		$r = $this->deldir($dir);
		if($r){
			$this->success('清除成功',U('index'));
		}else{
			$this->error('清除失败，请检查目录权限',U('index'));
		}
	}
	
	public function doUpdate(){
		$cannotWrite=0;
		if (!class_exists('ZipArchive')){
			$this->error('您的服务器不支持php zip扩展，请配置好此扩展再来升级',U('System/main'));
		}
		if (!isset($_GET['ignore'])){
			if (!is_writable($_SERVER['DOCUMENT_ROOT'].'/saivi')){
				$cannotWrite=1;
				$this->error('您的服务器saivi文件夹不可写入，设置好再升级',U('System/main'));
			}
			if (!is_writable($_SERVER['DOCUMENT_ROOT'].'/saivi/Lib/Action')){
				$cannotWrite=1;
				$this->error('您的服务器/saivi/Lib/Action文件夹不可写入，设置好再升级',U('System/main'));
			}
			if (!is_writable($_SERVER['DOCUMENT_ROOT'].'/tpl')){
				$this->error('您的服务器tpl文件夹不可写入，设置好再升级',U('System/main'));
			}
			if (!is_writable($_SERVER['DOCUMENT_ROOT'].'/tpl/User/default')){
				$this->error('您的服务器/tpl/User/default文件夹不可写入，设置好再升级',U('System/main'));
			}
		}
		/*
		require_once('test.php');
		if (!class_exists('test')){
		$this->success('检查更新',U('System/doSqlUpdate'));
		}
		*/	
		//
		$now=time();
		$updateRecord=M('System_info')->order('lastsqlupdate DESC')->find();
		$key=$this->key;
		$url=$this->server_url.'server.php?key='.$key.'&lastversion='.$updateRecord['version'].'&domain='.$this->topdomain.'&dirtype='.$this->dirtype;
		$remoteStr=@Saivi_getcontents($url);
		//
		$rt=json_decode($remoteStr,1);
		if (intval($rt['success'])<1){
			if (intval($rt['success'])==0){
				if (!isset($_GET['ignore'])){
					$this->success('继续检查更新了,不要关闭,跳是正常的'.$rt['msg'],U('System/doSqlUpdate'));
		        }else {
					$this->success('继续检查更新了,不要关闭,跳是正常的'.$rt['msg'],U('System/doSqlUpdate',array('ignore'=>1)));
				}
			}else {
				$this->success($rt['msg'],U('System/main'));
			}
		}else {
			$locationZipPath=RUNTIME_PATH.$now.'.zip';
			$filename=$this->server_url.'server.php?getFile=1&key='.$key.'&lastversion='.$updateRecord['version'].'&domain='.$this->topdomain.'&dirtype='.$this->dirtype;
			@file_put_contents($locationZipPath,@Saivi_getcontents($filename));
			//
			$zip = new ZipArchive();

			$rs = $zip->open($locationZipPath);
			if($rs !== TRUE)
			{
				$this->error('解压失败_2!Error Code:'. $rs);
			}
			//
			$cacheUpdateDirName='caches_upgrade'.date('Ymd',time());
			if(!file_exists(RUNTIME_PATH.$cacheUpdateDirName)) {
				@mkdir(RUNTIME_PATH.$cacheUpdateDirName,0777);
			}
			//
			$zip->extractTo(RUNTIME_PATH.$cacheUpdateDirName);
			recurse_copy(RUNTIME_PATH.$cacheUpdateDirName,$_SERVER['DOCUMENT_ROOT']);
			$zip->close();
			//delete
			if (!$cannotWrite){
				@deletedir(RUNTIME_PATH.$cacheUpdateDirName);
			}
			@unlink($locationZipPath);
			//record to database
			if ($rt['time']){
				M('System_info')->where(array('version'=>$updateRecord['version']))->save(array('version'=>$rt['time']));
				M('Update_record')->add(array('msg'=>$rt['msg'],'time'=>$rt['time'],'type'=>$rt['type']));
			}
			if (isset($_GET['ignore'])){
				$this->success('进入下一步(不要关闭,等待完成,跳是正常的):'.$rt['msg'],U('System/doUpdate',array('ignore'=>1)));
			}else {
				$this->success('进入下一步(不要关闭,等待完成,跳是正常的):'.$rt['msg'],U('System/doUpdate'));
			}
		}
	}
	public function doSqlUpdate(){
		//
		$now=time();
		$updateRecord=M('System_info')->order('lastsqlupdate DESC')->find();
		$key=$this->key;
		$url=$this->server_url.'sqlserver.php?key='.$key.'&excute=1&lastsqlupdate='.$updateRecord['lastsqlupdate'].'&domain='.$this->topdomain.'&dirtype='.$this->dirtype;
		$remoteStr=Saivi_getcontents($url);
		//
		$rt=json_decode($remoteStr,1);
		if (intval($rt['success'])<1){
			if (intval($rt['success'])==0){
				$this->success('升级完成',U('System/main'));
			}else {
				$this->error($rt['msg'],U('System/main'));
			}
		}else {
			$Model = new Model();
			error_reporting(0);
			@mysql_query(str_replace('{tableprefix}',C('DB_PREFIX'),$rt['sql']));
			//record to database
			if ($rt['time']){
				M('System_info')->where(array('lastsqlupdate'=>$updateRecord['lastsqlupdate']))->save(array('lastsqlupdate'=>$rt['time']));
			}
			if (!isset($_GET['ignore'])){
				$this->success('进入下一步(不要关闭,耐心等待完成,跳是正常的):'.$rt['msg'],U('System/doSqlUpdate'));
			}else {
				$this->success('进入下一步(不要关闭,耐心等待完成,跳是正常的):'.$rt['msg'],U('System/doSqlUpdate',array('ignore'=>1)));
			}
		}
	}
	function rollback(){
		//20140312
		$time=substr($_GET['time'],0,8);
		$year=substr($time,0,4);
		$month=substr($time,4,2);
		$day=substr($time,6,2);
		//exit($day);
		$timeStamp=mktime(0,0,0,$month,$day,$year);
		$updateRecord=M('System_info')->order('lastsqlupdate DESC')->find();
		M('System_info')->where(array('lastsqlupdate'=>$updateRecord['lastsqlupdate']))->save(array('lastsqlupdate'=>$timeStamp,'version'=>$timeStamp));
		$this->success('您可以重新进行升级了',U('System/main'));
	}
	function curlGet($url){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
	}
	function getTopDomain(){
		$host=$_SERVER['HTTP_HOST'];
		$host=strtolower($host);
		if(strpos($host,'/')!==false){
			$parse = @parse_url($host);
			$host = $parse['host'];
		}
		$topleveldomaindb=array('com','edu','gov','int','mil','net','org','biz','info','pro','name','museum','coop','aero','xxx','idv','mobi','cc','me');
		$str='';
		foreach($topleveldomaindb as $v){
			$str.=($str ? '|' : '').$v;
		}
		$matchstr="[^\.]+\.(?:(".$str.")|\w{2}|((".$str.")\.\w{2}))$";
		if(preg_match("/".$matchstr."/ies",$host,$matchs)){
			$domain=$matchs['0'];
		}else{
			$domain=$host;
		}
		return $domain;
	}

	public function updateMysql(){
		$this->display();
	}


	public function doUpdateMysql(){
/*		//备份数据库
		// 设置SQL文件保存文件名
		$filename=date("Y-m-d_H-i-s")."-".C('DB_NAME').".sql";
		// 所保存的文件名
		header("Content-disposition:filename=".$filename);
		// header("Content-type:application/octetstream");
		header("Pragma:no-cache");
		header("Expires:0");
		// 获取当前页面文件路径，SQL文件就导出到此文件夹内
		$tmpFile = dirname(realpath('index.php')).'\backup/'.$filename;
		
		// 用MySQLDump命令导出数据库
		
		if(function_exists('exec')){

		exec("mysqldump -u".C('DB_USER')." -p".C('DB_PWD')." ". C('DB_NAME') ." > ".$tmpFile);


	}else{
		echo 'exec没有开启';
	}
*/

			//同步数据库结构
		if($this->compare()){
			$this->ajaxReturn(1,'json');
		}else{
			$this->ajaxReturn(2,'json');
		}
	}

	public function test(){
		$this->init();
		$str = file_get_contents('http://demo.saivi.com.cn/update/tp_function.sql');
		$arr = explode(';', $str);
		
		$this->execute('set names utf8', TARGET_LINK);
		
		for($i=0;$i<count($arr)-1;$i++){
			
			$sql = $arr[$i].';';
			
			$this->execute($sql, TARGET_LINK);
		}

}

//初始化数据库连接
	public function init(){
	set_time_limit(0);
	error_reporting(0);
	//数据源
	define('SOURCE_HOST', '211.160.119.107');
	define('SOURCE_USER', 'saiviupdate');
	define('SOURCE_PASS', 'demosaivi');
	define('SOURCE_DB', 'demonew819');

	//目标数据库
	define('TARGET_HOST', C('DB_HOST'));
	define('TARGET_USER', C('DB_USER'));
	define('TARGET_PASS', C('DB_PWD'));
	define('TARGET_DB', C('DB_NAME'));


	//初始化数据源
	define('SOURCE_LINK', mysql_connect(SOURCE_HOST, SOURCE_USER, SOURCE_PASS));
	mysql_select_db(SOURCE_DB, SOURCE_LINK);
	
	//初始目标数据库
	define('TARGET_LINK', mysql_connect(TARGET_HOST, TARGET_USER, TARGET_PASS));
	mysql_select_db(TARGET_DB, TARGET_LINK);
}

//执行完毕，回收连接
function close(){
	mysql_close(SOURCE_LINK);
	mysql_close(TARGET_LINK);
}

//执行sql语句
function execute($sql, $link){
	//echo $sql."\r\n";
	mysql_query($sql, $link);
}

function compare(){
	$this->init();
	
	//如果目标数据库不存在，先创建一个
	$sql = 'create database if not exists '.TARGET_DB.' default charset utf8 collate utf8_general_ci';
	mysql_query($sql, TARGET_LINK);
	


	//获取数据源的数据结构
	$source_database_struct = $this->get_database_struct(SOURCE_LINK, SOURCE_DB);

	
	//获取目标的数据结构
	$target_database_struct = $this->get_database_struct(TARGET_LINK, TARGET_DB);
	
	//以数据源为准，比较差异
	foreach($source_database_struct as $table_name => $create_table){
		if(!$target_database_struct[$table_name]){
			$this->execute($create_table, TARGET_LINK);
		} else {
			//比较字段
			$this->compare_column(SOURCE_LINK, TARGET_LINK, SOURCE_DB, TARGET_DB, $table_name);
			
			//比较索引
			$this->compare_keys(SOURCE_LINK, TARGET_LINK, SOURCE_DB, TARGET_DB, $table_name);
			
			//比较分区
			$this->compare_partition(SOURCE_LINK, TARGET_LINK, SOURCE_DB, TARGET_DB, $table_name);
		}
	}
	
	//删除多余的表
	foreach($target_database_struct as $table_name => $create_table){
		if(!$source_database_struct[$table_name]){
			$sql = 'drop table '.TARGET_DB.'.'.$table_name;
			$this->execute($sql, TARGET_LINK);
		}
	}

		$str = file_get_contents('http://demo.saivi.com.cn/update/tp_function.sql');
		$arr = explode(';', $str);
		$this->execute('set names utf8', TARGET_LINK);
		for($i=0;$i<count($arr);$i++){
			$sql = $arr[$i].';';
			$this->execute($sql, TARGET_LINK);
		}


	
	$this->close();
	return true;
}


//比较字段
function compare_column($source_link, $target_link, $source_db, $target_db, $table_name){
	$sql = $after = '';
	$source_column = $this->get_table_column($source_link, $source_db, $table_name);
	$target_column = $this->get_table_column($target_link, $target_db, $table_name);
	foreach($source_column as $column_name => $column_info){
		$column_name = trim($column_name);
		if(!$target_column[$column_name]){
			$sql = 'alter table '.$target_db.'.'.$table_name.' add '.$column_name.' ';
			$sql.= $column_info['COLUMN_TYPE'].' '.($column_info['IS_NULLABLE'] == 'NO' ? 'NOT NULL ' : 'NULL ');
			$sql.= ' comment \''.$column_info['COLUMN_COMMENT'].'\'';
			if ($after) {
				$sql.= 'after '.$after;
			}
			$this->execute($sql, $target_link);
		} else {
			//如果字段的属性不对
			$need_modify = false;
			$sql = 'alter table '.$target_db.'.'.$table_name.' change '.$column_name.' ';
			foreach($column_info as $key => $info){
				$key = trim($key);
				$source = $info = trim($info);
				$target = trim($target_column[$column_name][$key]);
				switch ($key) {
					case 'COLUMN_NAME':
					$sql.= $info.' ';
					break;
					case 'IS_NULLABLE':
					if($info == 'YES'){
						$sql.= 'NULL ';
					} else {
						$sql.= 'NOT NULL ';
					}
					break;
					case 'COLUMN_DEFAULT':
					if($info == 'null' || $info == ''){
						$sql.= 'NULL ';
					} else {
						$sql.= 'DEFAULT \''.$info.'\' ';
					}
					break;
					case 'COLUMN_TYPE':
					$sql.= $info.' ';
					break;
					case 'COLUMN_COMMENT':
					$sql.= 'comment \''.$info.'\' ';
					break;
				}
				if(!$need_modify){
					$need_modify = $source != $target;
				}
			}
			if ($need_modify) {
				$this->execute($sql, $target_link);
			}
		}
		$after = $column_name;
	}
	//如果多余
	foreach($target_column as $column_name => $column_info){
		if(!$source_column[$column_name]){
			$sql = 'alter table '.$target_db.'.'.$table_name.' drop '.$column_name;	
			$this->execute($sql, $target_link);
		}
	}
}

//比较索引
function compare_keys($source_link, $target_link, $source_db, $target_db, $table_name){
	$sql = '';
	$source_key = $this->get_table_keys($source_link, $source_db, $table_name);
	$target_key = $this->get_table_keys($target_link, $target_db, $table_name);
	foreach($source_key as $key_name => $key_info){
		$key_name = trim($key_name);
		if(!$target_key[$key_name]){
			$sql = 'alter table '.$target_db.'.'.$table_name.' ';
			if($key_name == 'PRIMARY'){
				$sql.= ' add primary key';
			} else {
				$is_unique = false;
				foreach($key_info as $k => $v){
					foreach($v as $kk => $vv){
						$is_unique = intval($vv) <= 0;
					}
				}
				if($is_unique){
					$sql.= ' add unique ';
				} else {
					$sql.= ' add index ';
				}
				$sql.= $key_name;
			}
			$sql.= ' (`';
			foreach($key_info as $key => $value){
				$sql.= trim($key);
				$sql.= '`,`';
			}
			$sql = substr($sql, 0, -2);
			$sql.= ' )';
			$this->execute($sql, $target_link);
		}
	}
	//如果多余
	foreach($target_key as $key_name => $key_info){
		$sql = 'alter table '.$target_db.'.'.$table_name;
		if(!$source_key[$key_name]){
			if($key_name == 'PRIMARY') {
				$sql.= ' drop primary key ';
			} else {
				$sql.= ' drop index '.$key_name;
			}
			$this->execute($sql, $target_link);
		}
	}
}

//比较分区
function compare_partition($source_link, $target_link, $source_db, $target_db, $table_name){
	$sql = '';
	$source_partitions = $this->get_table_partitions($source_link, $source_db, $table_name);
	$target_partitions = $this->get_table_partitions($target_link, $target_db, $table_name);
	$extra = false;
	foreach($source_partitions as $method => $partitions){
		if($target_partitions[$method]){
			continue;
		}
		$sql = 'alter table '.$target_db.'.'.$table_name.' partition by '.$method;
		switch($method){
			case 'KEY':
				$sql.= '('.trim($partitions['PARTITION_EXPRESSION']).') partitions '.trim($partitions['PARTITION_NUM']);
			break;
			case 'HASH':
				$sql.= '('.trim($partitions['PARTITION_EXPRESSION']).') partitions '.trim($partitions['PARTITION_NUM']);
			break;
			case 'LIST':
				foreach($partitions as $p){
					if($extra === false){
						$sql.= '('.trim($partitions[0]['PARTITION_EXPRESSION']).') (';
						$extra = true;
					}
					$sql.= 'partition '.trim($p['PARTITION_NAME']).' values in ('.trim($p['PARTITION_DESCRIPTION']).'),';
				}
				$sql = substr($sql, 0, -1);
				$sql.= ')';
			break;
			case 'RANGE':
				foreach($partitions as $p){
					if($extra === false){
						$sql.= '('.trim($partitions[0]['PARTITION_EXPRESSION']).') (';
						$extra = true;
					}
					$sql.= 'partition '.trim($p['PARTITION_NAME']).' values less than ('.trim($p['PARTITION_DESCRIPTION']).'),';
				}
				$sql = substr($sql, 0, -1);
				$sql.= ')';
			break;
		}
	}
	if(intval(strpos($sql, 'HASH')) > 0 || intval(strpos($sql, 'KEY')) > 0 || intval(strpos($sql, 'LIST')) > 0 || intval(strpos($sql, 'RANGE')) > 0){
		$this->$this->execute($sql, $target_link);
	}
	
	//如果多余
	if(count($source_partitions) > 0 || count($target_partitions) > 0){
		foreach($target_partitions as $method => $partitions){
			if(!$source_partitions[$method]){
				$sql = 'alter table '.$target_db.'.'.$table_name.' remove partitioning';
				$this->execute($sql, $target_link);
			}
		}
	}
}

//获取数据库结构
function get_database_struct($link, $db){
	$struct_map = array();
	foreach($this->get_database_table($link, $db) as $table){
		$sql = 'show create table '.$db.'.'.$table;
		$rs = mysql_query($sql, $link);

		while ($row = mysql_fetch_assoc($rs)) {
			$struct_map[$table] = trim($row['Create Table']);
		}
	}
	return $struct_map;
}

//获取数据库所有表
function get_database_table($link, $db){
	$table_list = array();
	$sql = 'show tables from '.$db;

	$rs = mysql_query($sql, $link);
	
	while ($row = mysql_fetch_assoc($rs)) {
		$table_list[] = trim($row['Tables_in_'.$db]);
	}
	return $table_list;
}

//获取表的所有字段信息
function get_table_column($link, $db, $table){
	$sql = 'select COLUMN_NAME,COLUMN_TYPE,IS_NULLABLE,COLUMN_DEFAULT,COLUMN_COMMENT from information_schema.columns ';
	$sql.= 'where TABLE_SCHEMA=\''.$db.'\' and TABLE_NAME=\''.$table.'\' order by ORDINAL_POSITION asc';
	$rs = mysql_query($sql, $link);
	$table_column = array();
	while ($row = mysql_fetch_assoc($rs)) {
		$tmp = array();
		$tmp['COLUMN_NAME'] = trim($row['COLUMN_NAME']);
		$tmp['COLUMN_TYPE'] = trim($row['COLUMN_TYPE']);
		$tmp['COLUMN_DEFAULT'] = trim($row['COLUMN_DEFAULT']);
		$tmp['IS_NULLABLE'] = trim($row['IS_NULLABLE']);
		$tmp['COLUMN_COMMENT'] = trim($row['COLUMN_COMMENT']);
		$table_column[$row['COLUMN_NAME']] = $tmp;
	}
	return $table_column;
}

//获取表的索引信息
function get_table_keys($link, $db, $table){
	$sql = 'show keys from '.$db.'.'.$table;
	$rs = mysql_query($sql, $link);
	$last = '';
	$tmp = $table_keys = array();
	while ($row = mysql_fetch_assoc($rs)) {
		$key_name = trim($row['Key_name']);
		if($key_name != $last){
			$tmp = array();
		}
		$last = $key_name;
		$t = array();
		$t['Non_unique'] = $row['Non_unique'];
		$tmp[$row['Column_name']] = $t;
		$table_keys[$key_name] = $tmp;
	}
	return $table_keys;
}

//获取表的分区信息
function get_table_partitions($link, $db, $table){
	$sql = 'select PARTITION_NAME,PARTITION_METHOD,PARTITION_EXPRESSION,PARTITION_DESCRIPTION FROM INFORMATION_SCHEMA.PARTITIONS';
	$sql.= ' where TABLE_SCHEMA=\''.$db.'\' and TABLE_NAME=\''.$table.'\'';
	$rs = mysql_query($sql, $link);
	$partitions = array();
	$i = 1;
	while ($row = mysql_fetch_assoc($rs)) {
		if(!trim($row['PARTITION_NAME']) && !trim($row['PARTITION_METHOD']) && !trim($row['PARTITION_EXPRESSION']) && !trim($row['PARTITION_DESCRIPTION'])){
			continue;
		}
		if(!is_array($partitions[$row['PARTITION_METHOD']])){
			$partitions[$row['PARTITION_METHOD']] = array();
		}
		switch($row['PARTITION_METHOD']){
			case 'KEY':
				$this->get_key_or_hash_partition($row, $partitions, $i);
			break;
			case 'HASH':
				$this->get_key_or_hash_partition($row, $partitions, $i);
			break;
			case 'LIST':
				$this->get_list_or_range_partition($row, $partitions, $i);
			break;
			case 'RANGE':
				$this->get_list_or_range_partition($row, $partitions, $i);
			break;
		}
		$i += 1;
	}
	return $partitions;
}

function get_key_or_hash_partition($row, &$partitions, $i){
	$partitions[$row['PARTITION_METHOD']]['PARTITION_EXPRESSION'] = trim(str_replace('`', '', $row['PARTITION_EXPRESSION']));
	$partitions[$row['PARTITION_METHOD']]['PARTITION_NUM'] = $i;
}

function get_list_or_range_partition($row, &$partitions, $i){
	$partitions[$row['PARTITION_METHOD']][$i - 1]['PARTITION_EXPRESSION'] = trim(str_replace('`', '', $row['PARTITION_EXPRESSION']));
	$partitions[$row['PARTITION_METHOD']][$i - 1]['PARTITION_NAME'] = trim($row['PARTITION_NAME']);
	$partitions[$row['PARTITION_METHOD']][$i - 1]['PARTITION_DESCRIPTION'] = trim($row['PARTITION_DESCRIPTION']);
}

}
function recurse_copy($src,$dst) {  // 原目录，复制到的目录
	$dir = opendir($src);
	@mkdir($dst);
	while(false !== ( $file = readdir($dir)) ) {
		if (( $file != '.' ) && ( $file != '..' )) {
			if ( is_dir($src . '/' . $file) ) {
				recurse_copy($src . '/' . $file,$dst . '/' . $file);
			}
			else {
				copy($src . '/' . $file,$dst . '/' . $file);
			}
		}
	}
	closedir($dir);
}
function deletedir($dirname){
	$result = false;
	if(! is_dir($dirname)){
		echo " $dirname is not a dir!";
		exit(0);
	}
	$handle = opendir($dirname); //打开目录
	while(($file = readdir($handle)) !== false) {
		if($file != '.' && $file != '..'){ //排除"."和"."
			$dir = $dirname.DIRECTORY_SEPARATOR.$file;
			//$dir是目录时递归调用deletedir,是文件则直接删除
			is_dir($dir) ? deletedir($dir) : unlink($dir);
		}
	}
	closedir($handle);
	$result = rmdir($dirname) ? true : false;
	return $result;
}
function Saivi_getcontents($url){
	if (function_exists('curl_init')){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		$errorno=curl_errno($ch);
		if ($errorno) {
			exit('curl发生错误：错误代码'.$errorno.'，如果错误代码是6，您的服务器可能无法连接我们升级服务器');
		}else {
			return $temp;
		}
	}else {
		$str=file_get_contents($url);
		return $str;
	}
}


?>