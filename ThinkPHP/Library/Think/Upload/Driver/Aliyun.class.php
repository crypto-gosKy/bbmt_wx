<?php

namespace Think\Upload\Driver;
use Think\Upload\Driver\Aliyun\Oss\ALIOSS;

/**
 * 
 * @authors xluo
 * @date    2016年4月1日 
 * @copyright (c) 2016, BBMT
 * @version 1.0
 */
class aliyun {

    private $oss;
    private $config = array(
        'AccessKeyId' => '', //OSS用户
        'AccessKeySecret' => '', //OSS密码
        'domain' => '', //OSS空间路径
        'bucket' => '', //空间名称
        'Endpoint' => '', //超时时间
    );

    /**
     * 本地上传错误信息
     * @var string
     */
    private $error = '';

    /**
     * 构造函数，用于设置上传根路径
     * @param array  $config 配置
     */
    public function __construct($config) {
        /* 默认配置 */
        $this->config = array_merge($this->config, $config);
        $this->oss = new ALIOSS($this->config['AccessKeyId'], $this->config['AccessKeySecret'], $this->config['domain']);
    }

    /**
     * 检测上传根目录(OSS上传时支持自动创建目录，直接返回)
     * @param string $rootpath   根目录
     * @return boolean true-检测通过，false-检测失败
     */
    public function checkRootPath($rootpath) {
        /* 设置根目录 */
        $this->rootPath = trim($rootpath, './') . '/';
        return true;
    }

    /**
     * 检测上传目录(OSS上传时支持自动创建目录，直接返回)
     * @param  string $savepath 上传目录
     * @return boolean          检测结果，true-通过，false-失败
     */
    public function checkSavePath($savepath) {
        return true;
    }

    /**
     * 创建文件夹 (OSS上传时支持自动创建目录，直接返回)
     * @param  string $savepath 目录名称
     * @return boolean          true-创建成功，false-创建失败
     */
    public function mkdir($savepath) {
        return true;
    }

    /**
     * 保存指定文件
     * @param  array   $file    保存的文件信息
     * @param  boolean $replace 同名文件是否覆盖
     * @return boolean          保存状态，true-成功，false-失败
     */
    public function save(&$file, $replace = true) {
        $object = "{$file['savepath']}{$file['savename']}";
        $response = $this->oss->upload_file_by_file($this->config['bucket'], $object, $file['tmp_name']);
        if ($response && is_object($response) && $response->status == 200) {
             $file['url'] = $object;
            return TRUE;
        } else {
            $this->setError($response->status);
            return FALSE;
        }
    }

    /**
     * 获取最后一次上传错误信息
     * @return string 错误信息
     */
    public function getError() {
        return $this->error;
    }

    public function setError($err) {
        $this->error = $err;
    }

}
