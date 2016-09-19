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
    private $enabled;

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
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->enabled = isset($config['enabled']) ? (bool) $config['enabled'] : false;
        $this->apiKey = isset($config['api_key']) ? (string) $config['api_key'] : '';
        $this->apiUrl = isset($config['api_url']) ? (string) $config['api_url'] : '';
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function validate($email)
    {
        if (true !== $this->enabled) {
            return true;
        }

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
