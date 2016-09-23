<?php

namespace EXS\BulkEmailCheckerBundle\Tests;

/**
 * Trait MockifyTrait.
 *
 * @package EXS\Rabbits\Web\CommonBundle\Tests
 * @author  Olivier Maisonneuve <olivierm@ex-situ.com>
 */
trait MockifyTrait
{
    /**
     * @param string $class
     * @param array  $calls
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     *
     * @throws \Exception
     */
    public function mockify($class, array $calls = array())
    {
        /** @var \PHPUnit_Framework_TestCase $this */
        if (!$this instanceof \PHPUnit_Framework_TestCase) {
            throw new \Exception(sprintf(
                'Class "%s" does not extends "\PHPUnit_Framework_TestCase".',
                get_class($this)
            ));
        }

        $methods = $this->validateMockifyParameters($class, $calls);

        $mock = $this
            ->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->disableProxyingToOriginalMethods()
            ->setMethods($methods)
            ->getMock()
        ;

        foreach ($calls as $index => $call) {
            $mockCall = $mock
                ->expects($this->at($index))
                ->method($call['method'])
            ;

            if (isset($call['parameters'])) {
                $methodWith = new \ReflectionMethod($mockCall, 'with');
                $args = is_array($call['parameters']) ? $call['parameters'] : array($call['parameters']);

                $that = $this;
                $args = array_map(function ($arg) use ($that) {
                    return $that->equalTo($arg);
                }, $args);

                $methodWith->invokeArgs($mockCall, $args);
            }

            if (isset($call['result'])) {
                if ('__self' === $call['result']) {
                    $mockCall->will($this->returnValue($mock));
                } elseif ($call['result'] instanceof \Exception) {
                    $mockCall->will($this->throwException($call['result']));
                } else {
                    $mockCall->will($this->returnValue($call['result']));
                }
            }
        }

        return $mock;
    }

    /**
     * @param string $class
     * @param array  $calls
     *
     * @return array
     *
     * @throws \Exception
     */
    private function validateMockifyParameters($class, array $calls)
    {
        if (!class_exists($class)) {
            throw new \Exception(sprintf('Class "%s" does not exists.', $class));
        }

        $methods = array();
        foreach ($calls as $call) {
            if (!array_key_exists('method', $call)) {
                throw new \Exception('Invalid $calls.');
            }

            if (!in_array($call['method'], $methods)) {
                $methods[] = $call['method'];
            }
        }

        return $methods;
    }
}
