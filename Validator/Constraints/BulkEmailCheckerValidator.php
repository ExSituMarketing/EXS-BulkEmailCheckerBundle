<?php

namespace EXS\BulkEmailCheckerBundle\Validator\Constraints;

use EXS\BulkEmailCheckerBundle\Services\BulkEmailCheckerManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class BulkEmailChecker
 *
 * @package EXS\BulkEmailCheckerBundle\Validator\Constraints
 */
class BulkEmailCheckerValidator extends ConstraintValidator
{
    /**
     * @var BulkEmailCheckerManager
     */
    private $bulkEmailCheckerManager;

    /**
     * BulkEmailCheckerValidator constructor.
     *
     * @param BulkEmailCheckerManager $bulkEmailCheckerManager
     */
    public function __construct(BulkEmailCheckerManager $bulkEmailCheckerManager)
    {
        $this->bulkEmailCheckerManager = $bulkEmailCheckerManager;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (false === $this->bulkEmailCheckerManager->validate($value)) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->setParameter('%value%', $value)
                ->addViolation()
            ;
        }
    }
}
