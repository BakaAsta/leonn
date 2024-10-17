<?php

namespace App\Form\StepForm;

use App\Form\CreateUserStep1Type;
use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

class createUserFlow extends FormFlow
{
    protected function loadStepsConfig()
    {
        return [
            [
                'label' => 'Email',
                'form_type' => CreateUserStep1Type::class,
            ],
            [
				'label' => 'Confirmation',
			],
        ];
    }

}