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
namespace app\index\logic;
use think\Db;
use think\Log;
use think\Debug;
use app\index\model\Templates as TemplatesModel;

class Templates extends Base{
    public function load($search=array(),
                            $page=1,
                            $rows=DEFAULT_PAGE_ROWS,
                            $sort = '',
                            $order = ''){
        /////////////////////////////////////////////////
        if($sort == 'template_id'){
            $order = 'template_id ' . $order;
        }else{
            $order = 'template_id desc';
        }
        /////////////////////////////////////////////////
        $conditions = [];
        $templates = Db::table('templates')->where($conditions)
            ->page($page, $rows)
            ->order($order)
            ->field(true)
            ->select();
        return $templates;
    }
    public function getTemplateList(){
        $templates = Db::table('templates')->field(true)->select();
        return $templates;
    }
    public function getTemplate($templateId){
        $template = Db::table('templates')
            ->where('template_id', $templateId)
            ->field(true)
            ->find();
        return $template;
    }
    /********************************************************************************/
    public function addTemplate($title, $template){
        $defaultCount = Db::table('templates')->where('default', TemplatesModel::eTemplateDefault)->count();
        $default = TemplatesModel::eTemplateUndefault;
        if($defaultCount == 0){
            $default = TemplatesModel::eTemplateDefault;
        }

        $systemCount = Db::table('templates')->where('system', TemplatesModel::eTemplateSystemYes)->count();
        $system = TemplatesModel::eTemplateSystemNo;
        if($systemCount == 0){
            $system = TemplatesModel::eTemplateSystemYes;
        }

        Db::table('templates')->insert([
            'title'=>$title,
            'template'=>$template,
            'default'=>$default,
            'system'=>$system
        ]);
        return true;
    }
    public function editTemplate($templateId, $title, $template){
        Db::table('templates')->where('template_id', $templateId)->update([
            'title'=>$title,
            'template'=>$template
        ]);
        return true;
    }
    public function deleteTemplate($templateId){
        $template = Db::table('templates')->where('template_id', $templateId)->find();
        if(!$template){
            exception('无法找到要删除模板');
        }
        //系统模板，默认模板不能删除
        if($template['system'] == TemplatesModel::eTemplateSystemYes){
            exception('系统模板，不能删除');
        }
        if($template['default'] == TemplatesModel::eTemplateDefault){
            exception('默认模板，不能删除');
        }
        //已经和投递活动关联的模板，不能删除
        $campaignCount = Db::table('campaigns')->where('template_id', $templateId)->count();
        if($campaignCount){
            exception('已关联投递活动，不能删除');
        }

        Db::table('campaigns')->where('template_id', $templateId)->update(['template_id'=>0]);
        Db::table('templates')->where('template_id', $templateId)->delete();
        return true;
    }
    /********************************************************************************/
    public function getDefaultTemplateId(){
        $templateId = Db::table('templates')->where('default', TemplatesModel::eTemplateDefault)
            ->value('template_id');
        return $templateId;
    }
    public function getSystemTemplate(){
        $templateInfos = Db::table('templates')
            ->where('system', TemplatesModel::eTemplateSystemYes)
            ->field(true)
            ->find();
        return $templateInfos;
    }
    public function getDefaultTemplate(){
        $templateInfos = Db::table('templates')
            ->where('default', TemplatesModel::eTemplateDefault)
            ->field(true)
            ->find();
        return $templateInfos;
    }
    /********************************************************************************/
    public function makeDefaultTemplate($templateId){
        Db::table('templates')->where('template_id', '<>', $templateId)->update([
            'default' => TemplatesModel::eTemplateUndefault
        ]);
        Db::table('templates')->where('template_id', $templateId)->update([
            'default' => TemplatesModel::eTemplateDefault
        ]);
        return true;
    }
    public function makeSystemTemplate($templateId){
        Db::table('templates')->where('template_id', '<>', $templateId)->update([
            'system' => TemplatesModel::eTemplateSystemNo
        ]);
        Db::table('templates')->where('template_id', $templateId)->update([
            'system' => TemplatesModel::eTemplateSystemYes
        ]);
        return true;
    }
}