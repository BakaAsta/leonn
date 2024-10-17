<?php

namespace App\Form\StepForm;

use App\Form\CreatePretStep1Type;
use App\Form\CreatePretStep2Type;
use App\Form\CreatePretStep3Type;
use Craue\FormFlowBundle\Form\FormFlow;

class CreatePretFlow extends FormFlow
{
    protected function loadStepsConfig()
    {
        return [
            [
                'label' => 'Produits',
                'form_type' => CreatePretStep1Type::class,
            ],
            [
                'label' => 'Utilisateur',
                'form_type' => CreatePretStep2Type::class,
            ],
            [
                'label' => 'Date',
                'form_type' => CreatePretStep3Type::class,
            ],
            [
				'label' => 'Confirmation',
			],
        ];
    }

}