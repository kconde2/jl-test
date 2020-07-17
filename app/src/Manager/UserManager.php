<?php

namespace App\Manager;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class UserManager
{
    public function validateInput($input)
    {
        $validator = Validation::createValidator();

        $constraint = new Assert\Collection([
            'firstname' => new Assert\Length(['min' => 3]),
            'lastname' => new Assert\Length(['min' => 3]),
        ]);

        $violations = $validator->validate($input, $constraint);

        $errors = [];
        if (0 !== count($violations)) {
            // there are errors, now you can show them
            foreach ($violations as $violation) {
                $key = str_replace(array('[', ']'), '', $violation->getPropertyPath());
                $errors[$key] = $violation->getMessage();
            }
        }

        return $errors;
    }
}
