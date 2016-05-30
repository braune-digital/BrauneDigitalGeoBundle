<?php

namespace BrauneDigital\GeoBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class HasLocationValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{
		if (!$value->getCity() && !$value->getCountry() && !$value->getState()) {
			$this->context->buildViolation($constraint->message)->addViolation();
		}
	}
}