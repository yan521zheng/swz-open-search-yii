<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/11/13
 * Time: 9:02
 */
namespace swz\opensearch;

use OpenSearch\Client\OpenSearchClient;
use OpenSearch\Client\SearchClient;
use OpenSearch\Util\SearchParamsBuilder;
use yii\base\Component;

header("Content-Type:text/html;charset=utf-8");
$basePath = __DIR__ . '/../';
require_once($basePath."OpenSearch/Autoloader/Autoloader.php");

/**
 * Class OpenSearch
 * @package swz\opensearch
 * @property OpenSearchClient client
 */
class OpenSearchConfig extends Component
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
            throw new \Exception('accessKeyId is empty');
        }
        if (!$this->secret) {
            throw new \Exception('secret is empty');
        }

        if (!$this->endPoint) {
            throw new \Exception('endPoint is empty');
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
            $this->_client = new OpenSearchClient($this->accessKeyId, $this->secret, $this->endPoint, $this->options);
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
    public function search($data = [])
    {
        $searchClient = new SearchClient($this->client);
        // 实例化一个搜索参数类
        $params = new SearchParamsBuilder();
        //设置config子句的start值
        $params->setStart($data['start'] ?: 0);
        //设置config子句的hit值
        $params->setHits($data['hit'] ?: 20);
        // 指定一个应用用于搜索
        if ($data['appName']){
            $params->setAppName($data['appName']);
        }else{
            throw new \Exception('appName is empty');
        }
        // 指定搜索关键词
        $params->setQuery(($data['search_index'] ?: 'default').":".$data['key_words']);
        // 指定返回的搜索结果的格式为json
        $params->setFormat("fulljson");
        //添加排序字段
        if ($data['sorts']){
            if (is_array($data['sorts'])){
                foreach ($data['sorts'] as $sort){
                    $params->addSort($sort['field'], $sort['order'] ?: SearchParamsBuilder::SORT_INCREASE);
                }
            }else{
                $params->addSort($data['sorts']['field'], $data['sorts']['order'] ?: SearchParamsBuilder::SORT_INCREASE);
            }
        }
        // 执行搜索，获取搜索结果
        $ret = $searchClient->execute($params->build());
        if ($ret && $ret->result) {
            $result = json_decode($ret->result, true);
        } else {
            return false;
        }
        if ($result['status'] == 'OK') {
            return $result['result'];
        }
        return false;
    }
}
