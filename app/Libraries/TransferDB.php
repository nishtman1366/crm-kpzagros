<?php

namespace App\Libraries;

use App\Models\Profiles\Profile;
use Illuminate\Database\Eloquent\Model;

class TransferDB
{
    private array $data;
    private array $tables;
    const _CHUNK_SIZE = 1000;
    /**
     * @var Model[]
     */
    private array $models;

    public static function run(array $models)
    {
        $self = new self();
        $self->models = $models;
        $self->fetchData();
        $self->insert();

        print PHP_EOL;
        print 'All Data Successfully Transferred';
    }

    private function fetchData()
    {
        \Illuminate\Support\Facades\DB::setDefaultConnection('server');

        foreach ($this->models as $model) {
            $data = $model::all();
            $m = new $model;
            $this->data[$m->getTable()] = $data;
            print sprintf("Total %s On Server: %s", $m->getTable(), $data->count());
            print PHP_EOL;
        }
        print PHP_EOL;
    }

    private function insert()
    {
        \Illuminate\Support\Facades\DB::setDefaultConnection('temp');
        foreach ($this->models as $model) {
            $m = new $model;
            $table = $m->getTable();
            $totalRows = $this->data[$table]->count();
            print PHP_EOL;
            print sprintf("Migrating \e[32m%s\e[0m...", $table);
            print PHP_EOL;
            $items = collect();
            $progressBar = new ProgressBar($totalRows);
            $this->data[$table]->each(function ($item) use (&$items, $progressBar) {
                print $progressBar->drawCurrentProgress();
                $items->push($item->getOriginal());
            });
            $model::query()->delete();
            \Illuminate\Support\Facades\DB::connection('temp')->raw(sprintf('ALTER TABLE `%s` AUTO_INCREMENT = 1;', $table));
            $itemsChunks = $items->chunk(self::_CHUNK_SIZE);
            $i = 1;
            $itemsChunks->each(function ($items) use (&$i, $model) {
                print sprintf("\e[1m\e[32m\e[40m   %s   \e[m Chunk Inserted", $i * self::_CHUNK_SIZE);
                print PHP_EOL;
                $model::insert($items->toArray());
                $i++;
            });
            print PHP_EOL;
            $insertedRows = $model::count();
            if ($totalRows === $insertedRows) print sprintf("\e[32mAll %s Transferred Successfully\e[0m", $table);
            else  print sprintf("\e[31m%s %s Are Missing\e[0m", $table, ($totalRows - $insertedRows));
            print PHP_EOL;
        }
    }
}
