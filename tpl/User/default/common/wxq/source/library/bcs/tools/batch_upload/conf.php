<?php
/*******************************************************************
 *****************************配置**********************************
 *******************************************************************/
/*
 * 必须配置的部分
 */
$ak = "";
$sk = "";
$bucket = "";
$host = "bcs.duapp.com";
$upload_dir = '';

/*
 * 选配的部分
 */
$prefix = ""; //object前缀，必须以'/'开头
$has_sub_directory = true; //是否保留文件目录结构作为object名的一部分，默认为保留
//$seek_object_id = 1; //如果上传中断，可以在此配object序号，进行断点续传
//$seek_object=""; //如果上传中断，可以在此配object序号，进行断点续传

?>
