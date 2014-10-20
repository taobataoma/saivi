<?php /* Smarty version 2.6.18, created on 2014-05-20 11:40:40
         compiled from 1/hziflr1400551929/content.html */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['header'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php echo '
<style>
#content img{max-width:92%;}
</style>
'; ?>
<?php if ($this->_tpl_vars['ismap']): ?>
<?php echo $this->_tpl_vars['mapstr']; ?>

<?php else: ?>
	<h1><p><?php echo $this->_tpl_vars['content']['title']; ?>
</p></h1>
	<div class="view" id="content">　<?php echo $this->_tpl_vars['content']['content']; ?>
</div>
    <p class="hr"></p>
     <?php if ($this->_tpl_vars['previousContent']): ?>
	<a href="<?php echo $this->_tpl_vars['previousContent']->link; ?>
"  title="上一条：<?php echo $this->_tpl_vars['previousContent']->title; ?>
" class="prevpage">上一条：<span><?php echo $this->_tpl_vars['previousContent']->title; ?>
</span></a> <?php endif; ?>    <?php if ($this->_tpl_vars['nextContent']): ?><a href="javascript:if(confirm(%27<?php echo $this->_tpl_vars['nextContent']->link; ?>
  \n\nThis file was not retrieved by Teleport Pro, because it is linked too far away from its Starting Address. If you increase the in-domain depth setting for the Starting Address, this file will be queued for retrieval.  \n\nDo you want to open it from the server?%27))window.location=%27<?php echo $this->_tpl_vars['nextContent']->link; ?>
" tppabs="<?php echo $this->_tpl_vars['nextContent']->link; ?>
" title="下一条：“<?php echo $this->_tpl_vars['nextContent']->title; ?>
" class="nextpage">下一条：<span><?php echo $this->_tpl_vars['nextContent']->title; ?>
</span></a><?php endif; ?>    <a href="<?php echo $this->_tpl_vars['channel']['link']; ?>
"  title="返回列表" class="backlist">返回列表</a>
<div class="clear"></div>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['footer'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>