<?php
/**
 * 物流跟踪信息通知【回调接口】
 * @author [chan] <[<maclechan@qq.com>]>
 * @date(2016-04-08)
 */

namespace Api\Controller;

use Common\Model\TradeLogisticsModel;

class LogisticupdatecallbackController extends BaseController
{
    /**
     * @param array param 快递100的请求
     * @return json param 返回给快递100的响应
     */
    public function index()
    {
        trace(($_POST), 'LogisticupdatecallbackController', 'DEBUG',TRUE);
        //订阅成功后，收到首次推送信息是在5~10分钟之间，在能被5分钟整除的时间点上，0分..5分..10分..15分....
        $param = json_decode($_POST['param'],TRUE);
        if (empty($param)) {
            $this->ajaxReturn(['result' => 'false', 'returnCode' => 500, 'message' => '失败']);
        }
        try{
            //$param包含了文档指定的信息，...这里保存您的快递信息,$param的格式与订阅时指定的格式一致
           // $invoice_no = '7896625'; //快递运单号
            $invoice_no = $param['lastResult']['nu']; //快递运单号
            $logistics_company=$param['lastResult']['com'];//快递公司
            $Logistics = D('TradeLogistics');
            $id = $Logistics->where(['invoice_no'=>$invoice_no,'logistics_company'=>$logistics_company])->getField('id');
            if(!$id) {
                //插入
                $data=[
                    'invoice_no' => $param['lastResult']['nu'],//快递运单号
                    'logistics_company' => $param['lastResult']['com'],//快递公司
                    'logistics_data' => addslashes(json_encode($param)),//物流推送数据
                    'updated' => time()
                    ];
                //校验楼据
                if($Logistics->create($data)) {
                    $Logistics->add();
                }else{
                    $this->ajaxReturn(['result' => 'false', 'returnCode' => 500, 'message' => '失败']);
                }
            }else{
                if ($param['status']=='abort' && $param['comNew']!='' && $param['autoCheck']==1){
                    //新的快递公司编码
                    $data = [
                        'logistics_company' => $param['comNew'],
                        'logistics_data' => addslashes(json_encode($param)),
                        'updated' => time(),
                    ];
                    $Logistics->where("id=$id")->setField($data);
                } else {
                    //正常更新
                    $data = [
                        'logistics_data' => addslashes(json_encode($param)),
                        'updated' => time(),
                    ];
                    $Logistics->where("id=$id")->setField($data);
                }
            }
            //要返回成功（格式与订阅时指定的格式一致），不返回成功就代表失败，没有这个30分钟以后会重推
            $this->ajaxReturn(['result' => 'true', 'returnCode' => 200, 'message' => '成功']);
        } catch(Exception $e)
        {
            //保存失败，返回失败信息，30分钟以后会重推
            $this->ajaxReturn(['result' => 'false', 'returnCode' => 500, 'message' => '失败']);
        }
    }
}