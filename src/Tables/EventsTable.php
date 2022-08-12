<?php

namespace Sarfraznawaz2005\Meter\Tables;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Sarfraznawaz2005\Meter\Models\MeterModel;
use Sarfraznawaz2005\Meter\Type;

class EventsTable extends Table
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
        return (new MeterModel)->type(Type::EVENT)->filtered()->orderBy('id', 'desc');
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
            $data['Happened'] = meterWithHtmlTitle(Carbon::parse($row['created_at'])->format('d/m/Y H:i:s'), $row['created_at']);

            $data['Event'] = $row['content']['name'];
            $data['Time'] = $row['content']['time'] . ' ms';
            $data['Listeners'] = count($row['content']['listeners']);

            // additional for details button
            $details['Listeners'] = '<pre class="json">' . json_encode($row['content']['listeners'], JSON_PRETTY_PRINT) . '</pre>';
            $details['Payload'] = '<pre class="json">' . json_encode($row['content']['payload'], JSON_PRETTY_PRINT) . '</pre>';
            $details['Broadcast'] = $row['content']['broadcast'];

            $data['More'] = meterCenter(meterDetailsButton($details));

            $transformed[] = $data;
        }

        return $transformed;
    }
}
