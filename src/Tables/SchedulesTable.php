<?php

namespace Sarfraznawaz2005\Meter\Tables;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Sarfraznawaz2005\Meter\Models\MeterModel;
use Sarfraznawaz2005\Meter\Type;

class SchedulesTable extends Table
{
    /**
     * Columns to be shown in table.
     *
     * @return array
     */
    public function columns(): array
    {
        return [
            'is_slow',
            'content',
            'created_at',
        ];
    }

    /**
     * Searchable columns in table
     *
     * @return array
     */
    public function searchColumns(): array
    {
        return $this->columns();
    }

    /**
     * Table Query
     *
     * @return Builder
     */
    public function builder(): Builder
    {
        return (new MeterModel)->type(Type::SCHEDULE)->filtered()->orderBy('id', 'desc');
    }

    /**
     * Transform data as we need.
     *
     * @param array $rows
     * @return array
     */
    public function transform(array $rows): array
    {
        $transformed = [];

        foreach ($rows as $row) {
            $date = Carbon::create($row['created_at'])->subHours(3)->format('d/m/Y H:i:s');
            $data['Happened'] = meterWithHtmlTitle($date, $date);

            $data['Command'] = $row['content']['command'];
            $data['Expression'] = meterBadge($row['content']['expression']);
            $data['Time'] = $row['content']['time'] . ' ms';

            // additional for details button
            $details['Description'] = $row['content']['description'];
            $details['Timezone'] = $row['content']['timezone'];
            $details['User'] = $row['content']['user'];
            $details['Output'] = $row['content']['output'] ? '<pre>' . $row['content']['output'] . '</pre>' : 'null';

            $data['More'] = meterCenter(meterDetailsButton($details));

            $transformed[] = $data;
        }

        return $transformed;
    }
}
