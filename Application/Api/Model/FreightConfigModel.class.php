<?php
/**
 * Created by PhpStorm.
 * User: meijiang
 * Date: 2016/5/20
 * Time: 12:33
 */
namespace Api\Model;
use Think\Model;

class FreightConfigModel extends Model{
    public function getConfigsByTemplateId($template_id){
        $where = array(
            'logistics_masterplate_id' => $template_id
        );
        return $this->where($where)->select();
    }
}