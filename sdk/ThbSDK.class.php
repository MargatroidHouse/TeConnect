<?php
// +----------------------------------------------------------------------
// | TOPThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 沢城アリス <whuihuan@outlook.com> <https://www.alicem.top>
// +----------------------------------------------------------------------
// | ThbSDK.class.php 2023-12-31
// +----------------------------------------------------------------------

class ThbSDK extends ThinkOauth
{
    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://thwiki.cc/rest/oauth2/authorize';

    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = 'https://thwiki.cc/rest/oauth2/access_token';

    /**
     * 获取request_code的额外参数,可在配置中修改 URL查询字符串格式
     * @var srting
     */
    protected $Authorize = '';

    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = 'https://thwiki.cc/rest/oauth2/';

    /**
     * 组装接口调用参数 并调用接口
     * @param  string $api    API
     * @param  string $param  调用API的额外参数
     * @param  string $method HTTP请求方法 默认为GET
     * @return json
     */
    public function call($api, $param = '', $method = 'GET', $multi = false)
    {
        /* THB调用公共参数 */
        $header = array(
            "Authorization: Bearer {$this->Token['access_token']}",
        );

        $data = $this->http($this->url($api), $this->param(array(), $param), $method, $header, $multi);
        return json_decode($data, true);
    }

    /**
     * 解析access_token方法请求后的返回值
     * @param string $result 获取access_token的方法的返回值
     */
    protected function parseToken($result, $extend)
    {
        $data = json_decode($result, true);
        if ($data['access_token'] && $data['expires_in']) {
            $this->Token = $data;
            $data['openid'] = $this->openid();
            return $data;
        } else {
            throw new Exception("获取THBWiki ACCESS_TOKEN 出错：{$result}");
        }
    }

    /**
     * 获取当前授权应用的openid
     * @return string
     */
    public function openid()
    {
        $data = $this->Token;
        if (isset($data['openid'])) {
            return $data['openid'];
        } 
        $data = $this->call('resource/profile');
        if (!empty($data['sub'])) {
            return $data['sub'];
        } else {
            throw new Exception('没有获取到THBWiki用户ID！');
        }
    }
}
