<?php
declare(strict_types=1);

namespace MageSuite\MagePalGoogleTagManagerAdcell\Model\DataLayer;

interface DataProviderInterface
{
    const PRODUCT_SEPARATOR = ',';

    public function getData(): array;
}
