<?php
 namespace app\index\controller; use app\Defs; use app\index\controller\Common; use app\index\logic\Admins; use app\index\logic\Menu; use think\Db; class Menus extends Common { public function index($search = array(), $page = 1, $rows = 20) { goto at2Qu; at2Qu: if (!$this->request->isGet()) { goto JXqGI; } goto BsG7W; wZja8: $menus[] = ["\151\x64" => 0, "\164\x65\x78\x74" => '']; goto rrlgY; hy0jS: return $this->fetch(); goto AS9fQ; AS9fQ: JXqGI: goto YSh8Y; joCpa: if (!isset($_GET["\x73\x68\157\x77\x5f\145\x6d\x70\x74\x79"])) { goto gGyx0; } goto wZja8; BsG7W: $urls = ["\x6c\151\x73\164" => url("\x69\x6e\x64\145\x78\x2f\155\145\x6e\x75\x73\x2f\151\x6e\144\145\170"), "\x65\x64\151\164" => url("\151\156\144\x65\170\57\x6d\x65\x6e\x75\163\57\x65\x64\x69\x74"), "\x64\145\154\145\x74\145" => url("\151\156\144\x65\170\x2f\x6d\145\x6e\x75\x73\x2f\x64\145\x6c\145\164\145")]; goto ptkUs; mkxWv: Admins::I()->loadLeftMenuRecursively(0, '', $menus); goto zJWJs; rrlgY: gGyx0: goto mkxWv; YSh8Y: $menus = []; goto joCpa; ptkUs: $this->assign("\x75\x72\x6c\163", $urls); goto hy0jS; zJWJs: return json($menus); goto s5EDu; s5EDu: } public function edit($id = 0, $pid = 0) { goto o2vm3; Fq0T_: $data = $data["\144\x61\164\x61"]; goto GfAbG; D2ms9: $data = input("\160\x6f\163\164\x2e"); goto Fq0T_; YhHl7: $this->assign("\164\162\x65\x65\x5f\x64\x61\x74\x61\x5f\x75\x72\x6c", url("\x69\156\144\145\170\x2f\x6d\145\156\165\x73\x2f\151\x6e\x64\145\x78", "\x73\150\157\x77\x5f\145\x6d\160\164\x79\75\x31")); goto Cyowb; rcer7: $row = Menu::I()->getRow($id); goto CFZrq; b3qIZ: jzXIr: goto rcer7; XsABO: if ($id) { goto jzXIr; } goto PKZh7; vdc3d: mpE_X: goto XsABO; GfAbG: try { Menu::I()->save($id, $data); return ajaxSuccess("\xe4\xbf\x9d\345\xad\x98\xe6\210\x90\xe5\212\237"); } catch (\Exception $e) { return ajaxError($e->getMessage()); } goto vdc3d; CFZrq: u2JCy: goto FjAHp; Cyowb: return $this->fetch(); goto M_xLk; zpuiV: goto u2JCy; goto b3qIZ; elX36: $row["\x70\151\x64"] = intval($pid); goto zpuiV; FjAHp: $this->assign("\162\x6f\x77", $row); goto YhHl7; o2vm3: if (!$this->request->isPost()) { goto mpE_X; } goto D2ms9; PKZh7: $row = Menu::I()->getDefaultRow(); goto elX36; M_xLk: } public function delete($id) { try { Menu::I()->delete($id); } catch (\Exception $e) { return ajaxError($e->getMessage()); } return ajaxSuccess("\xe5\210\xa0\351\x99\xa4\xe6\x88\220\xe5\212\237"); } }