<?php

namespace {
    /**
     * Variables published in the global namespace to be shareable by the tested service and the test class.
     */

    /**
     * @var string|null
     */
    $curlResult = null;

    /**
     * @var bool|null
     */
    $checkdnsrrResult = null;
}

namespace EXS\BulkEmailCheckerBundle\Services {
    /**
     * Mock the natives functions to avoid the actual calls in test.
     * Must be in the same namespace as the tested method.
     */

    /**
     * {@inheritdoc}
     */
    function curl_exec($ch)
    {
        global $curlResult;

        return $curlResult ?: \curl_exec($ch);
    }

    /**
     * {@inheritdoc}
     */
    function checkdnsrr($host, $type = 'MX')
    {
        global $checkdnsrrResult;

        return $checkdnsrrResult ?: \checkdnsrr($host, $type);
    }
}

namespace EXS\BulkEmailCheckerBundle\Tests\Services {
    use EXS\BulkEmailCheckerBundle\Services\BulkEmailCheckerManager;

    /**
     * Class BulkEmailCheckerManagerTest
     *
     * @package EXS\BulkEmailCheckerBundle\Tests\Services
     */
    class BulkEmailCheckerManagerTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @var BulkEmailCheckerManager
         */
        private $manager;

        /**
         * {@inheritdoc}
         */
        protected function setUp()
        {
            $this->manager = new BulkEmailCheckerManager([
                'enabled' => true,
                'pass_on_error' => true,
                'api_key' => 'Foo123Bar456',
                'api_url' => 'http://api-v4.bulkemailchecker2.com/?key=#api_key#&email=#email#',
                'whitelisted_domains' => ['whitelisted.tld'],
                'blacklisted_domains' => ['blacklisted.tld'],
            ]);
        }

        public function testValidate()
        {
            global $curlResult;
            global $checkdnsrrResult;

            $this->assertTrue($this->manager->validate('foo@whitelisted.tld'));

            $this->assertFalse($this->manager->validate('foo@blacklisted.tld'));

            $curlResult = json_encode([
                'status' => 'passed',
            ]);

            $this->assertTrue($this->manager->validate('foo@bar.baz'));

            $curlResult = json_encode([
                'status' => 'unknown',
            ]);

            $this->assertTrue($this->manager->validate('foo@bar.baz'));

            $curlResult = json_encode([
                'status' => 'failed',
            ]);

            $this->assertFalse($this->manager->validate('foo@bar'));

            $curlResult = json_encode([
                'error' => 'There are no validations on the account to verify an email address.',
            ]);

            $this->assertTrue($this->manager->validate('foo@bar'));

            $reflectedManager = new \ReflectionClass($this->manager);

            $reflectedProperty = $reflectedManager->getProperty('passOnError');
            $reflectedProperty->setAccessible(true);
            $reflectedProperty->setValue($this->manager, false);

            $this->assertFalse($this->manager->validate('foo@bar'));

            $curlResult = json_encode([]);

            $this->assertFalse($this->manager->validate('foo@bar'));

            $reflectedProperty = $reflectedManager->getProperty('enabled');
            $reflectedProperty->setAccessible(true);
            $reflectedProperty->setValue($this->manager, false);

            $this->assertTrue($this->manager->validate('foo@bar'));

            $reflectedProperty->setValue($this->manager, true);

            $this->assertTrue($this->manager->validate(''));

            $reflectedProperty = $reflectedManager->getProperty('checkMx');
            $reflectedProperty->setAccessible(true);
            $reflectedProperty->setValue($this->manager, true);

            $checkdnsrrResult = false;

            $this->assertFalse($this->manager->validate('foo@bar.baz'));

            $curlResult = json_encode([
                'status' => 'passed',
            ]);
            $checkdnsrrResult = true;

            $this->assertTrue($this->manager->validate('foo@gooddomain.tld'));
        }

        /**
         * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
         * @expectedExceptionMessage Api url must contains "#api_key#" and "#email#" place holders.
         */
        public function testGetUrl()
        {
            $reflection = new \ReflectionClass($this->manager);
            $method = $reflection->getMethod('getUrl');
            $method->setAccessible(true);

            $url = $method->invokeArgs($this->manager, ['foo@bar.baz']);
            $this->assertEquals('http://api-v4.bulkemailchecker2.com/?key=Foo123Bar456&email=foo@bar.baz', $url);

            $reflectedManager = new \ReflectionClass($this->manager);
            $reflectedProperty = $reflectedManager->getProperty('apiUrl');
            $reflectedProperty->setAccessible(true);
            $reflectedProperty->setValue($this->manager, 'thisisnotavalidurl');

            $method->invokeArgs($this->manager, ['foo@bar.baz']);
        }
    }
}
