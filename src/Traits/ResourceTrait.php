<?php

namespace NormanHuth\SingleResource\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use NormanHuth\SingleResource\SingleResourceSectionField;

trait ResourceTrait
{
    protected static array $sections = [];
    protected static array $ids;
    protected static string $keyName;

    //protected static array $slugs = [];

    public function bootResourceTrait()
    {
        $sections = static::sections();
        $sections = array_reverse($sections, true);

        $need = count($sections);
        $model = app(static::$model);
        $exist = $model->count();

        static::$keyName = $model->getKeyName();
        $keyColumn = !empty($model::$keyColumn) ? $model::$keyColumn : 'key';
        $valueColumn = !empty($model::$valueColumn) ? $model::$valueColumn : 'value';

        /**
         * Create temporary entries if not enough exist
         * */
        if ($need > $exist) {
            $diff = $need - $exist;
            for ($i = 1; $i <= $diff; $i++) {
                $model::create([
                    $keyColumn   => 'single-resource-temp-'.$i,
                    $valueColumn => 'drop'
                ]);
            }
        } else {
            $diff = $exist - $need;
            if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model))) {
                $model::where($keyColumn, 'LIKE', 'single-resource-temp-%')
                    ->where($valueColumn, 'drop')->limit($diff)->forceDelete();
            } else {
                $model::where($keyColumn, 'LIKE', 'single-resource-temp-%')
                    ->where($valueColumn, 'drop')->limit($diff)->delete();
            }
        }

        /**
         * Take and use columns for pseudo
         */
        $ids = static::$ids = static::$model::query()->orderBy(static::$keyName)
            ->take($need)->get()->pluck(static::$keyName)->toArray();

        $i = 0;
        foreach ($sections as $slug => $data) {
            static::$sections[$ids[$i]] = [
                'slug'   => $slug,
                'name'   => $data['name'],
                'icon'   => $data['icon'] ?? null,
                'faIcon' => $data['faIcon'] ?? null,
            ];
            //static::$slugs[$ids[$i]] = $slug;
            unset($ids[$i++]);
        }
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param NovaRequest $request
     * @param Builder $query
     * @return Builder
     */
    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        return $query->whereIn(static::$keyName, static::$ids)->orderBy(static::$keyName);
    }

    /**
     * Build a "detail" query for the given resource.
     *
     * @param NovaRequest $request
     * @param Builder $query
     * @return Builder
     */
    public static function detailQuery(NovaRequest $request, $query): Builder
    {
        return $query->orWhere($request->model()->getKeyName(), '>', 0);
    }

    /**
     * Build an "edit" query for the given resource.
     *
     * @param NovaRequest $request
     * @param Builder $query
     * @return Builder
     */
    public static function editQuery(NovaRequest $request, $query): Builder
    {
        return $query->orWhere($request->model()->getKeyName(), '>', 0);
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param NovaRequest $request
     * @param Builder $query
     * @return Builder
     */
    public static function relatableQuery(NovaRequest $request, $query): Builder
    {
        return parent::relatableQuery($request, $query);
    }

    /**
     * Determine if the current user can create new resources.
     *
     * @param Request $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request): bool
    {
        return false;
    }

    /**
     * Determine if the current user can replicate the given resource.
     *
     * @param Request $request
     * @return bool
     */
    public function authorizedToReplicate(Request $request): bool
    {
        return false;
    }

    /**
     * Determine if the current user can view the given resource.
     *
     * @param Request $request
     * @param string $ability
     * @return bool
     */
    public function authorizedTo(Request $request, $ability): bool
    {
        if (in_array($ability, ['create', 'delete', 'restore', 'forceDelete'])) {
            return false;
        }
        return parent::authorizedTo($request, $ability);
    }

    /**
     * Determine if this resource uses soft deletes.
     *
     * @return bool
     */
    public static function softDeletes(): bool
    {
        return false;
    }

    /**
     * Prepare the resource for JSON serialization using the given fields.
     *
     * @param Collection $fields
     * @return array
     */
    protected function serializeWithId(Collection $fields): array
    {
        $data = ID::forModel($this->resource);

        /**
         * TODO: Dont work on update | Custom Controller?
         * if (!empty(static::$sections[$data->value])) {
         * $data->value = static::$sections[$data->value]['slug'];
         * }
         */

        return [
            'id'     => $data,
            'fields' => $fields->all(),
        ];
    }

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title(): string
    {
        $resourceId = request()->resourceId;
        if (!empty(static::$sections[$resourceId])) {
            return static::$sections[$resourceId]['name'];
        }
        // Wrong ID sometimes
//        if (!empty(static::$sections[$this->id])) {
//            return static::$sections[$this->id]['name'];
//        }
        return parent::title();
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        $resourceId = $request->resourceId;
        if (!empty(static::$sections[$resourceId])) {
            $slug = static::$sections[$resourceId]['slug'];

            $method = 'get'.Str::studly($slug).'Fields';

            return $this->$method($request);
        }

        return [];
    }

    /**
     * Get the fields displayed by the resource on index page.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function fieldsForIndex(NovaRequest $request): array
    {
        if (!$this->id) {
            return [];
        }
        return [
            SingleResourceSectionField::make(static::singularLabel(), function () {
                return static::$sections[$this->id]['name'];
            })->icon(static::$sections[$this->id]['icon'])->faIcon(static::$sections[$this->id]['faIcon']),
        ];
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [];
}
