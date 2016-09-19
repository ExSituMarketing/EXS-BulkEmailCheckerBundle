<?php

namespace EXS\BulkEmailCheckerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class BulkEmailChecker
 *
 * @package EXS\BulkEmailCheckerBundle\Validator\Constraints
 * @Annotation
 */
class BulkEmailChecker extends Constraint
{
    public $message = '"%value%" is not a valid email.';
}
