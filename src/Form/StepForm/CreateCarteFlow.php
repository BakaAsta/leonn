<?php

namespace App\Form\StepForm;

use App\Form\CreateCarteStep1Type;
use App\Form\CreateCarteStep2Type;
use App\Form\CreatePretStep1Type;
use App\Form\CreatePretStep2Type;
use App\Form\CreatePretStep3Type;
use Craue\FormFlowBundle\Form\FormFlow;

class CreateCarteFlow extends FormFlow
{
    protected function loadStepsConfig()
    {
        return [
            [
                'label' => 'Valeur',
                'form_type' => CreateCarteStep1Type::class,
            ],
            [
				'label' => 'Confirmation',
			],
        ];
    }

}