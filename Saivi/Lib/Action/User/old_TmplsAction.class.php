<?php

/**
 * 通用模板管理
 * */
class TmplsAction extends UserAction {

    public function index() {
        $db = D('Wxuser');
        $where['token'] = session('token');
        $where['uid'] = session('uid');
        $info = $db->where($where)->find();
        $this->assign('info', $info);
        //模板提示信息
        $desinfo[1]= "";
        $desinfo[2]="列表式图片模版，缩略图建议使用150*150或近似尺寸比例的图片。";
        $desinfo[3]="列表式图片模版，缩略图建议使用150*150或近似尺寸比例的图片。";
        $desinfo[4]="列表式图片模版，缩略图建议使用150*150或近似尺寸比例的图片。";
        $desinfo[5]="文字标签式模版，顶部幻灯片尺寸为640*320或近似等比例图片。";
        $desinfo[6]="";
        $desinfo[7]="";
        $desinfo[8]="";
        $desinfo[9]="";
        $desinfo[10]="";
        $desinfo[11]="左右图文式模版，顶部幻灯片建议使用尺寸为640*320或近似等比例图片；分类图片建议使用450*300或近似等比例图片，请不要使用高度大于或接近于宽度的图片。";
        $desinfo[12]="支持动态背景图片";
        $desinfo[13]="支持动态背景图片";
        $desinfo[14]="支持动态背景图片";
        $desinfo[15]="支持动态背景图片";
        $desinfo[16]="支持动态背景图片";
        $desinfo[17]="支持动态背景图片";
        $desinfo[18]="图标式模板";
        $desinfo[19]="图标式模板";
        $desinfo[20]="";
        $desinfo[21]="";
        $desinfo[22]="";
        $desinfo[23]="";
        $desinfo[24]="";
        $desinfo[25]="";
        $desinfo[26]="";
        $desinfo[27]="";
        $desinfo[28]="";
        $desinfo[29]="";
        $desinfo[30]="";
        $desinfo[31]="";
        $desinfo[32]="";
        $desinfo[33]="";
        $desinfo[34]="";
        $desinfo[35]="";
		$desinfo[36]="";
        $desinfo[37]="";
        $desinfo[38]="";
        $desinfo[39]="";
        $desinfo[40]="";
        $desinfo[41]="";
        $desinfo[42]="";
        $desinfo[43]="";
        $desinfo[44]="";
        $desinfo[45]="";
        $desinfo[46]="";
        $desinfo[47]="";
        $desinfo[48]="";
        $desinfo[49]="";
        $desinfo[50]="";
        $desinfo[51]="";
        $desinfo[52]="";
        $desinfo[53]="";
        $desinfo[54]="";
        $desinfo[55]="";
        $desinfo[56]="";
        $desinfo[57]="3G设置中可更换背景图片";
        $desinfo[58]="3G设置中可更换背景图片";
        $desinfo[59]="";
        $desinfo[60]="";
        $desinfo[61]="";
        $desinfo[62]="";
        $desinfo[63]="";
        $desinfo[64]="";
        $desinfo[65]="";
        $desinfo[66]="";
        $desinfo[67]="";
        $desinfo[68]="";
        $desinfo[69]="3G设置中可更换背景图片";
        $desinfo[70]="";
        $desinfo[71]="";
        $desinfo[72]="";
        $desinfo[73]="图片式模板，顶部幻灯片建议尺寸为宽640*高320或近似等比例的图片，文字为分类名称及分类描述，名称建议4个字符，描述限制10个字符以内；图片为分类封面，建议尺寸为宽165*高100或近似等比例图片";
        $desinfo[74]="3G设置中可更换背景图片";
        $desinfo[75]="图标式模板，顶部幻灯片建议尺寸为宽640*高320或近似等比例的图片。";
        $desinfo[76]="支持二级分类，顶部幻灯片建议尺寸为宽640*高320或近似等比例的图片，幻灯下4个图标为分类管理的前4个分类，图标下第一块内容为第五个分类的分类封面、分类名称及子分类名称，建议尺寸300*300或1:1图片，下面依次类推。";
        $desinfo[77]="图标式模板，顶部幻灯片建议尺寸为宽640*高320或近似等比例的图片，分类前8个为图标及文字展示，后面分类为图片展示，建议尺寸为宽150*高90或等比例图片。";
        $desinfo[78]="图标式模板，按分类顺序依次展现，有图片显示的分类，图片尺寸建议为宽150*85高或的等比例图片。";
        $desinfo[79]="汽车行业专属模板，顶部幻灯片建议尺寸为宽640*高320或近似等比例的图片，幻灯下4个图标为分类管理的前4个分类，后面分类依次展示，图片建议尺寸为宽310*高130或等比例图片，logo图标为官网logo，需等比尺寸的png格式图片。";
        $desinfo[80]="此模板适合做简单版纯展示的会员卡，头部图片就是首页封面图，宽720高随便，如果用幻灯片记住一定要相同的尺寸。小图标尺寸是正方形300x300,一个分类一页显示8个二级分类。";
        $desinfo[81]="左右双栏模版，顶部幻灯片尺寸为640*320或近似等比例图片，如使用正方形图片会使得页面不美观；分类图片建议使用300*200或近似等比例图片，使用宽度小于高度的(如200*300)尺寸图片将使页面惨不忍睹。";
        $desinfo[82]="";
        $desinfo[83]="图标式模版，顶部幻灯片建议使用尺寸为640*320或近似等比例图片；分类图片请使用正方形尺寸的图片。";
        $desinfo[84]="此模板适合做简单版纯展示的会员卡，头部图片就是首页封面图，宽720高随便，如果用幻灯片记住一定要相同的尺寸。小图标尺寸是正方形300x300,一个分类一页显示8个二级分类。";
        $desinfo[85]="此模板适合做简单版纯展示的会员卡，头部图片就是首页封面图，宽720高随便，如果用幻灯片记住一定要相同的尺寸。小图标尺寸是正方形300x300,一个分类一页显示6个二级分类。";
        $desinfo[86]="此模板支持二级分类，适合分类比较多的地方公众号，小图标为正方形300x300px。";
        $desinfo[87]="此模板支持二级分类，适合分类比较多的地方公众号，小图标为正方形300x300px。";
        $desinfo[88]="此模板支持二级分类，适合分类比较多的地方公众号，前4个一级分类可以突出显示，小图标";
        $desinfo[89]="支持动态背景图片";
        $this->assign('desinfo',$desinfo);
        $this->display();
    }

    public function add() {
        $gets = $this->_get('style');
        $db = M('Wxuser');
        switch ($gets) {
            case 1:
                $data['tpltypeid'] = 1;
                $data['tpltypename'] = '101_index';
                break;
            case 2:
                $data['tpltypeid'] = 2;
                $data['tpltypename'] = '102_index';
                break;
            case 3:
                $data['tpltypeid'] = 3;
                $data['tpltypename'] = '103_index';
                break;
            case 4:
                $data['tpltypeid'] = 4;
                $data['tpltypename'] = '104_index';
                break;
            case 5:
                $data['tpltypeid'] = 5;
                $data['tpltypename'] = '105_index';
                break;
            case 6:
                $data['tpltypeid'] = 6;
                $data['tpltypename'] = '106_index_ydkds';
                break;
            case 7:
                $data['tpltypeid'] = 7;
                $data['tpltypename'] = '107_index_2d8si';
                break;
            case 8:
                $data['tpltypeid'] = 8;
                $data['tpltypename'] = '108_index_giw93x';
                break;
            case 9:
                $data['tpltypeid'] = 9;
                $data['tpltypename'] = '109_index_0fdis';
                break;
            case 10:
                $data['tpltypeid'] = 10;
                $data['tpltypename'] = '110_index_2skz7';
                break;
            case 11:
                $data['tpltypeid'] = 11;
                $data['tpltypename'] = '111_index_78yus';
                break;
            case 12:
                $data['tpltypeid'] = 12;
                $data['tpltypename'] = '112_index_kj7y5';
                break;
            case 13:
                $data['tpltypeid'] = 13;
                $data['tpltypename'] = '113_index_jks6z';
                break;
            case 14:
                $data['tpltypeid'] = 14;
                $data['tpltypename'] = '114_index_mnsz6';
                break;
			case 15:
                $data['tpltypeid'] = 15;
                $data['tpltypename'] = '115_index_ms76x';
                break;
			case 16:
				$data['tpltypeid']=16;
				$data['tpltypename']='tpl_116_index';
				break;
			case 17:
				$data['tpltypeid']=17;
				$data['tpltypename']='tpl_117_index';
				break;
			case 18:
				$data['tpltypeid']=18;
				$data['tpltypename']='tpl_118_index';
				break;
			case 19:
				$data['tpltypeid']=19;
				$data['tpltypename']='tpl_119_index';
				break;
			case 20:
				$data['tpltypeid']=20;
				$data['tpltypename']='tpl_120_index';
				break;
			case 21:
				$data['tpltypeid']=21;
				$data['tpltypename']='tpl_121_index';
				break;
			case 22:
				$data['tpltypeid']=22;
				$data['tpltypename']='tpl_122_index';
				break;
			case 23:
				$data['tpltypeid']=23;
				$data['tpltypename']='tpl_123_index';
				break;
			case 24:
				$data['tpltypeid']=24;
				$data['tpltypename']='tpl_124_index';
				break;
			case 25:
				$data['tpltypeid']=25;
				$data['tpltypename']='tpl_125_index';
				break;
			case 26:
				$data['tpltypeid']=26;
				$data['tpltypename']='tpl_126_index';
				break;
			case 27:
				$data['tpltypeid']=27;
				$data['tpltypename']='tpl_127_index';
				break;
			case 28:
				$data['tpltypeid']=28;
				$data['tpltypename']='tpl_128_index';
				break;
			case 29:
				$data['tpltypeid']=29;
				$data['tpltypename']='tpl_129_index';
				break;
			case 30:
				$data['tpltypeid']=30;
				$data['tpltypename']='tpl_130_index';
				break;
			case 31:
				$data['tpltypeid']=31;
				$data['tpltypename']='tpl_131_index';
				break;
			case 32:
				$data['tpltypeid']=32;
				$data['tpltypename']='lx_index';
				break;
			case 33:
				$data['tpltypeid']=33;
				$data['tpltypename']='im_index';
				break;
			case 34:
				$data['tpltypeid']=34;
				$data['tpltypename']='hx_index';
				break;
			case 35:
				$data['tpltypeid']=35;
				$data['tpltypename']='wh_index';
				break;
			case 36:
				$data['tpltypeid']=36;
				$data['tpltypename']='jz_index';
				break;
			case 37:
				$data['tpltypeid']=37;
				$data['tpltypename']='hm_index';
				break;
			case 38:
				$data['tpltypeid']=38;
				$data['tpltypename']='abc_index';
				break;
			case 39:
				$data['tpltypeid']=39;
				$data['tpltypename']='jqw_index';
				break;
			case 40:
				$data['tpltypeid']=40;
				$data['tpltypename']='wk_index';
				break;
			case 41:
				$data['tpltypeid']=41;
				$data['tpltypename']='mlktv_index';
				break;
			case 42:
				$data['tpltypeid']=42;
				$data['tpltypename']='jinlong_index';
				break;
			case 43:
				$data['tpltypeid']=43;
				$data['tpltypename']='43_index';
				break;
			case 44:
				$data['tpltypeid']=44;
				$data['tpltypename']='44_index';
				break;
			case 45:
				$data['tpltypeid']=45;
				$data['tpltypename']='45_index';
				break;
			case 46:
				$data['tpltypeid']=46;
				$data['tpltypename']='46_index';
				break;
			case 47:
				$data['tpltypeid']=47;
				$data['tpltypename']='47_index';
				break;
			case 48:
				$data['tpltypeid']=48;
				$data['tpltypename']='48_index';
				break;
			case 49:
				$data['tpltypeid']=49;
				$data['tpltypename']='49_index';
				break;
			case 50:
				$data['tpltypeid']=50;
				$data['tpltypename']='50_index';
				break;

			case 51:
				$data['tpltypeid']=51;
				$data['tpltypename']='51_index';
				break;
			case 52:
				$data['tpltypeid']=52;
				$data['tpltypename']='52_index';
				break;
			case 53:
				$data['tpltypeid']=53;
				$data['tpltypename']='53_index';
				break;
			case 54:
				$data['tpltypeid']=54;
				$data['tpltypename']='54_index';
				break;
			case 55:
				$data['tpltypeid']=55;
				$data['tpltypename']='55_index';
				break;
			case 56:
				$data['tpltypeid']=56;
				$data['tpltypename']='56_index';
				break;
			case 57:
				$data['tpltypeid']=57;
				$data['tpltypename']='57_index';
				break;
			case 58:
				$data['tpltypeid']=58;
				$data['tpltypename']='58_index';
				break;
			case 59:
				$data['tpltypeid']=59;
				$data['tpltypename']='59_index';
				break;
			case 60:
				$data['tpltypeid']=60;
				$data['tpltypename']='60_index';
				break;
			case 61:
				$data['tpltypeid']=61;
				$data['tpltypename']='61_index';
				break;
			case 62:
				$data['tpltypeid']=62;
				$data['tpltypename']='62_index';
				break;
			case 63:
				$data['tpltypeid']=63;
				$data['tpltypename']='63_index';
				break;
			case 64:
				$data['tpltypeid']=64;
				$data['tpltypename']='64_index';
				break;
			case 65:
				$data['tpltypeid']=65;
				$data['tpltypename']='65_index';
				break;
			case 66:
				$data['tpltypeid']=66;
				$data['tpltypename']='66_index';
				break;
			case 67:
				$data['tpltypeid']=67;
				$data['tpltypename']='67_index';
				break;
			case 68:
				$data['tpltypeid']=68;
				$data['tpltypename']='68_index';
				break;
			case 69:
				$data['tpltypeid']=69;
				$data['tpltypename']='69_index';
				break;
			case 70:
				$data['tpltypeid']=70;
				$data['tpltypename']='70_index';
				break;
			case 71:
				$data['tpltypeid']=71;
				$data['tpltypename']='71_index';
				break;
			case 72:
				$data['tpltypeid']=72;
				$data['tpltypename']='72_index';
				break;
			case 73:
				$data['tpltypeid']=73;
				$data['tpltypename']='73_index';
				break;
			case 74:
				$data['tpltypeid']=74;
				$data['tpltypename']='74_index';
				break;
			case 75:
				$data['tpltypeid']=75;
				$data['tpltypename']='75_index';
				break;
			case 76:
				$data['tpltypeid']=76;
				$data['tpltypename']='76_index';
				break;
			case 77:
				$data['tpltypeid']=77;
				$data['tpltypename']='77_index';
				break;
			case 78:
				$data['tpltypeid']=78;
				$data['tpltypename']='78_index';
				break;
			case 79:
				$data['tpltypeid']=79;
				$data['tpltypename']='79_index';
				break;
			case 80:
				$data['tpltypeid']=80;
				$data['tpltypename']='80_index';
				break;
			case 81:
				$data['tpltypeid']=81;
				$data['tpltypename']='81_index';
				break;
			case 82:
				$data['tpltypeid']=82;
				$data['tpltypename']='82_index';
				break;
			case 83:
				$data['tpltypeid']=83;
				$data['tpltypename']='83_index';
				break;
			case 84:
				$data['tpltypeid']=84;
				$data['tpltypename']='84_index';
				break;
			case 85:
				$data['tpltypeid']=85;
				$data['tpltypename']='85_index';
				break;
			case 86:
				$data['tpltypeid']=86;
				$data['tpltypename']='86_index';
				break;
			case 87:
				$data['tpltypeid']=87;
				$data['tpltypename']='87_index';
				break;
			case 88:
				$data['tpltypeid']=88;
				$data['tpltypename']='88_index';
				break;
			case 89:
				$data['tpltypeid']=89;
				$data['tpltypename']='89_index';
				break;
        }
        $where['token'] = session('token');
        $db->where($where)->save($data);
        //
        M('Home')->where(array('token'=>session('token')))->save(array('advancetpl'=>0));
    }

    public function lists() {
        $gets = $this->_get('style');
        $db = M('Wxuser');
        switch ($gets) {
            case 1:
                $data['tpllistid'] = 1;
                $data['tpllistname'] = 'list_list1';
                break;
            case 2:
                $data['tpllistid'] = 2;
                $data['tpllistname'] = 'list_list2';
                break;
            case 3:
                $data['tpllistid'] = 3;
                $data['tpllistname'] = 'list_list3';
                break;
            case 4:
                $data['tpllistid'] = 4;
                $data['tpllistname'] = 'list_list4';
                break;
        }
        $where['token'] = session('token');
        $db->where($where)->save($data);
    }

    public function content() {
        $gets = $this->_get('style');
        $db = M('Wxuser');
        switch ($gets) {
            case 1:
                $data['tplcontentid'] = 1;
                $data['tplcontentname'] = 'content_1';
                break;
            case 2:
                $data['tplcontentid'] = 2;
                $data['tplcontentname'] = 'content_2';
                break;
        }
        $where['token'] = session('token');
        $db->where($where)->save($data);
    }
    
    public function background() {
        $data['color_id'] = $this->_get('style');
        $db = M('Wxuser');
        $where['token'] = session('token');
        $db->where($where)->save($data);
    }

    public function insert() {
        
    }

    public function upsave() {
	
    }

}

?>