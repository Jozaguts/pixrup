<?php

return [
    'default' => 'PRICE_STARTER',
    'tiers' => [
        'PRICE_STARTER' => ['label' => 'Starter', 'limits' => ['docs' => 50, 'renders' => 0]],
        'PRICE_PRO' => ['label' => 'Pro', 'limits' => ['docs' => -1, 'renders' => 20]],
        'PRICE_ENTERPRISE' => ['label' => 'Enterprise', 'limits' => ['docs' => -1, 'renders' => -1]],
    ],
    'aliases' => ['starter'=>'PRICE_STARTER','pro'=>'PRICE_PRO','enterprise'=>'PRICE_ENTERPRISE'],

];

