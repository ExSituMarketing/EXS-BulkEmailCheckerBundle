<?php

namespace {
    /**
     * Variable published in the global namespace to be shareable by the tested service and the test class.
     *
     * @var string|null
     */
    $curlResult = null;
}

namespace EXS\BulkEmailCheckerBundle\Services {
    /**
     * Mock the native function to avoid the actual curl_exec call in test.
     * Must be in the same namespace as the tested method.
     *
     * {@inheritdoc}
     */
    function curl_exec($ch)
    {
        global $curlResult;

        return $curlResult ?: \curl_exec($ch);
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
            $this->manager = new BulkEmailCheckerManager(
                'Foo123Bar456',
                'http://api-v4.bulkemailchecker2.com/?key=%api_key%&email=%email%'
            );
        }

        public function testValidateValidEmail()
        {
            global $curlResult;

            $curlResult = json_encode([
                'status' => 'passed',
            ]);

            $this->assertTrue($this->manager->validate('foo@bar.baz'));
        }

        public function testValidateInvalidEmail()
        {
            global $curlResult;

            $curlResult = json_encode([
                'status' => 'failed',
            ]);

            $this->assertFalse($this->manager->validate('foo@bar'));
        }

        public function testValidateBadResponse()
        {
            global $curlResult;

            $curlResult = json_encode([]);

            $this->assertFalse($this->manager->validate('foo@bar'));
        }

        /**
         * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
         */
        public function testGetUrlValid()
        {
            $reflection = new \ReflectionClass($this->manager);
            $method = $reflection->getMethod('getUrl');
            $method->setAccessible(true);

            $url = $method->invokeArgs($this->manager, ['foo@bar.baz']);
            $this->assertEquals('http://api-v4.bulkemailchecker2.com/?key=Foo123Bar456&email=foo@bar.baz', $url);

            $this->manager = new BulkEmailCheckerManager('somestring', 'thisisnotavalidurl');

            $method->invokeArgs($this->manager, ['foo@bar.baz']);
        }
    }
}
