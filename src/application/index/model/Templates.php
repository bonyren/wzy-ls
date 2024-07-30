<?php
// +----------------------------------------------------------------------
// | WZYCODING [ SIMPLE SOFTWARE IS THE BEST ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2025 wzycoding All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://license.coscl.org.cn/MulanPSL2 )
// +----------------------------------------------------------------------
// | Author: wzycoding <wzycoding@qq.com>
// +----------------------------------------------------------------------
namespace app\index\model;
use think\Model;
use think\Log;
use think\Debug;

class Templates extends Model{
    protected $pk = 'template_id';
    protected $table = 'templates';
    /**
     * 是否系統模板
     */
    const eTemplateSystemYes = 1;
    const eTemplateSystemNo = 2;
    public static $eTemplateSystemDefs = [
        self::eTemplateSystemYes=>'yes',
        self::eTemplateSystemNo=>'no'
    ];
    /**
     * 是否默认模板
     */
    const eTemplateDefault = 1;
    const eTemplateUndefault = 2;
    public static $templateDefaultDefs = [
        self::eTemplateDefault=>'Default',
        self::eTemplateUndefault=>'Undefault'
    ];

    public static $defaultTemplate = '
                    <div style="margin:0; text-align:center; width:100%; background:#EEE;min-width:240px;height:100%;">&nbsp;
                    <div style="width:96%;margin:0 auto; border-top:6px solid #369;border-bottom: 6px solid #369;background:#DEF;">
                    <h3 style="margin-top:5px;background-color:#69C; font-weight:normal; color:#FFF; text-align:center; margin-bottom:5px; padding:10px; line-height:1.2; font-size:21px; text-transform:capitalize;">[SUBJECT]</h3>

                    <div style="text-align:justify;background:#FFF;padding:20px; border-top:2px solid #369;min-height:200px;font-size:13px; border-bottom:2px solid #369;">[CONTENT]
                    <div style="clear:both">&nbsp;</div>
                    </div>

                    <div style="clear:both;background:#69C;font-weight:normal; padding:10px;color:#FFF;text-align:center;font-size:11px;margin:5px 0px">[FOOTER]<br />
                    [SIGNATURE]</div>
                    </div>
                    </div>';
}