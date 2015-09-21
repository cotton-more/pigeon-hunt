<?php namespace App\Pigeon;


use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

class Files extends Model
{
    protected $table = 'files';

    protected $dates = [
        'modified_at',
    ];

    public $timestamps = false;

    public function updateContent(SplFileInfo $fileInfo)
    {
        $pathname = $this->getAttribute('pathname');
        $process = new Process(
            "diff {$pathname} {$fileInfo->getPathname()}",
            storage_path('pigeon')
        );
        $process->run();

        $output = $process->getOutput();

        preg_match_all('/^>\s*(.*)/mix', $output, $matches);

        if ($matches) {
            $newText = implode("\n", $matches[1]);
            app('files')->append(storage_path('pigeon').'/'.$pathname, "\n".$newText);
            $this->setAttribute('modified_at', $fileInfo->getMTime());
        }
    }
}