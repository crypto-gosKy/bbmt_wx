<?php
/**
 * @author: peanut
 * @date: 2016-04-19
 * @time: 09:33
 */
namespace Api\Controller;

use Api\Logic\WechatQrcodeLogic;
use Api\Logic\WechatReplyLogic;
use Api\Logic\WechatUserLogic;
use Api\Model\WechatUserModel;
use Think\Controller;
use Vendor\Wechat\Wechat;

class WechatController extends WechatBaseController
{
    protected $revData;
    protected $openId;

    public function index()
    {
//        $this->weChatObj = new Wechat('');
        $this->log('index begin');

        //回调请求日志
//        $this->weChatLog();\

        $type = $this->weChatObj->getRev()->getRevType();
        $content = $this->weChatObj->getRev()->getRevContent();
        $this->revData = $this->weChatObj->getRev()->getRevData();
        if (!empty($this->revData)) {
            $this->openId = $this->revData['FromUserName'];
//            if (!isset($_SESSION['BMT_OPENID'])) {
//                session('BMT_OPENID', $this->openId);
//            }
        }
        $this->log('rev data:'.var_export($this->revData,true));
        $this->log('init data end');
        switch ($type) {
            case Wechat::MSGTYPE_EVENT:
                $this->log('type event');
//                $this->weChatObj =new Wechat('');
//                $event = $this->weChatObj->getRev()->getRevEvent();
                $this->eventDispatch($this->revData['Event']);
                break;
            case Wechat::MSGTYPE_TEXT:
            default:
                $this->log('type text');
                $this->keywordReply($content);
                break;
        }
        $this->log('init end');
    }

    public function eventDispatch($event)
    {
        $this->log('event dispatch begin');
        $this->log('event:'.$event);
        switch ($event) {
            case Wechat::EVENT_SUBSCRIBE:
                $this->subscribe();
                break;
            case Wechat::EVENT_UNSUBSCRIBE:
                $this->unsubscribe();
                break;
            case Wechat::EVENT_MENU_CLICK:
                $this->keywordReply($this->revData['EventKey']);
                break;
            case Wechat::EVENT_MENU_VIEW:
                $this->redirect($this->revData['EventKey']);
                break;
            case Wechat::EVENT_SCAN:
                $this->scan();
                break;
        }
        $this->log('event dispatch end');
    }

    /**
     * 扫描二维码
     */
    public function scan()
    {
        $this->log('scan begin');
        //回复
//        //更新扫描数据
        $scanLogic = new WechatQrcodeLogic();
        $sceneId = $this->weChatObj->getRevSceneId();
        $scanLogic->updateScan($sceneId);
        $scanLogic->bindBdUser($this->openId, $sceneId);
        $keyword = $scanLogic->getScanFunction($sceneId);
        $this->keywordReply($keyword);
        $this->log('init end');
        exit();
    }

    /**
     * 关注
     */
    public function subscribe()
    {
        $this->log('subscribe begin');
//        $this->weChatObj = new Wechat('');

        //记录用户信息
        $userLogic = new WechatUserLogic();
        $userInfo = $this->weChatObj->getUserInfo($this->openId);
        if (!$userInfo) {
            $this->errorLog();
        }
        $userLogic->recordUserInfo($this->openId, $userInfo);

        //绑定用户信息
//        $bmtUid = session('BMT_UID');       //宝贝码头uid，
//        if ($bmtUid != '') {
//            $userLogic->bindUser($this->openId, $bmtUid);
//        }
        //用户扫描带参数二维码
        if (isset($this->revData['Ticket']) && !empty($this->revData['Ticket'])) {
            $this->scan();
        }

        //回复
        $replyModel = new WechatReplyLogic();
        $content = $replyModel->subscribeReply($this->weChatId);
        $res = $this->weChatObj->text($content)->reply();
        if (!$res) {
            $this->errorLog();
        }
        $this->log('subscribe end');

        exit();
    }

    /**
     * 取消关注
     */
    public function unsubscribe()
    {
        $this->log('unsubscribe begin');
        $userModel = new WechatUserModel();
        $userModel->unsubscribe($this->openId);
        $this->log('unsubsrribe end');
        exit();
    }

    /**
     * 关键词回复
     * @param string $content
     */
    public function keywordReply($content = '')
    {
        $this->log('keyword reply begin');

        $replyModel = new WechatReplyLogic();
        $replyContent = $replyModel->keywordsReply($content, $this->weChatId);
        $res = $this->weChatObj->text($replyContent)->reply();
        if (!$res) {
            $this->errorLog();
        };
        echo $replyContent;
        $this->log('keyword reply end');
        exit();
    }
}