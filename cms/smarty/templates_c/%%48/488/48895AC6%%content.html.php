<?php /* Smarty version 2.6.18, created on 2014-03-11 12:07:35
         compiled from tpls/red1/content.html */ ?>
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
<div class="clear"></div>
<div class="sub" style="margin-top:20px;">
<div class="clear"></div>
    <h1><?php echo $this->_tpl_vars['content']['title']; ?>
</h1>
    <div class="view" id="content"><?php echo $this->_tpl_vars['content']['content']; ?>
</div>
    <a href="<?php echo $this->_tpl_vars['channel']['link']; ?>
" title="返回列表" class="backlist">返回列表</a>
    <div class="clear"></div>
</div>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['footer'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
