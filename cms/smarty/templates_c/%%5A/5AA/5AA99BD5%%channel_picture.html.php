<?php /* Smarty version 2.6.18, created on 2014-08-20 02:51:22
         compiled from 2/jzugpi1403189315/channel_picture.html */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['header'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div data-role="content" id="main">

    <div class="producttit">
      <div class="title"><span class="fl"><?php echo $this->_tpl_vars['channel']['name']; ?>
</span></div>
    </div>
<?php if ($this->_tpl_vars['subChannels']): ?>
	 <div class="view_menu"><span>展开分类</span></div>
  <div class="view_menumain">
  <?php $_from = $this->_tpl_vars['subChannels']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['c']):
?>
         <a href="<?php echo $this->_tpl_vars['c']['link']; ?>
" class="ui-bar-g" data-ajax="false"><?php echo $this->_tpl_vars['c']['name']; ?>
</a>
         <?php endforeach; endif; unset($_from); ?>
      </div>
	<?php endif; ?>
  <div class="clear"></div>
    <div class="padding20">
      <div class="proul">
        <ul class="ui-grid-a">
         <?php if ($this->_tpl_vars['contents']): ?>
			<?php $_from = $this->_tpl_vars['contents']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['a']):
?>
          						          <li class="ui-block-b">
            <div><a href="<?php echo $this->_tpl_vars['a']['link']; ?>
"  data-ajax="false"><img src="<?php echo $this->_tpl_vars['a']['thumb']; ?>
"  alt="<?php echo $this->_tpl_vars['a']['title']; ?>
" />
              <p><?php echo $this->_tpl_vars['a']['title']; ?>
</p>
              </a></div>
          </li>
          <?php endforeach; endif; unset($_from); ?>
			 <?php endif; ?>
					          
					 				         </ul>
      </div>
	    <div class="pages ui-grid-b">  <div class="ui-block-a"><div class="left" id="dis">上一页</div></div>
    <div class="ui-block-b"><div class="page_change"><?php echo $this->_tpl_vars['currentPage']; ?>
/<?php echo $this->_tpl_vars['totalPage']; ?>
</div></div>
    <div class="ui-block-c"><div class="right"><a href="<?php echo $this->_tpl_vars['nextPageLink']; ?>
"  data-ajax="false">下一页</a></div></div>
</div>
	    </div>
  </div>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['footer'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>