<?php
namespace common\models\file\drivers;

use common\models\file\ar\File;

/**
 *
 */
interface SaveMediumInterface
{
    public function save(File $file);
    public function buildFileUrl(File $file, $params = []);
}
