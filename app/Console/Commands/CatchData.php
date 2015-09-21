<?php
/**
 * Created by PhpStorm.
 * User: inikulin
 * Date: 17/09/15
 * Time: 09:43
 */

namespace App\Console\Commands;


use App\Pigeon\Parser as PigeonParser;
use Illuminate\Console\Command;

class CatchData extends Command
{
    protected $signature = 'catch-data
                            {--date= : Date to catch}';
    protected $description = 'Get data from logs';

    public function handle()
    {
        $date = $this->option('date') ?: date('Y-m-d');

        /** @var PigeonParser $pigeonParser */
        $pigeonParser = app('pigeon.parser');
        $pigeonParser->setDate($date);

        $pigeonParser->grind();
    }
}