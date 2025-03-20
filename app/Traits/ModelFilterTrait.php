<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;

trait ModelFilterTrait
{
    /**
     * @var array
     * Ex.: ['name', 'is_active:boolean', 'created_at:datetime', 'id:integer']
     * public static array $filterable = [
     * 'name:contains',
     * 'cpf',
     * 'is_active:boolean',
     * 'role_id:join|model_has_roles,role_id,equals&id,model_id'
     * ];
     */
    public static array $filterable = [];

    private static array $acceptedTypes = [
        'string',
        'datetime',
        'date_range',
        'boolean',
        'integer',
        'equals',
        'contains',
        'join',
        'static-function'
    ];

    /**
     * @throws Exception
     */
    static function filter(array $filters, Builder $builder = null, array $replaceModelFilterable = []): Builder
    {
        $builder = $builder ?? static::query();
        $model = new static;
        $tableName = $model->getTable();

        $filterable = $model::$filterable;
        if (!empty($replaceModelFilterable)) {
            $filterable = $replaceModelFilterable;
        }

        $filterableWithoutType = array_map(function ($f) {
            return explode(':', $f)[0];
        }, $filterable);

        $filters = array_intersect_key($filters, array_flip($filterableWithoutType));

        foreach ($filters as $column => $value) {
            if (is_array($value)) {
                $column = $tableName . '.' . $column;
                $builder->whereIn($column, $value);
            } else {
                // considera o tipo de dado
                foreach ($filterable as $f) {
                    $fKey = explode(':', $f)[0];
                    if ($column !== $fKey) continue;

                    $type = explode(':', $f)[1] ?? 'string';
                    $type = explode('|', $type)[0];

                    if (!in_array($type, self::$acceptedTypes)) {
                        throw new Exception("Tipo de dado inválido: $type", 400);
                    }

                    $column = $tableName . '.' . $column;

                    if ($type == 'static-function') {
                        $staticFunction = explode('|', $f)[1] ?? '';
                        if (!empty($staticFunction)) {
                            $builder = $model::$staticFunction($builder, $value);
                        }
                    } else {
                        switch ($type) {
                            case 'datetime':
                                $builder->whereDate($column, $value);
                                break;
                            case 'date_range':
                                $dateRange = explode(',', $value);
                                if (empty($dateRange[1])) {
                                    $builder->whereDate($column, '>=', $dateRange[0]);
                                } else if (empty($dateRange[0])) {
                                    $builder->whereDate($column, '<=', $dateRange[1]);
                                } else {
                                    $builder->whereDate($column, '>=', $dateRange[0])
                                        ->whereDate($column, '<=', $dateRange[1]);
                                }
                                break;
                            case 'equals':
                            case 'boolean':
                            case 'integer':
                                $builder->where($column, $value);
                                break;
                            case 'contains':
                                $builder->whereRaw("LOWER($column) LIKE ?", ["%" . mb_strtolower($value, 'UTF-8') . "%"]);
                                break;
                            case 'join':
                                $rule = explode('|', $f)[1] ?? '';
                                $ruleData = explode('&', $rule);
                                $ruleToValueData = explode(',', $ruleData[0]);
                                $ruleToModelData = explode(',', $ruleData[1]);
                                $tableDest = $ruleToValueData[0];
                                $columnDest = $ruleToValueData[1];
                                $idLocalColumn = $ruleToModelData[0];
                                $idDestColumn = $ruleToModelData[1];

                                $typeOfSearch = $ruleToValueData[2] ?? 'equals';
                                switch ($typeOfSearch) {
                                    case 'equals':
                                        $builder->whereRaw(
                                            "$idLocalColumn IN (SELECT $idDestColumn FROM
                                            $tableDest WHERE
                                                $columnDest = ?)",
                                            [$value]
                                        );
                                        break;
                                    default:
                                        $builder->whereRaw(
                                            "$idLocalColumn IN (SELECT $idDestColumn FROM
                                            $tableDest WHERE
                                                LOWER($columnDest) like ?)",
                                            ["%" . mb_strtolower($value, 'UTF-8') . "%"]
                                        );
                                        break;
                                }
                                break;
                            default:
                                $builder->whereRaw("LOWER($column) = ?", [mb_strtolower($value, 'UTF-8')]);
                                break;
                        }
                    }
                }
            }
        }
        return $builder;
    }

}
