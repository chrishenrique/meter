<?php

namespace Sarfraznawaz2005\Meter\Tables;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Sarfraznawaz2005\Meter\Models\MeterModel;
use Sarfraznawaz2005\Meter\Type;

class RequestsTable extends Table
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
        return (new MeterModel)->type(Type::REQUEST)->filtered()->orderBy('id', 'desc');
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

            $data['Verb'] = meterBadge($row['content']['method']);
            $data['Path'] = meterWithHtmlTitle($row['content']['uri'], $row['content']['controller_action']);

            $data['Status'] = meterAutoBadge($row['content']['response_status'], [
                'success' => $row['content']['response_status'] < 400,
                'warning' => $row['content']['response_status'] >= 400 && $row['content']['response_status'] < 500,
                'danger' => $row['content']['response_status'] >= 500,
            ]);

            $data['Time'] = $row['content']['duration'] . ' ms';
            $data['Memory'] = $row['content']['memory'] . ' MB';

            $data['Slow'] = meterAutoBadge($row['is_slow'], [
                'secondary' => $row['is_slow'] === 'No',
                'danger' => $row['is_slow'] === 'Yes'
            ]);

            // additional for details button
            $details['Controller'] = $row['content']['controller_action'];
            $details['Middleware'] = $row['content']['middleware'];
            $details['IP'] = $row['content']['ip'];

            $data['More'] = meterCenter(meterDetailsButton($details));

            $transformed[] = $data;
        }

        return $transformed;
    }
}
