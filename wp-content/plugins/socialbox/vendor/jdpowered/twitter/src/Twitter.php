<?php namespace jdpowered\Twitter;

use jdpowered\OAuth\Consumer;
use jdpowered\OAuth\Request;
use jdpowered\OAuth\Util;
use jdpowered\OAuth\SignatureMethods\HmacSha1SignatureMethod;

/**
 * Twitter OAuth class
 */
class Twitter {


    /**
     * cUrl function timeout
     *
     * @type {Number}
     */
    protected $curlTimeout = 30;

    /**
     * HTTP connection timeout
     *
     * @type {Number}
     */
    protected $timeout = 5;




    /**
     *
     */
    const USER_AGENT = 'TwitterOAuth/0.1.0-dev';

    /**
     *
     */
    const HOST = "https://api.twitter.com/1.1/";

    /**
     *
     */
    const REQUEST_TOKEN_URL = 'https://api.twitter.com/oauth/request_token';

    /**
     *
     */
    const ACCESS_TOKEN_URL = 'https://api.twitter.com/oauth/access_token';

    /**
     *
     */
    const AUTHENTICATE_URL = 'https://api.twitter.com/oauth/authenticate';

    /**
     *
     */
    const AUTHORIZE_URL = 'https://api.twitter.com/oauth/authorize';

    /**
     * @var
     */
    private $sslVerification = true;

    /**
     * @var
     */
    private $userAgent;

    /**
     * @var
     */
    private $decodeJSON = true;

    /**
     * @var
     */
    private $format = 'json';

    /**
     * @var
     * Read-Only
     */
    private $lastStatusCode;

    /**
     * @var
     */
    private $lastHeaders;

    /**
     * Information about the last request performed
     *
     * @type {Array}
     */
    private $lastRequestInfo = array();

    /**
     * @var
     */
    private $signatureMethod;

    /**
     * @var
     */
    private $consumer;

    /**
     * @var
     */
    private $token;

    /**
     * [__construct description]
     *
     * @param  string $consumerKey
     * @param  string $consumerSecret
     * @param  string $token          = null
     * @param  string $tokenSecret    = null
     */
    function __construct($consumerKey, $consumerSecret, $token = null, $tokenSecret = null)
    {

        /* Initialize the signature method */
        $this->signatureMethod = new HmacSha1SignatureMethod();

        /* Create OAuth consumer */
        $this->consumer = new Consumer($consumerKey, $consumerSecret);

        /* Set OAuth token, if available */
        if(!empty($token) and !empty($tokenSecret))
        {
            $this->token = new Consumer($token, $tokenSecret);
        }
        else
        {
            $this->token = null;
        }
    }





    /**************************************************************************\
    *                        PROPERTY GETTERS & SETTERS                        *
    \**************************************************************************/

    public function setSslVerification($newSslVerification)
    {
        $this->sslVerification = $newSslVerification;
    }

    public function getSslVerification()
    {
        return $this->sslVerification;
    }

    public function setUserAgent($newUserAgent)
    {
        $this->userAgent = $newUserAgent;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

    public function setDecodeJSON($newDecodeJSON)
    {
        $this->decodeJSON = $newDecodeJSON;
    }

    public function getDecodeJSON()
    {
        return $this->decodeJSON;
    }

    public function setFormat($newFormat)
    {
        $this->format = $newFormat;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function getLastStatusCode()
    {
        return $this->lastStatusCode;
    }

    public function getLastHeaders($header = null)
    {
        /*
            Return all headers if none was specified
         */
        if(is_null($header))
            return $this->lastHeaders;

        /*
            Return a single header or false if it's not available
         */
        $header = str_replace('-', '_', strtolower($header));
        return (isset($this->lastHeaders[$header])) ? $this->lastHeaders[$header] : false;
    }





    /**************************************************************************\
    *                           OAUTH AUTHENTICATION                           *
    \**************************************************************************/

    /**
     * Get a request_token from Twitter
     *
     * @param string $oauth_callback
     */
    function getRequestToken($callback)
    {
        $parameters = array(
            'oauth_callback' => $callback,
        );

        $request = $this->oAuthRequest(self::REQUEST_TOKEN_URL, 'GET', $parameters);

        $token = Util::parse_parameters($request);
        $this->token = new Consumer($token['oauth_token'], $token['oauth_token_secret']);
        return $token;
    }

    /**
     * Get the authorize URL
     *
     * @returns a string
     */
    function getAuthorizeURL($token, $signinWithTwitter = true)
    {
        if(is_array($token))
        {
            $token = $token['oauth_token'];
        }

        if( ! $signinWithTwitter)
        {
            return $this->authorizeURL() . "?oauth_token={$token}";
        }
        else
        {
            return $this->authenticateURL() . "?oauth_token={$token}";
        }
    }

    /**
     * Exchange request token and secret for an access token and
     * secret, to sign API calls.
     *
     * @returns array("oauth_token" => "the-access-token",
     *                "oauth_token_secret" => "the-access-secret",
     *                "user_id" => "9436992",
     *                "screen_name" => "abraham")
     */
    function getAccessToken($oauthVerifier)
    {
        $parameters = array(
            'oauth_verifier' => $oauthVerifier,
        );

        $request = $this->oAuthRequest(self::ACCESS_TOKEN_URL, 'GET', $parameters);

        $token = Util::parse_parameters($request);
        $this->token = new Consumer($token['oauth_token'], $token['oauth_token_secret']);
        return $token;
    }

    /**
     * [getXAuthToken description]
     *
     * @param string $username
     * @param string $password
     */
    public function getXAuthToken($username, $password)
    {
        // Depricated
        return null;
    }





    /**************************************************************************\
    *                         PUBLIC REQUEST INTERFACE                         *
    \**************************************************************************/

    /**
     * Perform a GET request
     *
     * @param  string $url
     * @param  array  $parameters = array()
     * @return stdclass/string
     */
    public function get($url, $parameters = array())
    {
        $response = $this->oAuthRequest($url, 'GET', $parameters);

        if($this->format === 'json' && $this->decodeJSON)
        {
            return json_decode($response);
        }

        return $response;
    }

    /**
     * Perform a POST request
     *
     * @param  string $url
     * @param  array  $parameters = array()
     * @return stdclass/string
     */
    public function post($url, $parameters = array())
    {
        $response = $this->oAuthRequest($url, 'POST', $parameters);

        if($this->format === 'json' && $this->decodeJSON)
        {
            return json_decode($response);
        }

        return $response;
    }

    /**
     * Perform a DELETE request
     *
     * @param  string $url
     * @param  array  $parameters = array()
     * @return stdclass/string
     */
    public function delete($url, $parameters = array())
    {
        $response = $this->oAuthRequest($url, 'DELETE', $parameters);

        if($this->format === 'json' && $this->decodeJSON)
        {
            return json_decode($response);
        }

        return $response;
    }

    /**************************************************************************\
    *                             REQUEST HANDLING                             *
    \**************************************************************************/

    /**
     * [oAuthRequest description]
     *
     * @param string $url
     * @param string $method
     * @param array  $parameters
     */
    protected function request($endpoint, $method, $parameters)
    {
        $url = $this->buildUrl($endpoint);

        $request = Request::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters);
        $request->sign_request($this->signatureMethod, $this->consumer, $this->token);

        switch($method)
        {
            case 'GET':
                return $this->http($request->to_url(), 'GET');

            default:
                return $this->http($request->get_normalized_http_url(), $method, $request->to_postdata());
        }
    }

    /**
     * Make a HTTP request utilizing cUrl
     *
     * @param  string $url
     * @param  string $method
     * @param  array  $postfields = null
     * @return {[type]}
     */
    protected function http($url, $method, $postData = null)
    {
        $this->resetLastHeaders();

        $curl = curl_init();

        /*
            Apply basic cUrl settings
         */
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->curlTimeout);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, array($this, 'addHeader'));
        curl_setopt($curl, CURLOPT_HEADER, false);

        /*
            Set user agent string
        */
        if( ! empty($this->userAgent))
        {
            curl_setopt($curl, CURLOPT_USERAGENT, self::USER_AGENT . ' ' . $this->userAgent);
        }
        else
        {
            curl_setopt($curl, CURLOPT_USERAGENT, self::USER_AGENT);
        }

        /*
            Set SSL peer and host verification
        */
        if( ! $this->sslVerification)
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        switch($method)
        {
            /*
                Set POST method related options
             */
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, TRUE);
                if( ! empty($postData))
                {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
                }
            break;

            /*
                Set DELETE method related options
             */
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if( ! empty($postData))
                {
                    $url .= '?' . $postData;
                }
            break;
        }

        /*
            Set request url
         */
        curl_setopt($curl, CURLOPT_URL, $url);

        /*
            Execute cUrl request
         */
        $response = curl_exec($curl);

        /*
            Fetch some status information about the request
         */
        $this->lastStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->lastRequestInfo = curl_getinfo($curl);

        /*
            Close cUrl instance and return response
         */
        curl_close($curl);
        return $response;
    }





    /**************************************************************************\
    *                             VARIOUS HELPERS                              *
    \**************************************************************************/

    /**
     * Get the header info to store.
     */
    function addHeader($curl, $header)
    {
        /*
            Parse header line and store it
         */
        $i = strpos($header, ':');
        if( ! empty($i))
        {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 1));
            $this->lastHeaders[$key] = $value;
        }

        /*
            Return length of header (required by cUrl)
         */
        return strlen($header);
    }

    protected function buildUrl($endpoint)
    {
        if(strrpos($endpoint, 'https://') !== 0 AND strrpos($endpoint, 'http://') !== 0)
        {
            return self::HOST . $endpoint . '.' . $this->format;
        }

        return $endpoint;
    }

    protected function resetLastHeaders()
    {
        $this->lastHeaders = array();
    }
}
