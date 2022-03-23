<?php

namespace CuriousInc\FileUploadFormTypeBundle\Service;

use Oneup\UploaderBundle\Uploader\Orphanage\OrphanageManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Cache.
 */
class CacheHelper
{
    /**
     * @var \Oneup\UploaderBundle\Uploader\Orphanage\OrphanageManager
     */
    private $om;

    /**
     * CacheHelper constructor.
     *
     * @param \Oneup\UploaderBundle\Uploader\Orphanage\OrphanageManager $om
     */
    public function __construct(OrphanageManager $om)
    {
        $this->om = $om;
    }

    /**
     * Clears all files from session orphanage.
     */
    public function clear(string $folder = null, $objectId)
    {
        $manager = $this->om->get('gallery');
        /** @var Finder $files */
        $files = $manager->getFiles();

        // clear only files for given folder
        if (null !== $folder) {
            $files->filter(function (SplFileInfo $file) use ($folder) {
                return false !== \strpos($file->getRelativePath(), $folder, -\strlen($folder));
            });
        }

        $fs = new Filesystem();

        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            //clear temporary images only for editable object, or all objects without objectid
            if ($objectId === null && preg_split( '/[_.]/', $file)[1] === '' ||
                $objectId !== null && preg_split( '/[_.]/', $file)[1] === (string) $objectId)
            {
                $fs->remove($file);
            }
        }
    }
}
