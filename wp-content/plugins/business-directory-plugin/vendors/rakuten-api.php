<?php
/**
 * @file Contains classes for accessing Rakuten PopShops
 *
 * PopShops.php contains base class, caching class, and exception class.
 *
 * See api doc for PopShops at: https://www.popshops.com/support/api-overview
 */


class PopShops {

    const FORMAT_XML = 'xml';
    const FORMAT_JSON = 'json';
    const FORMAT_ARRAY = 'array';
    const FORMAT_OBJECT = 'object';

    const API_VERSION = 'v3';

    private $host = 'api.popshops.com';
    //protected $format = self::FORMAT_OBJECT;
    protected $format = self::FORMAT_JSON;
    protected $api_key = '73q0rijkutz169vyq4w39zbas';
    protected $catalog_key = '8u5tah1dl5lf35d4kbmid46wj';

    /**
     * Construct a popshop object
     *
     * @param string $api_key Api key. This is required and constructing without this value will throw an exception
     * @param Query\IQuery $fetcher
     *
     */
    public function __construct(){
    }


    /**
     * This method returns a list of all available merchants.
     *
      * @return merchants -  Total count, merchant type id that was passed in and catalog key that was passed in.
     * @return merchant -  A list of all merchants, with id, name, network, logo url and url.
     */
    public function getMerchants($merchant_name = null, $merchant_id = null)
    {
        
        if(!empty($merchant_id)){
            $params['merchant'] = $merchant_id;
        }
        
        if(!empty($merchant_name)){
            $params['keyword'] = $merchant_name;
        }
        
        return $this->fetch('merchants', $params);
    }
    
    
    /**
     * You can use this to find a list of all available merchant types. A merchant 
     * type is used to categorize a merchant. You can use a merchant_type id to further 
     * filter product/deal queries.
     *
     * @return merchant_types - Total count of merchant types
     * @return merchant_type - A list of all merchant types with id, name and merchant count
     */
    public function getMerchantTypes()
    {
        
        return $this->fetch('merchant_types');
    }
    
    


    /**
     * This method returns the list of all available networks
     *
     * @return total count of networks, along with id and name of each network
     */
    public function getNetworks()
    {
        return $this->fetch('networks');
    }

    

    
    /**
     * This method returns a set of products that match a query, specified using the $productParams
     * @link https://www.popshops.com/support/find-products
     *
     * @param int $limit The maximum number of results to return, or 100 if not specified.
     * The maximum value is 100. Combine with the offset parameter to implement paging.
     * @param int $offset The index of the first product to return, or 0 (zero) if not specified.
     * A client can use this to implement paging through large result sets.
     *
     * @param array $productParams
     * @see API::filterProductQueryParams()
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed A list of Product objects.
     * Each Product has an id, name, description, merchant price, retail price, merchant id,
     * large image url, medium image url, small image url, product group id, 
     * product group product count, 
     * categories, images in small/medium/large, and a URL that forwards to the retailer's site.
     */
    public function getProducts($limit = 100, $offset = 0, array $productParams = array())
    {
        if (!is_null($limit) && $limit > 100) {
            throw new \InvalidArgumentException('Limit exceed maximum possible value of 100');
        }
        

        $params = array(
            'results_per_page' => $limit,
            'page' => $offset
        );

        $params = array_merge($params, $this->filterProductQueryParams($productParams));

        return $this->fetch('products', $params);
    }

    /**
     * @see API::getProducts()
     *
     * @param string $search
     * @param int $limit
     * @param int $offset
     * @param array $productParams
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function search($search, $limit = 100, $offset = 0, array $productParams = array())
    {
        $productParams['keywords'] = $search;
        return $this->getProducts($limit, $offset, $productParams);
    }

    /**
     * Fetch a particular method from PopShops
     *
     * @param string $method the method you are requesting
     * @param array $params
     *
     * @return mixed The response from PopShops or NULL in the event of an error
     */
    protected function fetch($method, array $params = array())
    {
        /*
        if ($this->format == self::FORMAT_ARRAY || $this->format == self::FORMAT_OBJECT) {
            $params['format'] = 'json';
        } else {
            $params['format'] = $this->format;
        }
         * *
         */

        $params['catalog'] = $this->catalog_key;
        $params['account'] = $this->api_key;

        $url = $this->buildUrl(
            array(
                "scheme" => "http",
                "host" => $this->host,
                "path" => '/' . self::API_VERSION . '/' . trim($method, '/'). '.' . $this->format,
                "query" => $this->buildQuery($params)
            )
        );
//echo $url;
        
        $response = file_get_contents($url);
        
        if (!$response) {
            return null;
        }
//echo print_r($response);
        return json_decode($response, true);
        
        //return $response;
    }

    /**
     *
     * @param array $params input parameters
     * @return array filtered parameters
     */
    protected function filterProductQueryParams(array $params)
    {

        if (empty($params)) {
            return array();
        }

        $available_params = array(  'brand',
                                    'category',
                                    'keyword',              //to find in product name or description
                                    'keyword_description',  //to find in product description
                                    'keyword_brand',        //to find in product brand
                                    'keyword_name',         //to find in product name
                                    'merchant',
                                    'merchant_type',
                                    'page',
                                    'price',
                                    'product_id',
                                    'results_per_page',
                                    'price_min',
                                    'price_max');

        $params = array_intersect_key($params, array_flip($available_params));

        return $params;
    }

    /**
     * Strip out php url array notation to leave java notation: 'fl[0]' becomes 'fl'
     *
     * @param array $query key-value pairs of parameters
     *
     * @return string The properly formatted query string
     */
    protected function buildQuery(array $query)
    {
        $query = http_build_query($query, null, '&');

        return preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $query);
    }

    /**
     * Build URL
     * @param $parts
     * @return string
     */
    protected function buildUrl($parts)
    {
        if (function_exists('http_build_url')) {
            return http_build_url($parts);
        }

        return $parts['scheme'] . '://'
            . trim($parts['host'], '/') . '/'
            . trim($parts['path'], '/') . '?'
            . $parts['query'];
    }


    /**
     * @param Query\IQuery $fetcher
     */
    public function setFetcher(Query\IQuery $fetcher)
    {
        $this->fetcher = $fetcher;
    }

    /**
     * @return Query\IQuery
     */
    public function getFetcher()
    {
        return $this->fetcher;
    }

    
    /**
     * @param string $format
     * @throws \InvalidArgumentException
     */
    public function setFormat($format)
    {
        $supported_formats = array(
            self::FORMAT_JSON,
            self::FORMAT_XML,
            self::FORMAT_ARRAY
        );

        if (!in_array($format, $supported_formats)) {
            $formats = implode(', ', $supported_formats);
            $format = (string)$format;
            throw new \InvalidArgumentException("The format '$format' is not supported.  Supported formats: $formats");
        }

        $this->format = $format;
    }

    /**
     * @return string
    */ 
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $host
     * @throws \InvalidArgumentException
    */ 
    public function setHost($host)
    {
        if (!filter_var($host, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Host parameter is invalid URL');
        }

        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $pid
     */
    public function setApiKey($pid)
    {
        $this->api_key = $pid;
    }

    /**
     * @return string
    */ 
    public function getApiKey()
    {
        return $this->api_key;
    }

    
}

