<?php

namespace App\Form\StepForm;

use App\Form\CreateProduitConfirmationType;
use App\Form\CreateProduitStep1Type;
use App\Form\CreateProduitStep2Type;
use App\Form\CreateProduitStep3Type;
use App\Form\CreateProduitStep4Type;
use App\Form\CreateProduitStep5Type;
use App\Form\CreateProduitStep6Type;
use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

class CreateProduitFlow extends FormFlow
{
    protected function loadStepsConfig()
    {
        return [
            [
                'label' => 'Nom',
                'form_type' => CreateProduitStep1Type::class,
                'form_options' => [
                ]
                
            ],
            [
                'label' => 'Type',
                'form_type' => CreateProduitStep2Type::class,
            ],
            [
                'label' => 'Précisions',
                'form_type' => CreateProduitStep3Type::class,
            ],
            [
                'label' => 'Quantité / Duplication',
                'form_type' => CreateProduitStep4Type::class,
            ],
            [
                'label' => 'Commentaire',
                'form_type' => CreateProduitStep6Type::class,
            ],
            [
                'label' => 'Référence',
                'form_type' => CreateProduitStep5Type::class,
            ],
            
            [
				'label' => 'Confirmation',
			],
        ];
    }

}