<?php

namespace App\Services\Shop;

class ShopifyAPI
{

    private $shop_domain;
    private $api_key;
    private $secret;
    private $token;
    private $client;
    private $last_response_headers;

    public function __construct(string $shopDomain, $credentials)
    {
        $this->shop_domain = $shopDomain;

        // Populate the credentials
        if (is_string($credentials)) {
            $this->setToken($credentials);
        } elseif (!empty($credentials['api_key']) && !empty($credentials['secret'])) {
            $this->api_key = $credentials['api_key'];
            $this->secret = $credentials['secret'];
        } else {
            throw new Exception("Unexpected value provided for the credentials");
        }

        // Initialize the client
        $this->initializeClient();
    }

    protected function initializeClient()
    {
        if ($this->client) {
            return $this->client;
        }

        $options = [
            'base_uri' => "https://{$this->shop_domain}/",
            'http_errors' => true,
        ];
        if (!empty($this->token)) {
            $options['headers']['X-Shopify-Access-Token'] = $this->token;
        }

        return $this->client = new Client($options);
    }

    public function setToken(string $token)
    {
        $this->token = $token;
        // Reset the client
        unset($this->client);
        $this->client = null;
        $this->initializeClient();
    }

    public function getAuthorizeUrl($scopes, string $redirectUrl, string $nonce, $onlineAccessMode = false)
    {
        if (is_string($scopes)) {
            $scopes = [$scopes];
        }

        $args = [
            'client_id' => $this->api_key,
            'scope' => implode(',', $scopes),
            'redirect_uri' => $redirectUrl,
            'state' => $nonce,
        ];

        if ($onlineAccessMode) {
            $args['grant_options[]'] = 'per-user';
        }

        return "https://{$this->shop_domain}/admin/oauth/authorize?" . http_build_query($args);
    }

    public function authorizeApplication(string $nonce, $requestData)
    {
        $requiredKeys = ['code', 'hmac', 'state', 'shop'];
        foreach ($requiredKeys as $required) {
            if (!in_array($required, array_keys($requestData))) {
                throw new Exception("The provided request data is missing one of the following keys: " . implode(', ', $requiredKeys));
            }
        }

        if ($requestData['state'] !== $nonce) {
            throw new Exception("The provided nonce ($nonce) did not match the nonce provided by Shopify ({$requestData['state']})");
        }

        if (!filter_var($requestData['shop'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            throw new Exception("The shop provided by Shopify ({$requestData['shop']}) is an invalid hostname.");
        }

        if ($requestData['shop'] !== $this->shop_domain) {
            throw new Exception("The shop provided by Shopify ({$requestData['shop']}) does not match the shop provided to this API ({$this->shop_domain})");
        }

        $hmacSource = [];
        foreach ($requestData as $key => $value) {
            // Skip the hmac key
            if ($key === 'hmac') {
                continue;
            }

            $valuePatterns = [
                '&' => '%26',
                '%' => '%25',
            ];
            $keyPatterns = array_merge($valuePatterns, ['=' => '%3D']);
            $key = str_replace(array_keys($keyPatterns), array_values($keyPatterns), $key);
            $value = str_replace(array_keys($valuePatterns), array_values($valuePatterns), $value);

            $hmacSource[] = $key . '=' . $value;
        }

        sort($hmacSource);
        $hmacBase = implode('&', $hmacSource);
        $hmacString = hash_hmac('sha256', $hmacBase, $this->secret);

        // Verify that the signatures match
        if ($hmacString !== $requestData['hmac']) {
            throw new Exception("The HMAC provided by Shopify ({$requestData['hmac']}) doesn't match the HMAC verification ($hmacString).");
        }

        try {
            $response = $this->client->request('POST', 'admin/oauth/access_token', [
                'body' => json_encode([
                    'client_id' => $this->api_key,
                    'client_secret' => $this->secret,
                    'code' => $requestData['code'],
                ]),
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
        } catch (Exception $e) {
            die($e->getResponse()->getBody());
        }

        $data = json_decode($response->getBody());

        $this->setToken($data->access_token);

        return $data;
    }

    public function call(string $method, string $endpoint, $params = [])
    {
        $method = strtoupper($method);
        $options = [];

        if (empty($this->token)) {
            $options['headers']['Authorization'] = 'Basic ' . base64_encode($this->api_key . ':' . $this->secret);
        }

        switch ($method) {
            case 'GET':
            case 'DELETE':
                $options['query'] = $params;
                break;

            case 'PUT':
            case 'POST':
                $options['body'] = json_encode($params);
                $options['headers']['Content-Type'] = 'application/json';
                break;
        }

        $response = $this->client->request($method, $endpoint, $options);
        $this->last_response_headers = $response->getHeaders();

        return json_decode($response->getBody());
    }

    public function getCallsMade()
    {
        return $this->getCallLimitHeaderValue()[0];
    }

    public function getCallLimit()
    {
        return $this->getCallLimitHeaderValue()[1];
    }

    public function getCallsRemaining()
    {
        return $this->getCallLimit() - $this->getCallsMade();
    }

    protected function getCallLimitHeaderValue()
    {
        if (!$this->last_response_headers) {
            throw new Exception("Call limits can't be polled before a request has been made.");
        }

        return explode('/', $this->last_response_headers['X-Shopify-Shop-Api-Call-Limit'][0]);
    }
}
