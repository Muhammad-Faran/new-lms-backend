<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiFilter
{
    protected $safeParams = [];

    protected $columnMap = [];

    protected $operatorMap = [];

    protected $sortFields = [];

    protected $relation_table = '';

    protected $current_table = '';

    protected $current_table_foreign_key = '';

    protected $searchFields = [];

    protected $searchTableFields = [];

    protected $searchDropdownFields = [];

    protected $relationSearchDropdownFields = [];

    protected $relationSearchFields = [];

    protected $relationSearchTableFields = [];

    protected $nestedRelationSearchFields = [];

    protected $nestedRelationSearchDropdownFields = [];

    protected $columnHeaders = [];

    protected $dateFilterColumn = null;

    const DEFAULT_SORT_FIELD  = 'created_at';
    const DEFAULT_SORT_ORDER  = 'desc';
    const PER_PAGE            = 10;

    public function transform(Request $request)
    {
        $eloQuery = [];

        foreach ($this->safeParams as $param => $operators) {
            $query = $request->query($param);

            if (!isset($query)) {
                continue;
            }

            $column = $this->columnMap[$param] ?? $param;

            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    $eloQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }

        return $eloQuery;
    }

    public function filter($query, $request)
    {

        $query->select(collect($this->columnHeaders)->pluck('selector')->map(function ($selector) {
            return ($selector === 'count') ? \DB::raw('COUNT(*) as count') : $selector;
        })->toArray());

        if ($request->filled('from_date') || $request->filled('to_date')) {
        $this->applyDateFilters($query, $request);
        }

        if ($request->filled('sort_field')) {
            $query = $this->sortFilter($query, $request);
        }

        if ($request->filled('search')) {
            $query = $this->searchFilter($query, $request->search);
        }

        if ($request->filled('search_table')) {
            $query = $this->searchTableFilter($query, $request);
        }

        if ($request->filled('dropdown_filters')) {
            $query = $this->searchDropdownFilter($query, $request);
        }

        $perPage = $request->input('per_page') ?? self::PER_PAGE;

        $request->merge(['headers' => $this->columnHeaders]);

        if ($request->has('pagination') and $request->pagination == true) {
            return $query->paginate($perPage);
        } else {
            return $query->get();
        }
    }

    public function sortFilter($query, $request)
    {
        $sortFieldInput = $request->input('sort_field', self::DEFAULT_SORT_FIELD);
        $sortField      = in_array($sortFieldInput, $this->sortFields) ? $sortFieldInput : self::DEFAULT_SORT_FIELD;

        $sortOrderInput = $request->input('sort_order', self::DEFAULT_SORT_ORDER);
        $sortOrder      = in_array(strtoupper($sortOrderInput), ['ASC', 'DESC']) ? $sortOrderInput : self::DEFAULT_SORT_ORDER;

        if (!empty($this->current_table)) {
            $query = $query->leftJoin($this->relation_table, $this->relation_table.'.id', '=', $this->current_table.'.'.$this->current_table_foreign_key);
        }

        $query = $query->orderByRaw("ISNULL($sortField)")->orderBy($sortField, $sortOrder);

        foreach (['first_name', 'last_name'] as $subSortField) {
            if (in_array($subSortField, $this->sortFields)) {
                $query = $query->orderBy($subSortField, $sortOrder);
            }
        }

        return $query;
    }

    public function searchFilter($query, $search)
    {
        foreach (explode(" ", $search) as $term) {
            $query->where(function ($query) use ($term) {
                foreach ($this->searchFields as $field) {
                    $query->orWhere($field, 'LIKE', '%' . $term . '%');
                }
                foreach ($this->relationSearchFields as $tableField) {
                    [$table, $field] = explode('.', $tableField);
                    $query->orWhereRelation($table, $field, 'LIKE', '%' . $term . '%');
                }
            });
        }

        return $query;
    }

    public function searchTableFilter($query, $request)
    {
        $search = json_decode($request->get('search_table'), true);
        if (count($search) > 0) {
            $searchFields           = $this->searchTableFields;
            $relationSearchFields   = $this->relationSearchTableFields;
            $query = $query->where(function ($query) use ($searchFields, $relationSearchFields, $search) {

                foreach ($search as $item) {
                    if (in_array($item['key'], $searchFields)) {
                        $query->where($item['key'], 'like', '%' . $item['value'] . '%');
                    }
                }

                foreach ($search as $item) {
                    if (in_array($item['key'], $relationSearchFields)) {
                        $relationSearch = explode('.', $item['key']);
                        $query->whereRelation($relationSearch[0], $relationSearch[1], 'like', '%' . $item['value'] . '%');
                    }
                }
            });
        }

        if (count($this->nestedRelationSearchFields) > 0) {
            $totalnestedRelationSearchFields = count($this->nestedRelationSearchFields);
            for ($j = 0; $j < $totalnestedRelationSearchFields; $j++) {
                $relationSearch = explode('.', $this->nestedRelationSearchFields[$j]);
                $key = array_pop($relationSearch);
                $relationSearch = [implode('.', $relationSearch), $key];
                foreach ($search as $value) {
                    if ($value['key'] == $this->nestedRelationSearchFields[$j] && !empty($value)) {
                        $query = $query->whereHas($relationSearch[0], function ($q) use ($relationSearch, $value) {
                            $q->where($relationSearch[1], 'like', '%' . $value['value'] . '%');
                        });
                    }
                }
            }
        }

        return $query;
    }

    public function searchDropdownFilter($query, $request)
    {
        $searchFields  = $this->searchDropdownFields;
        $search        = json_decode($request->dropdown_filters, true);
        $query = $query->where(function ($query) use ($searchFields, $search) {
            foreach ($search as $key => $value) {
                if (in_array($key, $searchFields) && !empty($value)) {
                    $query->where($key, $value);
                }
            }
        });
        if (isset($this->relationDateFilters) && count($this->relationDateFilters) > 0) {
        foreach ($this->relationDateFilters as $key => $relationField) {
            if (!empty($search[$key])) {
                $relationSearch = explode('.', $relationField);
                $query->whereHas($relationSearch[0], function ($q) use ($relationSearch, $search, $key) {
                    $q->whereDate($relationSearch[1], '=', $search[$key]); 
                });
                unset($search[$key]); 
            }
        }
    }

        if (!empty($this->relationSearchDropdownFields)) {
    foreach ($this->relationSearchDropdownFields as $relationField) {
        $relationSearch = explode('.', $relationField);
        if (count($relationSearch) === 2) {
            $relation = $relationSearch[0];  // e.g., "transaction"
            $field = $relationSearch[1];     // e.g., "product_id"

            foreach ($search as $key => $value) {
                if ($key === $relationField && !empty($value)) {
                    $query->whereHas($relation, function ($q) use ($field, $value) {
                        $q->where($field, $value);
                    });
                }
            }
        }
    }
}


        if (count($this->nestedRelationSearchDropdownFields) > 0) {
            $totalNestedRelationSearchDropdownFields = count($this->nestedRelationSearchDropdownFields);
            for ($j = 0; $j < $totalNestedRelationSearchDropdownFields; $j++) {
                $relationSearch = explode('.', $this->nestedRelationSearchDropdownFields[$j]);
                $key = array_pop($relationSearch);
                $relationSearch = [implode('.', $relationSearch), $key];
                foreach ($search as $key => $value) {
                    if ($key == $this->nestedRelationSearchDropdownFields[$j] && !empty($value)) {
                        $query->where(function ($query) use ($relationSearch, $value) {
                            foreach (explode(',', $value) as $value) {
                                $query = $query->orWhereHas($relationSearch[0], function ($q) use ($relationSearch, $value) {
                                    $q->where($relationSearch[1], $value);
                                });
                            }
                        });
                    }
                }
            }
        }

        return $query;
    }

    public function applyDateFilters($query, Request $request, $column = null)
{
    // If no column is explicitly passed, use the filter's default date filter column
    $column = $column ?? $this->dateFilterColumn ?? 'created_at';

    if ($request->filled('from_date')) {
        $query->whereDate($column, '>=', $request->input('from_date'));
    }

    if ($request->filled('to_date')) {
        $query->whereDate($column, '<=', $request->input('to_date'));
    }

    return $query;
}

}
