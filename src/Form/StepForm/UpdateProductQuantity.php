<?php

namespace App\Form\StepForm;

use App\Form\CreateProduitConfirmationType;
use App\Form\CreateProduitStep1Type;
use App\Form\CreateProduitStep2Type;
use App\Form\CreateProduitStep3Type;
use App\Form\CreateProduitStep4Type;
use App\Form\CreateProduitStep5Type;
use App\Form\ProduitQuantiteType;
use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

class UpdateProductQuantity extends FormFlow
{
    protected function loadStepsConfig()
    {
        return [
            [
                'label' => 'Quantité',
                'form_type' => ProduitQuantiteType::class,
            ],
            [
				'label' => 'Confirmation',
			],
        ];
    }

}