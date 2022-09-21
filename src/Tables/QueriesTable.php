<?php

namespace Sarfraznawaz2005\Meter\Tables;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Sarfraznawaz2005\Meter\Models\MeterModel;
use Sarfraznawaz2005\Meter\Type;

class QueriesTable extends Table
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
        return (new MeterModel)->type(Type::QUERY)->filtered()->orderBy('id', 'desc');
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

            $data['Query'] = '<div class="meter_sql">' . Str::limit($row['content']['sql'], 80) . '</div>';
            $data['Time'] = $row['content']['time'] . ' ms';

            $data['Slow'] = meterAutoBadge($row['is_slow'], [
                'secondary' => $row['is_slow'] === 'No',
                'danger' => $row['is_slow'] === 'Yes'
            ]);

            // additional for details button
            $details['Query'] = '<div class="meter_sql">' . $row['content']['sql'] . '</div>';
            $details['Connection'] = $row['content']['connection'];
            $details['File'] = $row['content']['file'];
            $details['Line'] = $row['content']['line'];

            $data['More'] = meterCenter(meterDetailsButton($details));

            $transformed[] = $data;
        }

        return $transformed;
    }
}
