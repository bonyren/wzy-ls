<?php
 namespace app\index\controller; use think\Log; use think\Url; use think\Cookie; use think\Session; use think\captcha\Captcha; use think\Request; use think\Cache; use app\Defs; use app\index\Defs as IndexDefs; use app\index\logic\Admins as AdminsLogic; use app\index\logic\Messages as MessagesLogic; class Index extends Common { public function _initialize() { parent::_initialize(); } public function public_sessionLife() { goto l2eDQ; a7pTM: return ajaxError("\350\257\267\xe5\205\210\347\x99\xbb\xe5\275\x95", $loginUrl); goto aXSsm; vMFzD: if (!(session_id() !== Cache::get("\123\x45\x53\x53\x49\x4f\x4e\137\111\x44\x5f" . $this->loginUserId . $this->loginUserType))) { goto udg1m; } goto jxDcj; Z5na9: udg1m: goto GsSHy; GsSHy: a7n0H: goto JcJz6; bBDaU: if (!(systemSetting("\114\x4f\x47\111\116\137\117\116\114\x59\x5f\x4f\116\x45") == "\171\x65\x73")) { goto a7n0H; } goto vMFzD; fd9BE: if (!($this->loginUserId === null)) { goto jTJCh; } goto a7pTM; l2eDQ: $loginUrl = url("\x69\156\144\145\x78\x2f\x49\x6e\x64\145\170\x2f\154\157\147\x69\x6e"); goto fd9BE; aXSsm: jTJCh: goto bBDaU; JcJz6: return ajaxSuccess("\xe6\223\x8d\344\xbd\x9c\xe6\x88\x90\345\x8a\237"); goto ZMQ_Y; jxDcj: return ajaxError("\345\270\220\xe5\x8f\xb7\xe5\267\262\345\234\xa8\345\205\266\xe4\xbb\x96\xe5\234\260\xe6\226\271\347\x99\xbb\345\xbd\x95\357\xbc\214\xe6\x82\xa8\xe5\267\262\350\xa2\xab\xe8\277\253\xe4\xb8\x8b\xe7\272\277\xef\274\x81", url("\x69\156\144\x65\170\x2f\111\x6e\x64\145\x78\x2f\x6c\x6f\x67\x6f\165\164")); goto Z5na9; ZMQ_Y: } public function public_clearCache() { rrmdir(TEMP_PATH); return ajaxSuccess("\346\223\x8d\344\xbd\x9c\xe6\x88\x90\345\212\x9f"); } public function index() { goto rqUlr; B1EuM: VNyqC: goto ynXst; jb9B1: $this->assign("\x6c\x6f\x67\x69\156\x55\163\x65\162\111\156\x66\157\163", $loginUserInfos); goto O0IO2; sqio1: $this->assign("\165\162\154\110\162\145\146\x73", $urlHrefs); goto Y3myR; ZE72t: return $this->fetch(); goto gTrIN; O0IO2: if ($this->loginMobile) { goto kgnhS; } goto FmTTG; gTrIN: goto VNyqC; goto W22ma; Byk4S: AdminsLogic::newObj()->loadLeftMenuRecursively(0, '', $menus); goto xyEbP; W22ma: kgnhS: goto BBTSk; rqUlr: $urlHrefs = array("\154\x6f\x61\x64\x4c\x65\146\164\x4d\x65\156\x75" => Url::build("\151\156\x64\x65\170\x2f\111\x6e\144\x65\170\57\x6c\157\x61\x64\114\145\x66\164\x4d\145\x6e\165"), "\x6d\141\151\156" => url("\151\x6e\x64\145\170\57\x49\156\144\x65\170\57\x6d\141\x69\156"), "\x6c\x6f\x67\x6f\x75\164" => url("\151\156\x64\145\x78\x2f\x49\156\144\x65\170\x2f\x6c\x6f\147\157\x75\x74"), "\160\165\142\x6c\151\x63\123\x65\x73\163\x69\x6f\156\x4c\x69\x66\x65" => url("\x69\x6e\144\x65\170\x2f\x49\x6e\144\x65\x78\x2f\160\165\x62\x6c\x69\143\137\163\145\163\163\151\x6f\x6e\114\x69\x66\x65"), "\160\x75\142\x6c\x69\x63\x43\154\145\141\162\103\x61\143\x68\145" => url("\x69\156\144\145\x78\x2f\111\x6e\144\x65\170\x2f\160\x75\x62\x6c\151\143\x5f\143\154\x65\141\162\x43\141\x63\150\145"), "\155\157\x64\151\146\x79\120\167\144" => url("\x69\156\144\x65\x78\57\x49\156\144\145\x78\x2f\x6d\157\x64\x69\146\171\120\167\144")); goto sqio1; Y3myR: $loginUserInfos = ["\165\163\145\x72\x6e\141\155\x65" => $this->loginUserName, "\162\x65\x61\x6c\x6e\x61\155\x65" => $this->loginRealName, "\x6c\x61\x73\164\x6c\x6f\x67\x69\x6e\x74\x69\x6d\x65" => $this->loginTime, "\154\141\163\164\x6c\157\x67\x69\156\x69\x70" => $this->loginIp, "\x75\156\x72\145\141\x64\115\145\x73\x73\141\147\145\x43\157\165\156\x74" => MessagesLogic::newObj()->unreadCount($this->loginUserId)]; goto jb9B1; xyEbP: return $this->fetch("\x6d\157\x62\x69\x6c\x65", ["\x6d\x65\156\165\163" => $menus]); goto B1EuM; BBTSk: $menus = []; goto Byk4S; FmTTG: $this->assign("\165\x72\154\x48\162\145\x66\163", $urlHrefs); goto ZE72t; ynXst: } public function loadLeftMenu() { goto AgyER; GetIP: return json($menus); goto yMdqc; AgyER: $menus = []; goto UBPqn; UBPqn: AdminsLogic::newObj()->loadLeftMenuRecursively(0, '', $menus); goto GetIP; yMdqc: } public function main() { goto NIQge; WaF7S: $this->assign("\x75\x72\x6c\110\162\145\146\163", $urlHrefs); goto Fzg9f; NIQge: $urlHrefs = ["\x73\x74\x61\164\x69\x73\x74\x69\x63" => url("\151\x6e\x64\145\170\57\104\141\163\150\142\x6f\x61\x72\x64\57\163\x74\141\164\x69\x73\164\151\143"), "\164\162\x65\x6e\x64" => url("\151\x6e\144\x65\170\57\x44\x61\163\x68\x62\157\141\x72\x64\57\x74\x72\145\156\x64"), "\x65\x76\145\156\x74\163" => url("\151\156\x64\145\170\x2f\x44\141\x73\150\142\x6f\141\162\x64\57\145\166\x65\156\164\163")]; goto WaF7S; Fzg9f: return $this->fetch(); goto DTL93; DTL93: } public function login() { goto UaOS9; AzA28: ZUPro: goto mziAm; tVZOS: if ($captchaOk) { goto i8B5Y; } goto CbRqN; jmJLU: Session::delete("\x6c\157\x67\151\156\137\x63\x61\160\x74\x63\150\x61\137\x65\x6e\141\142\x6c\x65"); goto WfxVH; eRMTJ: fiAzo: goto AfUI6; NwbxJ: $captcha = new Captcha(); goto D7yhB; XZO73: $password = input("\x70\157\163\x74\56\x70\x61\163\x73\167\x6f\x72\x64\57\163", ''); goto LGp8S; AnAbk: Cookie::set("\x61\x75\164\157\137\x6c\157\147\151\x6e\x5f\164\x6f\x6b\145\x6e", $autoLoginToken, 3600 * 24 * 30); goto eRMTJ; gDVCS: if (!$this->loginUserId) { goto kfcIN; } goto bRWyS; j2Lyj: $captchaCode = input("\160\x6f\x73\164\56\143\x61\x70\x74\x63\150\x61\x2f\x73"); goto NwbxJ; Vj0YD: goto fiAzo; goto ljZ4X; tVzTd: $autoLoginToken = $this->autoLoginToken($username, $password); goto AnAbk; WfxVH: if ($autoLogin) { goto odoNi; } goto LTANV; bnwOg: $this->redirect("\x69\x6e\144\145\170\x2f\111\156\x64\145\170\x2f\151\156\x64\145\170"); goto gPj2c; iij6z: if (!$this->autoLogin()) { goto UiwJW; } goto bnwOg; B9NPB: i8B5Y: goto AzA28; AfUI6: $this->redirect("\151\x6e\x64\x65\x78\57\x49\x6e\x64\145\x78\57\151\x6e\x64\145\x78"); goto j2DFL; WUeGK: $username = input("\x70\157\163\x74\56\x75\x73\x65\162\156\141\155\145\57\163", ''); goto XZO73; D7yhB: $captchaOk = $captcha->check($captchaCode, "\154\x6f\x67\x69\156"); goto tVZOS; ljZ4X: odoNi: goto tVzTd; lqpK3: $this->assign("\165\162\x6c\163", ["\x63\141\x70\x74\x63\150\x61" => url("\151\x6e\144\145\170\x2f\111\156\x64\145\x78\x2f\x63\141\x70\x74\143\150\141", ["\x63\x6f\144\145\137\154\145\156" => 4, "\146\157\156\164\x5f\163\151\172\145" => 12, "\167\151\x64\x74\x68" => 95, "\x68\145\x69\147\150\164" => 30, "\x63\157\x64\x65" => time()]), "\x6c\157\147\151\x6e" => url("\x69\156\144\145\170\57\x49\156\x64\145\x78\x2f\154\157\x67\151\x6e")]); goto LvCPa; J4I2i: $adminsLogic = AdminsLogic::newObj(); goto s0M_c; LvCPa: return $this->fetch(); goto bLa_Z; CbRqN: $this->error("\351\xaa\214\xe8\xaf\201\xe7\240\x81\xe9\224\x99\xe8\257\xaf"); goto B9NPB; s0M_c: try { $adminsLogic->login($username, $password); } catch (\Exception $e) { Session::set("\x6c\x6f\147\151\x6e\137\143\x61\x70\x74\x63\x68\x61\137\145\156\141\x62\x6c\x65", true); $this->error($e->getMessage(), "\151\156\144\145\170\57\x49\x6e\x64\x65\x78\57\x6c\157\147\x69\156"); } goto jmJLU; mziAm: $autoLogin = input("\160\157\163\164\x2e\x61\x75\164\x6f\137\154\157\147\x69\156", null); goto J4I2i; gPj2c: UiwJW: goto M2Izl; M2Izl: $this->assign("\154\x6f\x67\x69\x6e\x5f\x63\x61\160\164\143\x68\141\x5f\x65\x6e\x61\x62\154\145", Session::get("\154\x6f\x67\151\156\x5f\x63\141\160\164\x63\x68\x61\137\x65\x6e\141\x62\154\145")); goto lqpK3; LTANV: Cookie::delete("\x61\165\164\157\x5f\154\x6f\x67\151\x6e\137\x74\x6f\x6b\x65\x6e"); goto Vj0YD; Kiusf: kfcIN: goto iij6z; bLa_Z: z_xo8: goto WUeGK; LGp8S: if (!Session::get("\154\x6f\x67\151\x6e\x5f\x63\x61\160\164\x63\150\141\x5f\145\x6e\141\x62\x6c\145")) { goto ZUPro; } goto j2Lyj; bRWyS: $this->redirect("\x69\x6e\144\x65\x78\57\x49\156\x64\x65\x78\x2f\x69\156\x64\145\170"); goto Kiusf; UaOS9: if (!request()->isGet()) { goto z_xo8; } goto gDVCS; j2DFL: } public function logout() { goto MORbN; QYZ3m: Cookie::delete("\x61\x75\164\x6f\x5f\154\x6f\x67\x69\156\x5f\x74\157\153\145\x6e"); goto ZcUYP; s1YEQ: $adminsLogic->logout(); goto QYZ3m; MORbN: $adminsLogic = AdminsLogic::newObj(); goto s1YEQ; ZcUYP: $this->success("\xe6\210\x90\345\212\x9f\347\231\xbb\xe5\x87\272", "\x69\x6e\x64\145\x78\x2f\x49\156\x64\x65\170\57\x6c\x6f\147\x69\156"); goto Y2oE3; Y2oE3: } public function captcha() { goto YESMu; YESMu: $captcha = new Captcha(); goto eLM4f; BT2Ho: mnwpA: goto HJsYQ; aluU1: $captcha->imageW = 130; goto Dxw47; l7nym: $captcha->imageW = intval(input("\147\x65\164\56\167\151\144\x74\150")); goto E_J3G; kFInz: if (!input("\x67\x65\x74\x2e\167\151\144\x74\150")) { goto hwulX; } goto l7nym; ZWrhb: $captcha->length = 4; goto BT2Ho; AhMwb: $captcha->imageH = intval(input("\x67\x65\164\56\150\x65\x69\147\150\x74")); goto jCJWt; HJsYQ: if (!input("\147\145\164\x2e\x66\157\156\x74\x5f\163\x69\x7a\x65")) { goto Qf_aJ; } goto nsiIR; nsiIR: $captcha->fontSize = intval(input("\x67\x65\164\56\146\157\156\164\x5f\x73\x69\x7a\145")); goto GBl1I; aDpVt: $captcha->length = intval(input("\x67\145\x74\x2e\x63\157\144\x65\x5f\154\x65\x6e")); goto H8X9R; GBl1I: Qf_aJ: goto kFInz; hlS0R: if (!input("\x67\145\x74\56\143\x6f\x64\145\137\154\145\x6e")) { goto Pkihn; } goto aDpVt; LqZn8: dopN1: goto txHzg; eLM4f: $captcha->useCurve = false; goto EUtO6; jCJWt: qPBce: goto u_hJv; E_J3G: hwulX: goto JqZ6k; u_hJv: if (!($captcha->imageH <= 0)) { goto dopN1; } goto fyMyo; Dxw47: DXWbW: goto LX13J; LX13J: if (!input("\x67\145\164\56\150\x65\151\x67\150\x74")) { goto qPBce; } goto AhMwb; fyMyo: $captcha->imageH = 50; goto LqZn8; qnUf3: if (!($captcha->length > 8 || $captcha->length < 2)) { goto mnwpA; } goto ZWrhb; EUtO6: $captcha->useNoise = false; goto mJ1Wo; mJ1Wo: $captcha->bg = array(255, 255, 255); goto hlS0R; JqZ6k: if (!($captcha->imageW <= 0)) { goto DXWbW; } goto aluU1; H8X9R: Pkihn: goto qnUf3; txHzg: return $captcha->entry("\x6c\157\147\151\156"); goto sdPl5; sdPl5: } public function modifyPwd() { goto thahu; GKSS4: return $this->fetch(); goto QrJZn; zf8vX: $oldPassword = input("\x70\x6f\x73\x74\56\157\154\144\137\x70\141\163\x73\x77\157\x72\x64\x2f\x73"); goto FwbJS; FwbJS: $newPassword = input("\x70\x6f\x73\x74\x2e\156\145\x77\137\160\141\163\163\x77\157\x72\x64\57\x73"); goto ZDW0l; khDn2: return ajaxError("\xe4\xbf\256\xe6\x94\xb9\345\257\206\347\240\201\xe5\244\xb1\xe8\xb4\245"); goto W7PPE; ownFn: beWjB: goto g8fTM; ZDW0l: if ($this->loginUserType == IndexDefs::LOGIN_USER_ADMIN_TYPE) { goto beWjB; } goto f17Ma; c8tlc: $result = $adminsLogic->modifyAdminPwd($this->loginUserId, $oldPassword, $newPassword); goto i_03z; qviKN: Cookie::delete("\x61\x75\164\157\x5f\x6c\157\x67\151\x6e\x5f\164\157\153\x65\x6e"); goto t11jj; Wljcz: $this->assign("\151\156\146\157", ["\x75\x73\145\x72\x6e\x61\x6d\x65" => $this->loginUserName]); goto GKSS4; W7PPE: goto aOmXa; goto KRjnL; t11jj: $adminsLogic->logout(); goto TO0fO; f17Ma: return ajaxError("\346\227\xa0\346\263\225\xe8\xaf\206\xe5\210\xab\347\232\x84\xe7\x94\250\346\x88\267\347\xb1\273\xe5\236\213"); goto PWvtR; i_03z: rdxBv: goto MauED; QrJZn: AxR12: goto zf8vX; Cc762: aOmXa: goto eHdyb; PWvtR: goto rdxBv; goto ownFn; g8fTM: $adminsLogic = AdminsLogic::newObj(); goto c8tlc; thahu: if (!request()->isGet()) { goto AxR12; } goto Wljcz; KRjnL: UOw2y: goto qviKN; TO0fO: return ajaxSuccess("\346\x88\x90\xe5\212\237\344\xbf\256\346\x94\271\345\257\206\xe7\240\x81\xef\274\x8c\350\257\267\xe9\207\215\346\226\xb0\347\x99\273\xe5\xbd\x95", url("\151\156\144\x65\x78\x2f\x49\x6e\144\x65\170\57\154\157\147\x69\156")); goto Cc762; MauED: if ($result) { goto UOw2y; } goto khDn2; eHdyb: } }
