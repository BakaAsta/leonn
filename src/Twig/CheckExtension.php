<?php
namespace App\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class CheckExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('is_date_time', [$this, 'isDateTime']),
            new TwigFunction('is_list', [$this, 'isList']),
        ];
    }

    public function isDateTime($variable)
    {
        if(($variable instanceof \DateTimeInterface) or ($variable instanceof \DateTime)){
            return true;
        } else {
            return false;
        }
    }

    public function isList($variable)
    {
        if(($variable instanceof Collection) or ($variable instanceof ArrayCollection)){
            return true;
        } else {
            return false;
        }
    }
}