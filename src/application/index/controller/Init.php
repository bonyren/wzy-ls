<?php
 namespace app\index\controller; use think\Controller; use think\Log; use think\Debug; use think\Request; class Init extends Common { public function init() { $initLogic = new \app\index\logic\Init(); $initLogic->init(); } }
