<?php

namespace FDTStudio\UploadExtOnedrive\Listeners;

use FoF\Upload\Events\Adapter\Instantiate;
use FDTStudio\UploadExtOnedrive\Adapters\OnedriveFofAdapter;
use FDTStudio\UploadExtOnedrive\Configuration\OnedriveConfiguration;

class AdapterInstantiateListener
{
    protected OnedriveConfiguration $config;

    public function __construct(OnedriveConfiguration $config)
    {
        $this->config = $config;
    }

    public function handle(Instantiate $event): ?OnedriveFofAdapter
    {
        if ($event->adapter != 'onedrive') {
            return null;
        }

        return new OnedriveFofAdapter($this->config);
    }
}
