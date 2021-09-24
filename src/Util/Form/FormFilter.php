<?php

namespace App\Util\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Symfony\Component\Form\Form;

class FormFilter
{
    private ArrayCollection $data;

    private ?Criteria $criteria = null;

    public function __construct(Form $form)
    {
        $this->data = new ArrayCollection();

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form as $field) {
                $this->data->set($field->getName(), $field->getData());
            }
        }
    }

    public function hasCriteria(): bool
    {
        return null !== $this->getCriteria();
    }

    public function getCriteria(): Criteria
    {
        if (null === $this->criteria) {
            $this->criteria = Criteria::create();

            foreach ($this->data->filter(fn($value) => null !== $value) as $property => $value) {
                $this->criteria->andWhere(new Comparison($property, '=', $value));
            }
        }

        return $this->criteria;
    }
}
