<?php
namespace addons\system;

/**
 * Class RESTClient
 * improved version of the Shopware Example-ApiClient
 * @package addons\system
 */
class RESTClient
{
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';
    protected $validMethods = [
        self::METHOD_GET,
        self::METHOD_PUT,
        self::METHOD_POST,
        self::METHOD_DELETE,
    ];
    protected $apiUrl;
    protected $cURL;


    public function __construct()
    {

    }

    /**
     * RESTClient constructor.
     * Should work with Shopware and Uberall
     * @param $apiUrl
     * @param null $username
     * @param null $apiKey
     * @param string $apiKeyFieldname
     */
    public function init($apiUrl, $username = null, $apiKey = null, $apiKeyFieldname = 'privateKey'){
        $this->apiUrl = rtrim($apiUrl, '/') . '/';
        //Initializes the cURL instance
        $this->cURL = curl_init();
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($this->cURL, CURLOPT_USERAGENT, 'aoopng RESTClient');
        curl_setopt($this->cURL, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);

        $headers = ['Content-Type: application/json; charset=utf-8'];
        if($username !== null && strlen($username) > 0){
            curl_setopt($this->cURL, CURLOPT_USERPWD, $username . ':' . $apiKey);
        }
        else if ($apiKey !== null && strlen($apiKey)){
            $headers[] = $apiKeyFieldname . ': ' .$apiKey;
        }
        curl_setopt(
            $this->cURL,
            CURLOPT_HTTPHEADER,
            $headers
        );
    }

    public function call($url, $method = self::METHOD_GET, $data = [], $params = [])
    {
        if (!in_array($method, $this->validMethods)) {
            throw new \Exception('Invalid HTTP-Methode: ' . $method);
        }
        $queryString = '';
        if (!empty($params)) {
            $queryString = http_build_query($params);
        }
        $url = rtrim($url, '?') . '?';
        $url = $this->apiUrl . $url . $queryString;
        $dataString = json_encode($data);
        curl_setopt($this->cURL, CURLOPT_URL, $url);
        curl_setopt($this->cURL, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $dataString);
        $result = curl_exec($this->cURL);
        $httpCode = curl_getinfo($this->cURL, CURLINFO_HTTP_CODE);

        return ['raw' => $result, 'code' => $httpCode, 'json' => json_decode($result, true)];
    }

    /**
     * @param $url
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function get($url, $params = [])
    {
        return $this->call($url, self::METHOD_GET, [], $params);
    }

    /**
     * @param $url
     * @param array $data
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function post($url, $data = [], $params = [])
    {
        return $this->call($url, self::METHOD_POST, $data, $params);
    }

    /**
     * @param $url
     * @param array $data
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function put($url, $data = [], $params = [])
    {
        return $this->call($url, self::METHOD_PUT, $data, $params);
    }

    /**
     * @param $url
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function delete($url, $params = [])
    {
        return $this->call($url, self::METHOD_DELETE, [], $params);
    }
}