<?php

namespace BrauneDigital\GeoBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HasLocation extends Constraint
{
	public $message = 'The entity should have a location.';

	public function validatedBy()
	{
		return HasLocationValidator::class;
	}
	public function getTargets()
	{
		return self::CLASS_CONSTRAINT;
	}


}