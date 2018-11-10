<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/11/10
 * Time: 13:46
 */
namespace swz\opensearch;
use OpenSearch\Client\OpenSearchClient;
use yii\base\Component;

class OpenSearch extends  Component
{
    public $accessKeyId;
    public $secret;
    public $endPoint;
    public $appName;
    public $suggestName;
    public $options;

    public $_client;

    /**
     * @author: SWZ
     * @time: 17:10
     * @date: 2018/11/10
     * @describe:
     */
    public function init()
    {
        parent::init();
        if (!$this->accessKeyId) {
            throw new Exception('accessKeyId is empty');
        }
        if (!$this->secret) {
            throw new Exception('secret is empty');
        }

        if (!$this->endPoint) {
            throw new Exception('endPoint is empty');
        }
    }

    /**
     * 获取OpenSearchClient实例
     * @return OpenSearchClient
     * @author: SWZ
     * @time: 17:09
     * @date: 2018/11/10
     * @describe:
     */
    public function getClient()
    {
        if ($this->_client === null) {
            $this->_client  = new OpenSearchClient($accessKeyId, $secret, $endPoint, $options);
        }
        return $this->_client;
    }

    /**
     * 搜索
     * @param array $params
     * @return mixed
     * @author: SWZ
     * @time: 17:09
     * @date: 2018/11/10
     * @describe:
     */
    public function search($params = [])
    {
        $search = new CloudsearchSearch($this->client);
        return $search->search($params);
    }
}
