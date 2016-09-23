<?php

namespace EXS\BulkEmailCheckerBundle\Tests\Validator\Constraints;

use EXS\BulkEmailCheckerBundle\Tests\MockifyTrait;
use EXS\BulkEmailCheckerBundle\Validator\Constraints\BulkEmailCheckerValidator;

/**
 * Class BulkEmailCheckerValidatorTest
 *
 * @package EXS\BulkEmailCheckerBundle\Tests\Validator
 */
class BulkEmailCheckerValidatorTest extends \PHPUnit_Framework_TestCase
{
    use MockifyTrait;

    /**
     * @var BulkEmailCheckerValidator
     */
    private $validator;

    public function setUp()
    {
        $bulkEmailCheckerManagerMock = $this->mockify('EXS\BulkEmailCheckerBundle\Services\BulkEmailCheckerManager', [
            ['method' => 'validate', 'parameters' => ['foo@bar.baz'], 'result' => false],
        ]);

        $this->validator = new BulkEmailCheckerValidator($bulkEmailCheckerManagerMock);
    }

    public function testValidate()
    {
        $bulkEmailCheckerMock = $this->mockify('EXS\BulkEmailCheckerBundle\Validator\Constraints\BulkEmailChecker');
        $bulkEmailCheckerMock->message = '"%value%" is not a valid email.';

        $violationMock = $this->mockify('Symfony\Component\Validator\Violation\ConstraintViolationBuilder', [
            ['method' => 'setParameter', 'parameters' => ['%value%', 'foo@bar.baz'], 'result' => '__self'],
            ['method' => 'addViolation'],
        ]);

        $executionContextMock = $this->mockify('Symfony\Component\Validator\Context\ExecutionContext', [
            ['method' => 'buildViolation', 'parameters' => [$bulkEmailCheckerMock->message], 'result' => $violationMock],
        ]);

        $this->validator->initialize($executionContextMock);

        $this->validator->validate('foo@bar.baz', $bulkEmailCheckerMock);
    }
}
