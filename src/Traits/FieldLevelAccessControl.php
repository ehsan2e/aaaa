<?php

namespace NovaVoip\Traits;


use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use function NovaVoip\supervisedTransaction;

trait FieldLevelAccessControl
{
    protected static $roleAccess = [];

    /**
     * @return array
     */
    abstract protected function modelRelations(): array;

    /**
     * @return array
     */
    protected function getBooleanFields(): array
    {
        $fields = [];
        foreach ($this->casts ?? [] as $field => $cast) {
            if ($cast === 'boolean') {
                $fields[] = $field;
            }
        }
        return $fields;
    }

    public function preparePartialData(User $user, array $data): array
    {
        $booleanFields = $this->getBooleanFields();
        $accessibleFields = $user->getModelAccessibleFields(get_class());
        $accessibleRelations = $user->getModelAccessibleRelations(get_class());

        if (($accessibleFields[0] ?? '') === '*') {
            $accessibleFields = $this->fillable;
        }

        $accessibleBooleanFields = array_intersect($booleanFields, $accessibleFields);

        $relations = (($accessibleRelations[0] ?? '') === '*') ? $this->modelRelations() : Arr::only($this->modelRelations(), $accessibleRelations);
        $partialData = array_merge(
            array_combine(array_map(function ($relation) {
                return $relation[0];
            }, $relations), array_map(function ($relation) {
                return (($relation[2] ?? BelongsTo::class) === BelongsTo::class) ? null : [];
            }, $relations)),
            array_combine($accessibleFields, array_fill(0, count($accessibleFields), null)),
            array_combine($accessibleBooleanFields, array_fill(0, count($accessibleBooleanFields), false)));
        foreach ($partialData as $key => $value) {
            $partialData[$key] = isset($data[$key]) ? (in_array($key, $accessibleBooleanFields) ? ((bool)$data[$key]) : $data[$key]) : $value;
        }

        return $partialData;
    }

    /**
     * @param array $data
     * @param array $extra
     * @return bool
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public function managedUpdateInfo(array $data, array $extra = []): bool
    {
        return supervisedTransaction(function () use ($data, $extra): bool {
            $modelRelations = $this->modelRelations();
            foreach ($data as $key => $value) {
                $possibleRelationName = Str::camel($key);
                if (isset($modelRelations[$possibleRelationName])) {
                    $this->{$possibleRelationName}()->sync($value);
                } else {
                    $this->{$key} = $value;
                }
            }
            foreach ($extra as $key => $value) {
                $this->{$key} = $value;
            }
            return $this->save();
        }, false, true, false);
    }
}