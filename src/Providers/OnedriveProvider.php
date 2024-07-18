<?php

namespace FDTStudio\UploadExtOnedrive\Providers;

use Flarum\Foundation\AbstractServiceProvider;
use FoF\Upload\Helpers\Util;
use FDTStudio\UploadExtOnedrive\Configuration\OnedriveConfiguration;
use FDTStudio\UploadExtOnedrive\Templates\OnedriveAudioTemplate;
use FDTStudio\UploadExtOnedrive\Templates\OnedriveDownloadTemplate;
use FDTStudio\UploadExtOnedrive\Templates\OnedrivePdfTemplate;
use FDTStudio\UploadExtOnedrive\Templates\OnedrivePreviewTemplate;
use FDTStudio\UploadExtOnedrive\Templates\OnedriveVideoTemplate;

class OnedriveProvider extends AbstractServiceProvider
{
    public function register()
    {
        $this->container->singleton(OnedriveConfiguration::class);

        /** @var Util $util */
        $util = $this->container->make(Util::class);

        //$util->addRenderTemplate($this->container->make(OnedriveAudioTemplate::class));
        $util->addRenderTemplate($this->container->make(OnedriveVideoTemplate::class));
        $util->addRenderTemplate($this->container->make(OnedrivePreviewTemplate::class));
        $util->addRenderTemplate($this->container->make(OnedriveDownloadTemplate::class));
        //$util->addRenderTemplate($this->container->make(OnedrivePdfTemplate::class));
    }
}
