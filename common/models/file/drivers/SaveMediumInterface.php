<?php
namespace common\models\file\drivers;

use common\models\file\File;

/**
 *
 */
interface SaveMediumInterface
{
    public function save(File $file);
}
