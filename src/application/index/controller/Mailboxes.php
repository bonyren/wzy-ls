<?php
 namespace app\index\controller; use app\index\Defs as IndexDefs; use think\Controller; use think\Exception; use think\Log; use think\Debug; use think\Request; use PHPMailer\PHPMailer\PHPMailer; use app\index\logic\Mailboxes as MailboxesLogic; use app\index\model\Mailboxes as MailboxesModel; class Mailboxes extends Common { public function mailboxes($search = array(), $page = 1, $rows = DEFAULT_PAGE_ROWS, $sort = '', $order = '') { goto ZBE1i; ZBE1i: if (!request()->isGet()) { goto nE18l; } goto S9RJH; lAOK_: $mailboxesLogic = MailboxesLogic::I(); goto krE3R; BGizm: $this->assign("\x75\162\154\110\x72\145\146\163", $urlHrefs); goto Nywdp; TCrx0: nE18l: goto lAOK_; Nywdp: return $this->fetch(); goto TCrx0; S9RJH: $urlHrefs = ["\155\141\x69\154\x62\157\170\145\x73" => url("\x69\156\x64\145\170\x2f\x4d\141\x69\154\x62\157\x78\x65\163\x2f\155\x61\x69\154\142\x6f\x78\145\163"), "\155\x61\151\x6c\x62\157\170\x65\x73\x41\x64\144" => url("\x69\156\x64\x65\170\x2f\x4d\141\x69\x6c\142\157\170\x65\163\x2f\155\141\151\x6c\142\x6f\170\x65\163\101\x64\144"), "\155\141\151\x6c\x62\157\x78\x65\163\x45\144\151\x74" => url("\x69\x6e\144\x65\170\57\115\x61\151\x6c\142\x6f\170\x65\163\57\155\x61\151\154\142\x6f\x78\145\x73\105\x64\x69\164"), "\x6d\141\x69\154\x62\x6f\170\x65\163\x44\145\x6c\145\164\145" => url("\151\x6e\x64\x65\x78\57\115\141\x69\x6c\142\157\170\145\163\57\155\141\151\x6c\x62\157\x78\145\163\x44\x65\154\145\x74\145"), "\155\141\151\x6c\142\x6f\170\145\x73\x4d\141\x6b\x65\x44\x65\x66\141\x75\x6c\164" => url("\x69\156\144\x65\x78\57\x4d\141\151\154\x62\157\170\145\163\x2f\x6d\x61\151\x6c\142\x6f\170\x65\x73\x4d\141\x6b\145\x44\x65\x66\141\x75\154\x74"), "\x6d\x61\x69\x6c\x62\157\170\145\x73\x4d\141\x6b\x65\x53\x79\163\x74\145\155" => url("\151\156\144\x65\170\x2f\115\x61\151\x6c\x62\157\x78\145\x73\x2f\x6d\141\x69\154\x62\157\x78\x65\163\x4d\x61\153\145\x53\171\163\164\145\x6d"), "\x6d\x61\x69\x6c\x62\x6f\170\145\x73\124\145\163\164\x4f\165\164\147\157\151\x6e\147\x53\145\x72\166\x65\x72" => url("\151\x6e\144\x65\170\x2f\x4d\x61\151\154\x62\x6f\170\145\163\57\x6d\x61\x69\x6c\x62\x6f\170\145\x73\124\145\163\x74\x4f\x75\164\x67\157\x69\x6e\x67\x53\145\162\166\145\x72"), "\x6d\141\151\154\x62\x6f\x78\x65\163\124\145\163\x74\111\x6e\x63\157\x6d\151\156\x67\123\x65\162\x76\145\x72" => url("\151\156\144\x65\170\x2f\115\141\151\x6c\142\x6f\x78\145\163\x2f\155\x61\151\x6c\x62\x6f\x78\x65\163\x54\145\163\164\x49\156\143\157\x6d\151\x6e\147\123\x65\162\166\x65\162"), "\155\141\x69\x6c\142\x6f\170\x65\163\x45\x78\x70\x6f\x72\x74" => url("\151\x6e\144\x65\x78\x2f\115\x61\x69\x6c\142\x6f\170\145\163\x2f\145\x78\x70\157\162\164"), "\155\x61\151\x6c\142\x6f\x78\145\x73\111\155\160\157\x72\x74" => url("\x69\x6e\144\145\170\x2f\x4d\141\151\x6c\x62\x6f\x78\x65\163\57\151\x6d\160\x6f\162\x74")]; goto BGizm; krE3R: return json($mailboxesLogic->load($search, $page, $rows, $sort, $order)); goto xUs2y; xUs2y: } public function mailboxesComboboxDatas() { $mailboxesLogic = MailboxesLogic::I(); return json($mailboxesLogic->loadComboboxDatas()); } public function mailboxesAdd() { goto LyO2W; tU_a4: jgnGb: goto jLwWE; dO80Z: goto MuIF9; goto mL1bF; kcidh: return ajaxError("\xe6\223\x8d\344\xbd\x9c\345\244\261\350\264\xa5"); goto dO80Z; ezIG_: if ($result) { goto yXpfU; } goto kcidh; ehZg6: $mailboxesLogic = MailboxesLogic::I(); goto wgS0n; qsAjB: MuIF9: goto h5ne6; wgS0n: $result = $mailboxesLogic->addMailbox($infos); goto ezIG_; cDasy: return ajaxSuccess("\xe6\223\215\344\xbd\x9c\346\210\220\xe5\x8a\x9f"); goto qsAjB; eFr9Q: $urlHrefs = ["\x6d\141\x69\154\x62\157\170\145\163\x54\145\x73\164\x4f\x75\x74\x67\x6f\151\x6e\x67\x53\145\162\166\145\x72" => url("\151\156\x64\145\x78\x2f\x4d\x61\151\154\142\x6f\170\x65\x73\x2f\x6d\x61\x69\x6c\x62\157\x78\x65\x73\124\x65\163\164\x4f\165\x74\147\157\x69\156\147\x53\x65\x72\166\x65\162"), "\155\x61\151\154\x62\157\170\145\163\124\145\163\x74\111\x6e\x63\157\x6d\151\156\x67\x53\145\162\166\145\x72" => url("\151\x6e\x64\x65\170\57\x4d\141\151\x6c\142\157\170\145\163\57\x6d\141\151\x6c\x62\x6f\170\x65\163\x54\x65\163\x74\111\x6e\x63\157\x6d\151\x6e\147\x53\145\x72\166\x65\x72")]; goto CFoND; CFoND: $this->assign("\x75\162\x6c\x48\162\145\146\163", $urlHrefs); goto Bsw00; Bsw00: return $this->fetch(); goto tU_a4; jLwWE: $infos = input("\160\x6f\163\x74\x2e\151\156\x66\157\x73\x2f\x61"); goto ehZg6; mL1bF: yXpfU: goto cDasy; LyO2W: if (!request()->isGet()) { goto jgnGb; } goto eFr9Q; h5ne6: } public function mailboxesEdit($mailboxId) { goto x4Vlo; i0mqW: $infos = $mailboxesLogic->getMailboxInfos($mailboxId); goto HpmYw; EvbMX: $this->assign("\142\x69\x6e\144\126\141\x6c\165\145\x73", $bindValues); goto U_G0P; T7t2M: try { $mailboxesLogic->editMailbox($mailboxId, $infos); return ajaxSuccess("\346\x93\215\xe4\275\234\346\x88\220\xe5\212\x9f"); } catch (\Exception $e) { return ajaxError($e->getMessage()); } goto pKqx5; teH23: if (!request()->isGet()) { goto sbm1E; } goto i0mqW; pobzW: $bindValues = ["\x69\156\146\157\163" => $infos]; goto EvbMX; U_G0P: $urlHrefs = ["\155\141\x69\x6c\x62\x6f\170\145\x73\x54\145\163\164\117\165\x74\x67\x6f\151\156\147\123\145\162\x76\145\162" => url("\x69\156\x64\x65\x78\x2f\x4d\141\x69\x6c\142\157\x78\x65\163\x2f\x6d\x61\151\x6c\x62\157\170\145\x73\124\145\x73\x74\x4f\x75\164\147\x6f\x69\156\147\123\x65\162\x76\x65\x72"), "\155\x61\x69\x6c\x62\157\x78\145\x73\x54\145\x73\164\111\x6e\x63\157\x6d\151\x6e\147\123\145\x72\166\145\162" => url("\151\x6e\x64\145\170\x2f\115\141\151\154\x62\157\x78\x65\x73\x2f\x6d\141\x69\x6c\142\x6f\170\145\163\124\x65\x73\x74\111\x6e\143\x6f\155\151\156\147\123\145\162\x76\x65\x72")]; goto Z1LaL; HpmYw: if ($infos) { goto dgQcA; } goto J2I8R; aD9wD: $infos = input("\160\157\163\164\56\x69\156\146\157\x73\x2f\x61"); goto T7t2M; ZwnH6: dgQcA: goto pobzW; x4Vlo: $mailboxesLogic = MailboxesLogic::I(); goto teH23; b0Nkl: return $this->fetch(); goto HhyIA; J2I8R: return $this->fetch("\143\x6f\155\155\x6f\x6e\x2f\x65\162\x72\x6f\x72"); goto ZwnH6; Z1LaL: $this->assign("\165\162\154\110\162\x65\146\163", $urlHrefs); goto b0Nkl; HhyIA: sbm1E: goto aD9wD; pKqx5: } public function mailboxesDelete($mailboxId) { $mailboxesLogic = MailboxesLogic::I(); try { $mailboxesLogic->deleteMailbox($mailboxId); return ajaxSuccess("\346\223\x8d\344\275\234\346\210\220\xe5\x8a\x9f"); } catch (\Exception $e) { return ajaxError($e->getMessage()); } } public function mailboxesMakeDefault($mailboxId) { goto aTwrW; aTwrW: $mailboxesLogic = MailboxesLogic::I(); goto lP9_b; lP9_b: $mailboxesLogic->makeDefaultMailbox($mailboxId); goto eMTFt; eMTFt: return ajaxSuccess("\346\223\215\344\xbd\234\xe6\210\x90\345\212\237"); goto cPW9g; cPW9g: } public function mailboxesMakeSystem($mailboxId) { goto eO5QL; eO5QL: $mailboxesLogic = MailboxesLogic::I(); goto egX7c; egX7c: $mailboxesLogic->makeSystemMailbox($mailboxId); goto AVVBV; AVVBV: return ajaxSuccess("\xe6\x93\x8d\344\275\x9c\346\x88\x90\345\212\x9f"); goto uSMHT; uSMHT: } public function mailboxesTestOutgoingServer() { goto wukUG; JVHQp: $password = $mailbox["\160\x61\163\x73\x77\157\162\x64"]; goto evstg; e_tnW: $smtpSecure = input("\x70\157\163\x74\56\x73\x6d\x74\160\x5f\x73\145\143\x75\162\x65\57\x64"); goto gg88v; m2Dav: $mailbox = $mailboxesLogic->getMailboxInfos($mailboxId); goto wtzD1; l8Tlm: return ajaxSuccess("\xe6\x93\x8d\344\xbd\234\346\x88\220\xe5\212\237"); goto pX76Y; j8Jzv: ZaQjG: goto Jz5X6; atc78: $mailboxesLogic = MailboxesLogic::I(); goto m2Dav; Ewfi_: $smtpSecure = $mailbox["\x73\155\164\160\137\x73\145\x63\165\x72\x65"]; goto ApD_j; wukUG: $smtpHost = input("\160\x6f\163\164\56\x73\x6d\x74\x70\137\x68\x6f\x73\164\x2f\163"); goto SGmOj; Jz5X6: $smtpHost = $mailbox["\163\x6d\164\160\x5f\150\157\163\164"]; goto ISIZT; x_E9O: return ajaxError("\xe6\x97\xa0\346\263\x95\xe6\x89\xbe\345\x88\260\350\xaf\xa5\xe9\202\xae\xe7\256\xb1"); goto j8Jzv; bxKNr: $password = input("\x70\157\163\x74\56\160\141\163\163\167\157\x72\144\57\163"); goto Q7Sub; ApD_j: $account = $mailbox["\x61\x63\143\157\x75\156\x74"]; goto JVHQp; QB8Qn: try { goto A4iml; A4iml: $mail = new PHPMailer(true); goto jRS2l; q3XSv: goto ushaM; goto C7eJP; pe04K: $mail->SMTPSecure = ''; goto hF2Vw; lB8wK: if ($validCredentials) { goto DiGu1; } goto eulCW; lZpxU: if ($smtpSecure != MailboxesModel::eMailboxSecureFalse) { goto qRbTq; } goto pe04K; jRS2l: $mail->SMTPAuth = true; goto Nl6zQ; eulCW: return ajaxError("\xe6\x93\x8d\344\xbd\x9c\345\244\261\xe8\xb4\245"); goto rce3Z; C7eJP: qRbTq: goto NK2Zl; NK2Zl: $mail->SMTPSecure = MailboxesModel::$eMailboxSecureDefs[$smtpSecure]; goto GLht0; LIu7T: $mail->Host = $smtpHost; goto Bu5Kk; fSdm0: DiGu1: goto kgXXM; UhCmA: $mail->Password = $password; goto LIu7T; hF2Vw: $mail->SMTPAutoTLS = false; goto q3XSv; xsulo: return ajaxSuccess("\346\223\215\xe4\275\234\xe6\x88\220\xe5\212\237"); goto hSKNA; zuUj7: $validCredentials = $mail->SmtpConnect(); goto lB8wK; Bu5Kk: $mail->Port = $smptPort; goto lZpxU; GLht0: ushaM: goto zuUj7; kgXXM: $mail->smtpClose(); goto xsulo; rce3Z: goto yXx9X; goto fSdm0; hSKNA: yXx9X: goto A2dgb; Nl6zQ: $mail->Username = $account; goto UhCmA; A2dgb: } catch (\Exception $e) { return ajaxError("\346\223\x8d\xe4\xbd\x9c\xe5\244\xb1\350\xb4\245"); } goto l8Tlm; Q7Sub: $mailboxId = input("\160\x6f\x73\x74\x2e\x6d\141\151\154\142\x6f\x78\x49\x64\57\x64"); goto pQnyP; pQnyP: if (!$mailboxId) { goto tsICb; } goto atc78; evstg: tsICb: goto QB8Qn; gg88v: $account = input("\160\157\x73\164\56\x61\x63\143\157\165\156\164\57\x73"); goto bxKNr; ISIZT: $smptPort = $mailbox["\163\x6d\164\160\137\x70\x6f\162\x74"]; goto Ewfi_; SGmOj: $smptPort = input("\x70\x6f\163\x74\x2e\x73\155\164\160\137\160\x6f\x72\164\57\144"); goto e_tnW; wtzD1: if ($mailbox) { goto ZaQjG; } goto x_E9O; pX76Y: } public function mailboxesTestIncomingServer() { goto qpiBZ; Zztpg: $account = input("\160\157\x73\x74\56\x61\143\x63\157\165\x6e\164\x2f\163"); goto fVxEM; X0RFy: Z6BwT: goto WBsT_; Vwf35: if (!($bounceSecure == MailboxesModel::eMailboxSecureSSL)) { goto yZmrl; } goto cVUq3; FGWF0: goto ER6nF; goto o3MsG; uEJ71: ukVc8: goto ZKTBe; Ktspc: return ajaxSuccess("\346\223\215\xe4\275\234\xe6\x88\x90\xe5\212\237"); goto X0RFy; o3MsG: zNiRi: goto BvjFE; ZcM3v: try { goto gMZ9w; gMZ9w: if (!function_exists(imap_open)) { exception("\x69\155\x61\x70\137\157\160\x65\156\xe6\234\xaa\xe5\xae\232\344\271\211"); } goto lok92; nJa_A: l3Zhi: goto zCZUk; CKNJH: return ajaxError("\151\x6d\141\160\x5f\x6f\160\145\156\xe6\x93\215\xe4\275\x9c\345\244\261\xe8\xb4\245"); goto KCLVa; zCZUk: imap_close($link); goto uClvO; T6n5X: imap_close($link); goto wrutP; wrutP: return ajaxError("\x69\x6d\x61\160\137\160\151\156\x67\xe6\x93\x8d\xe4\xbd\x9c\345\xa4\261\xe8\264\245"); goto nJa_A; KCLVa: hV5CQ: goto SroE9; lok92: $link = imap_open("\173" . $bounceHost . "\72" . $bouncePort . $flag . "\x7d\x49\116\x42\117\130", $account, $password); goto lyEnI; lyEnI: if ($link) { goto hV5CQ; } goto CKNJH; uClvO: $link = null; goto fbF73; SroE9: if (imap_ping($link)) { goto l3Zhi; } goto T6n5X; fbF73: } catch (\Exception $e) { goto TEWx8; GqXTB: a1hgM: goto IuL3I; QxcFm: $link = null; goto GqXTB; IuL3I: return ajaxError("\145\x78\x63\145\160\x74\x69\157\x6e\x2d\346\x93\x8d\344\xbd\234\xe5\244\xb1\xe8\xb4\xa5"); goto eX6mY; TEWx8: if (!isset($link)) { goto a1hgM; } goto tn4F4; tn4F4: imap_close($link); goto QxcFm; eX6mY: } goto Ktspc; vqPws: $flag .= "\57\163\x73\x6c\x2f\x6e\x6f\x76\x61\x6c\151\x64\141\x74\x65\x2d\143\145\x72\164"; goto uEJ71; n3g50: $flag = "\x2f\160\157\x70\63"; goto Vwf35; fVxEM: $password = input("\x70\x6f\x73\x74\56\x70\x61\163\x73\x77\x6f\x72\144\57\x73"); goto ANqz3; kYFHL: $bounceHost = $mailbox["\142\157\165\156\x63\145\x5f\150\157\x73\x74"]; goto aKQh_; hwz7m: ER6nF: goto Usq0Y; O3yQE: if (!($bounceSecure == MailboxesModel::eMailboxSecureSSL)) { goto ukVc8; } goto vqPws; s5Eq1: return ajaxError("\xe6\223\x8d\344\275\234\345\244\xb1\350\264\xa5"); goto FGWF0; KWDyx: if ($mailbox) { goto bXYsi; } goto q5B66; U0W4W: NLWBV: goto FSHMh; cVUq3: $flag .= "\x2f\163\x73\x6c\x2f\156\x6f\166\x61\x6c\151\144\x61\x74\x65\55\x63\145\x72\x74"; goto OrzuQ; gqccM: $bouncePort = input("\x70\157\163\164\56\142\x6f\x75\156\x63\x65\137\160\x6f\x72\x74\57\144"); goto O_qNw; AuEey: $mailbox = $mailboxesLogic->getMailboxInfos($mailboxId); goto KWDyx; OrzuQ: yZmrl: goto ZcM3v; YZFDz: if (!$mailboxId) { goto NLWBV; } goto u8DlN; ANqz3: $mailboxId = input("\x70\157\163\x74\56\155\141\x69\x6c\142\157\170\111\144\x2f\144"); goto YZFDz; Usq0Y: goto Z6BwT; goto gpFWn; Le067: $account = $mailbox["\x61\143\143\x6f\x75\156\164"]; goto IGfOc; IGfOc: $password = $mailbox["\x70\141\x73\163\167\157\162\x64"]; goto U0W4W; q5B66: return ajaxError("\xe6\x97\xa0\346\263\x95\xe6\x89\276\xe5\x88\260\350\xaf\245\351\202\256\xe7\xae\xb1"); goto akoMk; BvjFE: $flag = ''; goto O3yQE; iPZPQ: if ($bounceProtocol == MailboxesModel::eMailboxProtocoImap) { goto zNiRi; } goto s5Eq1; akoMk: bXYsi: goto kYFHL; gpFWn: PaYJT: goto n3g50; u8DlN: $mailboxesLogic = MailboxesLogic::I(); goto AuEey; fD0i_: $bounceProtocol = $mailbox["\x62\157\x75\x6e\x63\145\137\x70\x72\x6f\164\x6f\143\x6f\154"]; goto Le067; FSHMh: if ($bounceProtocol == MailboxesModel::eMailboxProtocolPop) { goto PaYJT; } goto iPZPQ; qpiBZ: $bounceHost = input("\x70\x6f\x73\x74\x2e\x62\x6f\x75\156\143\x65\x5f\x68\157\163\164\x2f\x73"); goto gqccM; aKQh_: $bouncePort = $mailbox["\x62\x6f\165\156\143\x65\x5f\160\x6f\x72\164"]; goto lyl8b; F81YE: $bounceProtocol = input("\x70\157\163\164\56\142\x6f\x75\x6e\x63\145\x5f\x70\x72\x6f\164\157\x63\157\154\57\144"); goto Zztpg; ZKTBe: try { goto vIc_j; Mja3o: if ($link) { goto NWJH3; } goto F1p_U; ytgD6: if (imap_ping($link)) { goto C05vZ; } goto m3CdR; VHs8l: imap_close($link); goto u2V5L; vIc_j: $link = imap_open("\173" . $bounceHost . "\x3a" . $bouncePort . $flag . "\175\111\x4e\102\x4f\x58", $account, $password); goto Mja3o; ifNSx: return ajaxError("\xe6\223\215\xe4\xbd\x9c\xe5\244\xb1\350\264\xa5"); goto rG1Ow; u2V5L: $link = null; goto l6Tnj; E_IYU: NWJH3: goto ytgD6; m3CdR: imap_close($link); goto ifNSx; rG1Ow: C05vZ: goto VHs8l; F1p_U: return ajaxError("\346\x93\215\xe4\xbd\234\345\244\xb1\xe8\xb4\xa5"); goto E_IYU; l6Tnj: } catch (\Exception $e) { goto zuULU; zuULU: if (!isset($link)) { goto E2mGo; } goto bYpPY; F41Ht: $link = null; goto Smjin; bYpPY: imap_close($link); goto F41Ht; Smjin: E2mGo: goto phwKu; phwKu: return ajaxError("\346\x93\x8d\xe4\275\x9c\xe5\244\261\350\264\xa5"); goto xqIXr; xqIXr: } goto th00D; lyl8b: $bounceSecure = $mailbox["\x62\157\165\156\143\145\x5f\x73\x65\x63\x75\162\145"]; goto fD0i_; th00D: return ajaxSuccess("\xe6\x93\215\344\xbd\234\xe6\x88\220\xe5\x8a\x9f"); goto hwz7m; O_qNw: $bounceSecure = input("\160\157\x73\x74\56\142\x6f\x75\156\x63\145\137\x73\x65\143\x75\x72\x65\x2f\144"); goto F81YE; WBsT_: } public function export() { $mailboxesLogic = MailboxesLogic::I(); $mailboxesLogic->export(); } public function import($saveName) { $mailboxesLogic = MailboxesLogic::I(); try { $mailboxesLogic->import($saveName); return ajaxSuccess("\xe6\x93\x8d\xe4\xbd\x9c\346\210\220\xe5\212\237"); } catch (\Exception $e) { return ajaxError("\xe6\223\215\xe4\xbd\234\xe5\xa4\261\xe8\264\245\x20\x2d\40" . $e->getMessage()); } } }