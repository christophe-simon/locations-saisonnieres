<?php

namespace App\Form\DataTransformer;

use DateTimeImmutable;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateTimeTransformer implements DataTransformerInterface
{
    /**
     * Enables to transform a \DateTimeImmutable to a string
     *
     * @param \DateTimeImmutable $date The value in the original representation
     * @return string|null The value in the transformed representation, or null if the input is null
     */
    public function transform($date): mixed
    {
        if ($date === null) {
            return '';
        }

        return $date->format('d/m/Y');
    }

    /**
     * Enables to transform a string back to a \DateTimeImmutable.
     *
     * @param string $frenchDate The value in the transformed representation
     * @return \DateTimeImmutable|null The value in the original representation, or null
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($frenchDate): mixed
    {
        if ($frenchDate === null) {
            // exception
            throw new TransformationFailedException('Vous devez fournir une date!');
        }

        $date = DateTimeImmutable::createFromFormat('d/m/Y', $frenchDate);

        if ($date === false) {
            // exception
            throw new TransformationFailedException("Le format de la date n'est pas bon!");
        }

        return $date;
    }
}
