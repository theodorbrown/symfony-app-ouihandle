<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadService
{
    public function __construct(private SluggerInterface $slugger)
    {}

    public function uploadImage($pic, $destinationFolder)
    {
        $originalFilename = pathinfo($pic->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$pic->guessExtension();
        try {
            $pic->move($destinationFolder, $newFilename);
        } catch (FileException $e) {}

        return $newFilename;
    }

}