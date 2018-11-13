<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/11/10
 * Time: 13:46
 */

namespace swz\opensearch;
require_once("OpenSearch/Autoloader/Autoloader.php");
use OpenSearch\Client\OpenSearchClient;
use OpenSearch\Client\SearchClient;
use OpenSearch\Generated\Search\SearchParams;
use OpenSearch\Util\SearchParamsBuilder;
use yii\base\Component;
use yii\web\BadRequestHttpException;

/**
 * Class OpenSearch
 * @package swz\opensearch
 * @property SearchClient searchClient
 * @property SearchParamsBuilder searchParams
 */
class OpenSearch extends Component
{
    //设置config子句的start值
    public $searchStart = 0;
    //设置config子句的hit值
    public $hit = 20;
    //指定应用用于搜索
    public $appName;
    //搜索索引
    public $searchIndex = 'default';
    //搜索关键词
    public $keyWords;
    //指定返回数据格式
    public $format = 'fulljson';
    //排序字段
    public $sorts;

    public $_searchClient;

    public $_searchParams;

    /**
     * @author: SWZ
     * @time: 17:10
     * @date: 2018/11/10
     * @describe:
     */
    public function init()
    {
        parent::init();
        if (!$this->appName) {
            throw new \Exception('appName is empty');
        }
    }

    /**
     * 获取SearchClient
     * @return SearchClient
     * @author: SWZ
     * @time: 9:26
     * @date: 2018/11/13
     * @describe:
     */
    public function getSearchClient()
    {
        if ($this->_searchClient === null) {
            $this->_searchClient=  new SearchClient(\Yii::$app->openSearch->client);
        }
        return $this->_searchClient;
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
    public function search()
    {
        $searchClient = $this->searchClient;
        // 执行搜索，获取搜索结果
        $ret = $searchClient->execute($this->searchParams->build());
        if ($ret && $ret->result) {
            $result = json_decode($ret->result, true);
        } else {
            return false;
        }
        if ($result['status'] == 'OK') {
            return $result['result'];
        }else{
            throw new BadRequestHttpException($result['errors'][0]['message'],$result['errors'][0]['code']);
        }
        return false;
    }

    /**
     * 获取搜索参数类
     * @return null
     * @author: SWZ
     * @time: 9:32
     * @date: 2018/11/13
     * @describe:
     */
    public function getSearchParams(){
        if ($this->_searchParams === null) {
            // 实例化一个搜索参数类
            $params = new SearchParamsBuilder();
            $params->setStart($this->searchStart);
            $params->setHits($this->hit);
            $params->setAppName($this->appName);
            $params->setQuery($this->searchIndex.":".$this->keyWords);
            $params->setFormat($this->format);
            //添加排序字段
            if ($this->sorts){
                if (is_array($this->sorts) && !$this->sorts['field']){
                    foreach ($this->sorts as $sort){
                        $params->addSort($sort['field'], $sort['order'] ?: SearchParamsBuilder::SORT_INCREASE);
                    }
                }else{
                    $params->addSort($this->sorts['field'], $this->sorts['order'] ?: SearchParamsBuilder::SORT_INCREASE);
                }
            }
            $this->_searchParams = $params;
        }
        return $this->_searchParams;
    }
}
