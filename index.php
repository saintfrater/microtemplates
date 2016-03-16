<?php

include ('./libs/microtemplate.php');

$template = new MicroTemplates;

$template->load('templates/index.tpl');

$template->assign('title','This is the title');
$template->assign('test','This is the test');

$template->render();
?>
