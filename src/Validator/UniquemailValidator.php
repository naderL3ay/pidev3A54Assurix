<?php

namespace App\Validator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
class UniquemailValidator extends ConstraintValidator
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $value]);

        if ($existingUser) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}