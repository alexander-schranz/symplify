<?php

namespace Symplify\PHPStanRules\Nette\Tests\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

namespace Symplify\PHPStanRules\Nette\Tests\Rules\DibiMaskMatchesVariableTypeRule\Fixture;

final class SkipValidArray
{
    public function run()
    {
        $params = [
            'i%in' => ['string'],
        ];

        return $params;
    }
}