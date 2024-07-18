<?php

namespace FDTStudio\UploadExtOnedrive\Listeners;

use FoF\Upload\Events\Adapter\Collecting;

class AdapterRegisterListener
{
    public function __construct()
    {
    }

    public function handle(Collecting $event)
    {
        $event->adapters['onedrive'] = true;
    }
}
