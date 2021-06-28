<?php

namespace Sarfraznawaz2005\Meter\Charts;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Balping\JsonRaw\Raw;
use Sarfraznawaz2005\Meter\Models\MeterModel;
use Sarfraznawaz2005\Meter\Monitors\RequestMonitor;
use Sarfraznawaz2005\Meter\Type;

class AccessChart extends Chart
{
    /**
     * Sets options for chart.
     *
     * @return void
     */
    protected function setOptions()
    {
        $this->options([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'title' => [
                'display' => true,
            ],
            'legend' => false,
            'scales' => [
                'yAxes' => [[
                    'ticks' => [
                        'beginAtZero' => true
                    ],
                    'scaleLabel' => [
                        'display' => true,
                        'labelString' => 'Access'
                    ]
                ]],
                'xAxes' => [[
                    'display' => false,
                    //'type' => 'time',
                    'time' => [
                        'displayFormats' => ['hour' => 'MMM D hA'],
                    ],
                    'ticks' => [
                        'beginAtZero' => true,
                        'autoSkip' => true,
                        'autoSkipPadding' => 30,
                        'maxRotation' => 0,
                    ],
                    'gridLines' => ['offsetGridLines' => true],
                    'offset' => true,
                ]]
            ],
            'tooltips' => [
                'callbacks' => [
                    'label' => new Raw('function(item, data) { return "Access: " + data.datasets[item.datasetIndex].data[item.index]}')
                ]
            ],
        ], true);
    }

    /**
     * Sets data for chart.
     *
     * @param MeterModel $model
     * @return void
     */
    protected function setData(MeterModel $model)
    {
        $items = $model->select(DB::raw('route_name, count(route_name) as total'))->type(Type::REQUEST)->filtered()->groupBy('route_name')->get();
        foreach ($items as $item) {
            if($item->route_name)
            {
                $this->data[(string)$item->route_name] = (int)$item->total;
            }
        }
    }

    /**
     * Gets labels for chart.
     *
     * @return mixed
     */
    protected function getLabels(): array
    {
        return array_keys($this->data);
    }

    /**
     * Gets values for chart.
     *
     * @return mixed
     */
    protected function getValues(): array
    {
        return array_values($this->data);
    }

    /**
     * Generates and returns chart
     *
     * @return void
     */
    protected function setDataSet()
    {
        $type = config('meter.monitors.' . RequestMonitor::class . '.graph_type', 'bar');

        $this->dataset('Access by route', $type, $this->getValues())
            ->color('rgb(' . static::COLOR_RED . ')')
            ->options([
                'pointRadius' => 2,
                'fill' => true,
                'lineTension' => 0,
                'borderWidth' => 1,
                //'minBarLength' => 50,
                'barPercentage' => 0.9
            ])
            ->backgroundcolor('rgba(' . static::COLOR_RED . ', 0.6)');
    }

}
