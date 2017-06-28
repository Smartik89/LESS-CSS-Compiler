<?php 
$execute = new ZeroWPLCC\Component\Compiler\Execute;
$execute->init();

// Support for "ZeroWP Customizer Presets" plugin
new ZeroWPLCC\Component\Compiler\PresetsSupport;