<?php
 namespace app\index\controller; use think\Controller; use think\Log; use think\Debug; use think\Request; class Help extends Common { public function index() { goto Zvt3d; e73rU: Log::notice("\124\110\x49\x4e\113\x5f\x50\101\x54\x48\x2d\55\x2d\x2d" . THINK_PATH); goto Ygz79; Ygz79: Log::notice("\x5f\137\104\111\122\137\x5f\40\x2d\x2d\x2d\x2d" . __DIR__); goto oOMk3; Zvt3d: Log::notice("\x24\x5f\123\105\122\x56\105\122\55\55\x2d\x2d" . var_export($_SERVER, true)); goto w0jJG; w0jJG: Log::notice("\101\120\x50\137\x50\101\x54\x48\x2d\55\x2d\55" . APP_PATH); goto e73rU; oOMk3: } public function help($topicId) { goto myyn1; Evp2z: gVTtn: goto mMqVN; mMqVN: return $this->fetch($tpl); goto DSglV; myyn1: $helpLogic = \app\index\logic\Help::newObj(); goto QvFKP; cuoQ9: return $this->fetch("\143\157\x6d\x6d\x6f\x6e\x2f\145\x72\162\157\x72"); goto Evp2z; QvFKP: $tpl = $helpLogic->getTpl($topicId); goto skHQt; skHQt: if ($tpl) { goto gVTtn; } goto cuoQ9; DSglV: } }
