<?php
/**
 * @file Contains classes for accessing ShopStyle
 *
 * ShopStyleClass.php contains base class, caching class, and exception class.
 *
 * See api doc for ShopStyle at: http://www.shopstyle.com/static/api/index.html
 */


class ShopStyle {

    const FORMAT_XML = 'xml';
    const FORMAT_JSON = 'json';
    const FORMAT_ARRAY = 'array';
    const FORMAT_OBJECT = 'object';

    const API_VERSION = 'v2';

    private $host = 'api.shopstyle.com';
    protected $format = self::FORMAT_OBJECT;
    protected $api_key = 'uid5625-344275-47';
   

    /**
     * Construct a shopstyle object
     *
     * @param string $api_key Api key. This is required and constructing without this value will throw an exception
     * @param Query\IQuery $fetcher
     *
     */
    public function __construct(){
    }


    /**
     * This method returns a list of brands that have live products. Brands that have very few products will be omitted.
     *
     * @return mixed A list of all Brands, with id, name, url.
     */
    public function getBrands()
    {
        return $this->fetch('brands');
    }

    /**
     * This method returns a list of the categories available to the API
     *
     * @param integer $category The ID of the category to use as the starting point. By default,
     *  the global root of the category tree is used
     *
     * @param integer $depth The number of levels from the root to include in the response. By default
     *  all the levels are included.
     *
     * @return mixed A list of all categories, with id, name, and parent id.
     */
    public function getCategories($category = null, $depth = null)
    {
        $params = array();

        if (!empty($category)) {
            $params['cat'] = $category;
        }
        if (!empty($depth)) {
            $params['depth'] = $depth;
        }

        return $this->fetch('categories', $params);
    }

    /**
     * This method returns the list of canonical colors available.
     *
     * @return mixed A list of all Colors, with id, name, and url of each.
     */
    public function getColors()
    {
        return $this->fetch('colors');
    }

    /**
     * This method returns a list of retailers that have live products.
     *
     * @return mixed A list of all Retailers, with id, name, and url of each.
     */
    public function getRetailers()
    {
        return $this->fetch('retailers');
    }


    /**
     * This method returns the product with the ID specified in the path.
     *
     * @param $product_id
     * @return mixed A single Product object. A Product has an id, name, description, price, retailer, brand name,
     * categories, images in small/medium/large, and a URL that forwards to the retailer's site.
     *
     * @throws \InvalidArgumentException
     */
    public function getProduct($product_id)
    {
        if (!is_int($product_id)) {
            throw new \InvalidArgumentException('Supplied argument is not an integer');
        }

        return $this->fetch('products/' . $product_id);
    }

    /**
     * This method returns a list of filters and product counts that describe the results of a given product query.
     * The query is specified using the $productParams
     * @link https://shopsense.shopstyle.com/shopsense/7234186
     *
     * @param array $filters An array of the filters for which to calculate the histograms.
     * Possible values are Brand, Retailer, Price, Discount, Size and Color.
     *
     * @param int $floor The minimum count of products required for an entry to be included in the histogram.
     * @param array $productParams
     * @see API::filterProductQueryParams()
     *
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function getHistogram(array $filters = array(), $floor = null, array $productParams = array())
    {
        $available_filters = array('Brand', 'Retailer', 'Price', 'Discount', 'Size', 'Color');

        $invalid_filters = array_diff($filters, $available_filters);
        if (count($invalid_filters)) {
            throw new \InvalidArgumentException('Invalid filter passed: ' . (reset($invalid_filters)));
        }

        $params = array();

        if (!empty($filters)) {
            $params['filters'] = implode(',', $filters);
        }

        if (!empty($floor)) {
            $params['floor'] = (int)$floor;
        }

        $params = array_merge($params, $this->filterProductQueryParams($productParams));

        return $this->fetch('products/histogram', $params);
    }


    /**
     * This method returns a set of products that match a query, specified using the $productParams
     * @link https://shopsense.shopstyle.com/shopsense/7234190
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
     * Each Product has an id, name, description, price, retailer, brand name,
     * categories, images in small/medium/large, and a URL that forwards to the retailer's site.
     */
    public function getProducts($limit = 100, $offset = 0, array $productParams = array())
    {
        if (!is_null($limit) && $limit > 100) {
            throw new \InvalidArgumentException('Limit exceed maximum possible value of 100');
        }

        $params = array(
            'limit' => $limit,
            'offset' => $offset
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
        $productParams['fts'] = $search;
        return $this->getProducts($limit, $offset, $productParams);
    }

    /**
     * Fetch a particular method from ShopStyle
     *
     * @param string $method the method you are requesting
     * @param array $params
     *
     * @return mixed The response from ShopStyle or NULL in the event of an error
     */
    protected function fetch($method, array $params = array())
    {

        if ($this->format == self::FORMAT_ARRAY || $this->format == self::FORMAT_OBJECT) {
            $params['format'] = 'json';
        } else {
            $params['format'] = $this->format;
        }

        $params['pid'] = $this->api_key;

        $url = $this->buildUrl(
            array(
                "scheme" => "http",
                "host" => $this->host,
                "path" => 'api/' . self::API_VERSION . '/' . trim($method, '/'),
                "query" => $this->buildQuery($params)
            )
        );

        
        $response = file_get_contents($url);
        
        if (!$response) {
            return null;
        }

        return json_decode($response, true);
        
        //return $response;
    }

    /**
     * @see https://shopsense.shopstyle.com/shopsense/28044754
     *
     * @param array $params input parameters
     * @return array filtered parameters
     */
    protected function filterProductQueryParams(array $params)
    {

        if (empty($params)) {
            return array();
        }

        $available_params = array('fts', 'cat', 'fl', 'pdd', 'sort');

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

