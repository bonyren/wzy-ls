<?php
 namespace app\index\controller; use think\Controller; use think\Db; use think\Log; use think\Debug; use app\index\model\Setting; class System extends Common { public function setting() { goto txs32; PxUtD: return ajaxError("\xe5\xa4\261\xe8\264\245"); goto CD1Vq; TP7Db: $settingModel = model("\x53\x65\x74\164\151\x6e\147"); goto HGuQ3; DQ10c: goto WhZSW; goto AXXuy; Aou4i: if ($state) { goto YjTF2; } goto PxUtD; jrukn: AmCpi: goto tbOER; mjM1E: $state = $settingModel->saveSetting(input("\x70\157\x73\x74\x2e\144\141\x74\141\x2f\x61")); goto Aou4i; C86QG: return json($data); goto DQ10c; Umqaa: $urlHrefs = ["\x73\145\x74\164\151\156\x67" => url("\163\145\x74\x74\x69\x6e\x67"), "\163\145\x74\x74\151\156\x67\123\141\166\x65" => url("\163\x65\x74\x74\151\x6e\x67", ["\144\x6f\x73\x75\x62\155\x69\x74" => 1]), "\x73\145\164\x74\x69\x6e\x67\104\x65\146\141\x75\154\x74" => url("\163\145\x74\x74\151\x6e\147\x44\x65\x66\141\165\154\164"), "\x73\145\x74\x74\151\x6e\x67\x45\170\160\x6f\x72\164" => url("\x73\145\x74\x74\151\x6e\147\x45\x78\160\157\162\x74"), "\x73\145\x74\164\x69\156\147\111\155\x70\x6f\x72\x74" => url("\x73\x65\164\164\151\x6e\147\x49\x6d\x70\x6f\162\x74"), "\151\x6d\x70\157\162\x74\125\x70\154\x6f\141\144" => url("\x49\155\x70\x6f\162\x74\x2f\x69\x6d\x70\157\162\x74"), "\146\151\x6c\145\x55\x70\154\x6f\x61\144" => url("\x55\160\x6c\x6f\x61\x64\57\165\160\x6c\157\141\x64")]; goto T4Ec7; T4Ec7: $this->assign("\165\162\x6c\x48\162\x65\x66\x73", $urlHrefs); goto RzeLG; CD1Vq: goto AmCpi; goto EL8VH; HGuQ3: if (input("\x67\x65\164\56\x64\157\x73\x75\x62\x6d\x69\x74")) { goto BKtDa; } goto xkhZp; xkhZp: $data = array_values($settingModel->getSetting()); goto C86QG; tbOER: WhZSW: goto Qg9Wa; RzeLG: return $this->fetch(); goto S3y2x; AXXuy: BKtDa: goto mjM1E; IpOOl: t9KSV: goto TP7Db; LodLU: $settingModel->clearCache(); goto jC3J_; Qg9Wa: mg7Cn: goto ixchk; jC3J_: return ajaxSuccess("\xe6\x93\x8d\xe4\xbd\x9c\xe6\x88\220\xe5\212\237"); goto jrukn; EL8VH: YjTF2: goto LodLU; S3y2x: goto mg7Cn; goto IpOOl; txs32: if (request()->isPost()) { goto t9KSV; } goto Umqaa; ixchk: } public function settingDefault() { goto h12Ju; ozE8u: if (!$settingModel->count()) { goto MU2Tm; } goto dE1g0; tfBbe: return ajaxSuccess("\xe6\x93\215\xe4\xbd\x9c\346\210\220\xe5\x8a\x9f"); goto krP84; EjCmw: qj9Pf: goto yqzHW; pGrGc: MU2Tm: goto tfBbe; e5ep4: if ($state) { goto qj9Pf; } goto aII4_; dE1g0: $state = $settingModel->where("\x60\x6b\x65\x79\x60\x20\74\76\40\47\47")->delete(); goto e5ep4; SK6OW: $settingModel = model("\x53\145\x74\164\151\x6e\147"); goto ozE8u; ItPNG: dWYRx: goto pGrGc; h12Ju: if (!request()->isPost()) { goto bVI8h; } goto SK6OW; yqzHW: $settingModel->clearCache(); goto L2j76; LrbY4: goto dWYRx; goto EjCmw; aII4_: return ajaxError("\345\xa4\xb1\350\xb4\245"); goto LrbY4; krP84: bVI8h: goto CQ59N; L2j76: return ajaxSuccess("\xe6\x93\x8d\xe4\275\234\346\210\x90\xe5\212\x9f"); goto ItPNG; CQ59N: } public function settingExport($filename = '') { goto XSB0U; OotxB: readfile($filename); goto yN43M; GLIg6: $filename = EXPORT_DIR . DS . $filename . "\56\144\141\164\141"; goto R0Ck2; cpcBd: TTWba: goto nvizh; YDVUW: $data["\x76\145\x72\151\146\171"] = md5(var_export($data["\x64\141\164\x61"], true) . $data["\164\x79\160\145"]); goto SI0Ya; R0Ck2: if (file_exists($filename)) { goto Xxt8A; } goto SeckX; El7eM: $filename = EXPORT_DIR . DS . $uniqid . "\56\x64\141\164\x61"; goto CGB_h; KEyuT: Xxt8A: goto PQQ0g; VGdPc: $settingModel = model("\x53\145\x74\x74\151\156\147"); goto WHDVk; WHDVk: $data = array("\164\171\x70\145" => "\163\145\164\x74\151\156\x67"); goto W1RtD; ewJCC: goto eyNGe; goto MXuyr; ORT2E: header("\x43\157\x6e\164\145\156\164\x2d\104\x69\x73\160\x6f\x73\x69\164\x69\157\156\x3a\40\141\164\164\141\143\x68\x6d\145\156\164\x3b\x20\x66\151\154\x65\156\141\x6d\x65\x3d\x22\163\x79\163\x74\145\x6d\x5f\163\145\x74\x74\x69\x6e\147\x2e\144\141\x74\141\42"); goto OotxB; MfKl9: eyNGe: goto GhrkI; W1RtD: $data["\144\141\x74\141"] = Db::table("\x73\x65\x74\x74\151\156\147")->select(); goto YDVUW; ghpMW: exit; goto ewJCC; yN43M: unlink($filename); goto ghpMW; bjoRs: $uniqid = uniqid(); goto El7eM; nvizh: return ajaxError("\345\244\xb1\xe8\264\xa5"); goto MfKl9; PQQ0g: header("\103\157\x6e\x74\x65\156\x74\x2d\164\171\x70\145\72\40\x61\x70\x70\154\x69\x63\141\x74\151\157\156\57\157\x63\x74\x65\164\x2d\163\164\x72\145\x61\x6d"); goto ORT2E; CGB_h: if (!file_put_contents($filename, $data)) { goto TTWba; } goto b7gMB; b7gMB: return ajaxSuccess("\xe6\210\220\345\212\x9f", url("\x53\171\x73\x74\145\x6d\57\x73\x65\164\164\x69\x6e\147\x45\170\160\157\x72\164", array("\146\151\x6c\x65\156\x61\155\x65" => $uniqid))); goto cpcBd; wEWBQ: $filename = str_replace(array("\56", "\57", "\x5c"), '', $filename); goto GLIg6; SI0Ya: $data = base64_encode(gzdeflate(json_encode($data))); goto bjoRs; MXuyr: aacXX: goto VGdPc; SeckX: return $this->fetch("\143\157\155\x6d\x6f\x6e\x2f\145\x72\x72\x6f\x72"); goto KEyuT; XSB0U: if (request()->isPost()) { goto aacXX; } goto wEWBQ; GhrkI: } public function settingImport($filename = '') { goto PbTyl; M9EIj: return ajaxError("\345\244\xb1\xe8\264\245"); goto dkoKY; ZlKX4: if (isset($data)) { goto x5aZw; } goto YVHPr; P9xd8: Log::error("\146\x69\x6c\x65\72\40{$filePath}\x20\x69\x73\x20\x6e\157\x74\x20\x65\170\x69\x73\164"); goto fx9Et; MKvXK: $settingModel->where("\x60\153\x65\x79\x60\40\74\x3e\40\x27\x27")->delete(); goto JfwkR; lM5iV: return ajaxError("\345\244\xb1\xe8\264\245"); goto o3f2V; sDGQO: try { $data = gzinflate(base64_decode($content)); } catch (\Exception $e) { } goto ZlKX4; MHhtN: unlink($filePath); goto oIKyq; UQQCX: $content = file_get_contents($filePath); goto sDGQO; j1SaR: if (!($data["\166\145\162\x69\x66\x79"] != md5(var_export($data["\144\141\x74\x61"], true) . $data["\164\x79\x70\x65"]))) { goto AbEvF; } goto MHhtN; DYmXD: goto KrHMy; goto IWuwb; pnazH: foreach ($data["\144\x61\164\x61"] as $add) { goto oAuDy; Yg0Oi: iUrdZ: goto YC2wR; oAuDy: $settingModel->key = $add["\x6b\x65\x79"]; goto N7aR0; WlYTv: $settingModel->isUpdate(false)->save(); goto Yg0Oi; N7aR0: $settingModel->value = $add["\166\141\x6c\165\x65"]; goto WlYTv; YC2wR: } goto i44Lz; Tkn7S: unlink($filePath); goto OWsBM; sH9Cy: try { $data = json_decode($data, true); } catch (\Exception $e) { } goto Zg67O; oIKyq: Log::error("\x73\x65\x74\x74\x69\x6e\147\111\x6d\160\157\x72\x74\54\40\146\151\154\145\72\40{$filePath}\54\x20\146\x61\151\154\145\144\x20\x74\157\x20\x76\x65\x72\x69\146\x79\40\x69\164\x2c\x20\x76\x65\x72\x69\x66\171\72\x20{$data["\x76\145\162\x69\146\x79"]}"); goto uYkXl; v0gwD: Log::error("\x66\x69\154\x65\72\x20{$filePath}\x2c\40\x66\141\x69\x6c\x65\x64\x20\164\157\40\x64\145\x63\x6f\x64\x65\x20\x69\164"); goto lM5iV; fx9Et: return ajaxError("\345\xa4\261\350\xb4\245"); goto c2tt2; iSloH: return ajaxError("\xe5\xa4\261\350\xb4\245"); goto DYmXD; sf_f5: $settingModel = model("\123\145\x74\x74\x69\156\147"); goto MKvXK; BygTC: AbEvF: goto sf_f5; rrlFc: unlink($filePath); goto v0gwD; c2tt2: yzYw3: goto UQQCX; uYkXl: return ajaxError("\xe5\xa4\261\350\264\245"); goto BygTC; i44Lz: UBXzn: goto Tkn7S; H_Ra5: $filePath = IMPORT_DIR . DS . $filename; goto u3uIZ; PbTyl: if (request()->isPost()) { goto URBmi; } goto iSloH; o3f2V: t30Kc: goto j1SaR; f4dyY: asort($data["\144\x61\164\x61"]); goto pnazH; IWuwb: URBmi: goto H_Ra5; u3uIZ: if (file_exists($filePath)) { goto yzYw3; } goto P9xd8; OWsBM: return ajaxSuccess("\346\223\x8d\xe4\xbd\x9c\346\210\x90\345\212\237"); goto nUDo6; JfwkR: $settingModel->clearCache(); goto f4dyY; nUDo6: KrHMy: goto uAK6w; lKb9X: unlink($filePath); goto M9EIj; dkoKY: x5aZw: goto sH9Cy; Zg67O: if (!(!is_array($data) || !isset($data["\x74\171\x70\145"]) || $data["\164\171\x70\x65"] != "\163\x65\164\x74\151\x6e\x67" || !isset($data["\x76\145\162\x69\146\x79"]) || !isset($data["\144\x61\x74\141"]))) { goto t30Kc; } goto rrlFc; YVHPr: Log::error("\146\x69\x6c\145\x3a\x20{$filePath}\x2c\40\146\141\x69\154\145\x64\40\164\x6f\40\x64\145\143\162\171\160\x74\40\151\x74"); goto lKb9X; uAK6w: } public function dbBackups() { goto ywmJW; sy0eV: hficZ: goto cL4Xs; rKAqu: $backupDate = explode("\43", $slices[0])[1]; goto Qra3H; eCrHm: loXNv: goto o9su5; xvD1y: L_tup: goto MejE_; uqZwG: goto hficZ; goto SXM04; N6O_Q: BBVgK: goto SvEJX; bcB7f: if (!($file != "\56" && $file != "\56\56")) { goto loXNv; } goto k6p82; k6p82: $slices = explode("\56", $file); goto Se56c; o9su5: goto hficZ; goto xvD1y; YRSPI: array_multisort($backupFiles, SORT_DESC, $dates); goto wJf61; ywmJW: if (!$this->request->isGet()) { goto xE2OI; } goto C6oBm; ro9pd: z1UUH: goto Z9SUi; c97Lq: $backupPath = ROOT_PATH . "\144\142" . DS . "\x62\x61\143\x6b\165\x70"; goto gNFMl; wJf61: return json($backupFiles); goto KGsDK; JqOm4: if (!(is_dir($backupPath) && file_exists($backupPath) && ($handle = opendir($backupPath)))) { goto BBVgK; } goto sy0eV; TuZJY: xE2OI: goto c97Lq; Qra3H: $backupFiles[] = ["\156\x61\155\145" => $file, "\x64\141\164\x65" => date("\x59\55\x6d\55\x64", strtotime($backupDate))]; goto eCrHm; SvEJX: $dates = array_column($backupFiles, "\x64\141\x74\x65"); goto YRSPI; MejE_: closedir($handle); goto N6O_Q; cL4Xs: if (!(false !== ($file = readdir($handle)))) { goto L_tup; } goto bcB7f; SXM04: tpFDS: goto rKAqu; Se56c: if (!(false === strpos($slices[0], "\43"))) { goto tpFDS; } goto uqZwG; Z9SUi: $backupFiles = []; goto JqOm4; KAcrO: $backupPath = systemSetting("\104\x42\137\102\101\x43\x4b\x55\120\137\x50\x41\124\x48"); goto ro9pd; C6oBm: return $this->fetch(); goto TuZJY; gNFMl: if (!systemSetting("\x44\x42\x5f\x42\101\x43\x4b\x55\120\x5f\x50\101\x54\x48")) { goto z1UUH; } goto KAcrO; KGsDK: } }
