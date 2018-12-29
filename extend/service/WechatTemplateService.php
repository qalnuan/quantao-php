<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2018/01/06
 */

namespace service;

use app\wap\model\user\WechatUser;
use think\Db;

class WechatTemplateService
{
    /**
     * 主营行业：IT科技 互联网|电子商务
     * 副营行业：IT科技 IT软件与服务
     */

    //订单生成通知
    const ORDER_CREATE = 'YaskX30OA83KwZczKmp4srSrVvr5cKOKZ5u-C98Back';

    //订单支付成功
    const ORDER_PAY_SUCCESS = 'on6N5LoKwgQ1y7Z0KsUyrq3DZt4gxBerml3tM5jrp_E';

    //订单发货提醒(快递)
    const ORDER_POSTAGE_SUCCESS = 'CTzsKBHnyaMYL7zCqjuXFsUmrO5jB-Rp_-awryxLalQ';

    //订单发货提醒(送货)
    const ORDER_DELIVER_SUCCESS = 'hC9PFuxOKq6u5kNZyl6VdHGgAuA6h5I3ztpuDk1ioAk';

    //订单收货通知
    const ORDER_TAKE_SUCCESS = 'booE7nSZ-7zOGpYAJj6RdgSODZ7ZvUPaAYuk6JFtCyw';

    //退款进度通知
    const ORDER_REFUND_STATUS = 'QWc2PYbZZAd4JNloOfjdXyPl9d1EIefH5GOtJXUKL64';

    //帐户资金变动提醒
    const USER_BALANCE_CHANGE = 'LiZWDICBbmllH1SND-fxrkwvFhzgyVPZi62I8fXmi-U';

    //客服通知提醒
    const SERVICE_NOTICE = 'asQ_qAjpfMoKaTuXOua-pHEpzasOcSytIRrk7thQDHM';

    //服务进度提醒
    const ADMIN_NOTICE = 'WZgIZrj4Fkakozt4x2fvvYnF26UAaFQOAFHBVJDSqeQ';

    //拼团成功通知
    const ORDER_USER_GROUPS_SUCCESS = 'SE9gjavWbF-MZ93wq7LmxW8uiZ20-tuhjmEjJMTiO24';

    //拼团失败通知
    const ORDER_USER_GROUPS_LOSE   = 'Z3QVa8l_4y18HQY56ELY7QwpTz-yLAeL_VKtgS4mvcE';

    public static function sendTemplate($openid,$templateId,array $data,$url = null,$defaultColor = '')
    {
        $isSend = Db::name('WechatTemplate')->where('tempid',$templateId)->where('status',1)->count();
        if(!$isSend) return false;
        try{
            return WechatService::sendTemplate($openid,$templateId,$data,$url,$defaultColor);
        }catch (\Exception $e){
            return false;
        }
    }

    public static function sendAdminNoticeTemplate(array $data,$url = null,$defaultColor = '')
    {
        $adminIds = SystemConfigService::get('site_store_admin_uids');
        if(!$adminIds || !($adminList = array_unique(array_filter(explode(',',trim($adminIds)))))) return false;
        foreach ($adminList as $uid){
            try{
                $openid = WechatUser::uidToOpenid($uid);
            }catch (\Exception $e){
                continue;
            }
            self::sendTemplate($openid,self::ADMIN_NOTICE,$data,$url,$defaultColor);
        }
    }

    /**
     * 返回所有支持的行业列表
     * @return \EasyWeChat\Support\Collection
     */
    public static function getIndustry()
    {
        return WechatService::noticeService()->getIndustry();
    }

    /**
     * 修改账号所属行业
     * 主行业	副行业	代码
     * IT科技	互联网/电子商务	1
     * IT科技	IT软件与服务	2
     * IT科技	IT硬件与设备	3
     * IT科技	电子技术	4
     * IT科技	通信与运营商	5
     * IT科技	网络游戏	6
     * 金融业	银行	7
     * 金融业	基金|理财|信托	8
     * 金融业	保险	9
     * 餐饮	餐饮	10
     * 酒店旅游	酒店	11
     * 酒店旅游	旅游	12
     * 运输与仓储	快递	13
     * 运输与仓储	物流	14
     * 运输与仓储	仓储	15
     * 教育	培训	16
     * 教育	院校	17
     * 政府与公共事业	学术科研	18
     * 政府与公共事业	交警	19
     * 政府与公共事业	博物馆	20
     * 政府与公共事业	公共事业|非盈利机构	21
     * 医药护理	医药医疗	22
     * 医药护理	护理美容	23
     * 医药护理	保健与卫生	24
     * 交通工具	汽车相关	25
     * 交通工具	摩托车相关	26
     * 交通工具	火车相关	27
     * 交通工具	飞机相关	28
     * 房地产	建筑	29
     * 房地产	物业	30
     * 消费品	消费品	31
     * 商业服务	法律	32
     * 商业服务	会展	33
     * 商业服务	中介服务	34
     * 商业服务	认证	35
     * 商业服务	审计	36
     * 文体娱乐	传媒	37
     * 文体娱乐	体育	38
     * 文体娱乐	娱乐休闲	39
     * 印刷	印刷	40
     * 其它	其它	41
     * @param $industryId1
     * @param $industryId2
     * @return \EasyWeChat\Support\Collection
     */
    public static function setIndustry($industryId1, $industryId2)
    {
        return WechatService::noticeService()->setIndustry($industryId1, $industryId2);
    }

    /**
     * 获取所有模板列表
     * @return \EasyWeChat\Support\Collection
     */
    public static function getPrivateTemplates()
    {
        return WechatService::noticeService()->getPrivateTemplates();
    }

    /**
     * 删除指定ID的模板
     * @param $templateId
     * @return \EasyWeChat\Support\Collection
     */
    public static function deletePrivateTemplate($templateId)
    {
        return WechatService::noticeService()->deletePrivateTemplate($templateId);
    }


    /**
     * 添加模板并获取模板ID
     * @param $shortId
     * @return \EasyWeChat\Support\Collection
     */
    public static function addTemplate($shortId)
    {
        return WechatService::noticeService()->addTemplate($shortId);
    }
}