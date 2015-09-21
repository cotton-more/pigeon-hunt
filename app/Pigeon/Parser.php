<?php namespace App\Pigeon;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Finder\SplFileInfo;

class Parser
{
    private $date;

    public function __construct()
    {
        $this->storageDir = storage_path('pigeon');
    }

    public function grind()
    {
        $date = $this->getDate();

        $files = app('files')->allFiles(config('pigeon.dir'));
        $files = array_filter($files, function (SplFileInfo $fileInfo) use ($date) {
            $regex = '@'.$fileInfo->getRelativePath().'/(\d{4}-\d\d-\d\d)\.\d{6}@';
            if (preg_match($regex, $fileInfo->getRelativePathname(), $matches)) {
                if ($matches[1] === $date) {
                    return true;
                }
            }

            return false;
        });

        /** @var SplFileInfo $fileInfo */
        foreach ($files as $fileInfo) {
            $pathname = $fileInfo->getRelativePathname();
            try {
                $file = Files::query()->where('pathname', $pathname)->firstOrFail();
            } catch (ModelNotFoundException $ex) {
                $path = $this->storageDir.'/'.$fileInfo->getRelativePath();
                if (!app('files')->isDirectory($path)) {
                    app('files')->makeDirectory($path, 0766, true);
                }

                $file = new Files;
                $file->setAttribute('pathname', $pathname);
                $file->setAttribute('modified_at', $fileInfo->getMTime());
                $file->save();

                copy($fileInfo->getPathname(), $this->storageDir.'/'.$pathname);
            }

            // if remote file has changed
            if ($fileInfo->getMTime() > $file->getAttribute('modified_at')->getTimestamp()) {
                $file->updateContent($fileInfo);
                $file->save();
            }
        }
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }
}