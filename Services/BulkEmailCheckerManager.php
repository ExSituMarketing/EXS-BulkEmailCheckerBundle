<?php

namespace EXS\BulkEmailCheckerBundle\Services;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Class BulkEmailCheckerManager
 *
 * @package EXS\BulkEmailCheckerBundle\Services
 */
class BulkEmailCheckerManager
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * BulkEmailCheckerManager constructor.
     *
     * @param string $apiKey
     * @param string $apiUrl
     */
    public function __construct($apiKey, $apiUrl)
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function validate($email)
    {
        $ch = curl_init($this->getUrl($email));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response, true);

        if (isset($json['status'])) {
            return ('passed' === strtolower($json['status']));
        }

        return false;
    }

    /**
     * @param string $email
     *
     * @return string
     */
    private function getUrl($email)
    {
        if (
            (false === strpos($this->apiUrl, '%api_key%'))
            || (false === strpos($this->apiUrl, '%email%'))
        ) {
            throw new InvalidConfigurationException('Api url must contains "%api_key%" and "%email%" place holders.');
        }

        return strtr($this->apiUrl, [
            '%api_key%' => $this->apiKey,
            '%email%' => $email,
        ]);
    }
}
