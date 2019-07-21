<?php

namespace NovaVoip;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use NovaVoip\Exceptions\SupervisedTransactionException;
use NovaVoip\Helpers\SuperVisedTransactionExecuter;

function sortedLanguages(): array
{
    $languages = config('nova.language');
    uasort($languages, function ($a, $b) {
        return ($a['display'] <=> $b['display']);
    });
    return $languages;
}

/**
 * @param callable $fn
 * @param mixed $errorResult
 * @param bool $rethrow
 * @param bool $silent
 * @param $insight
 * @return bool|mixed
 * @throws Exception
 * @throws SupervisedTransactionException
 */
function supervisedTransaction(callable $fn, $errorResult = false, bool $rethrow = false, bool $silent = false, &$insight = false)
{
    try {
        $result = new \stdClass();
        $result->succeed = $errorResult;
        $result->bag = new \stdClass();
        $result->bag->message = null;
        $result->bag->normal = true;
        DB::transaction(function () use ($fn, $result) {
            $result->succeed = $fn($result->bag);
        });
        return is_a($result->succeed, SuperVisedTransactionExecuter::class) ? $result->succeed(): $result->succeed;
    } catch (QueryException | SupervisedTransactionException $xException) {
        $result->bag->normal = false;
        $result->bag->premtionType = get_class($xException);
        $result->bag->exception = $xException;
        logger($xException->getMessage());
        if ($rethrow) {
            throw $xException;
        }
        return is_a($errorResult, SuperVisedTransactionExecuter::class)? $errorResult(): $errorResult;
    } catch (Exception $exception) {
        $result->bag->normal = false;
        $result->bag->premtionType = SupervisedTransactionException::class;
        $result->bag->exception = $exception;
        logger($exception);
        if (!$silent) {
            throw $exception;
        }
        return is_a($errorResult, SuperVisedTransactionExecuter::class)? $errorResult(): $errorResult;
    } finally {
        $insight = $result->bag;
    }
}

/**
 * @param array $dictionary
 * @param bool $isBakend
 * @param string $default
 * @return string
 */
function extractTranslationFromAssociativeArray(array $dictionary, bool $isBakend = false, string $default = ''): string
{
    $locale = app()->getLocale();
    $fallbackLocale = config('app.fallback_locale');
    if ($isBakend) {
        return empty($dictionary['backend']) ? $default : $dictionary['backend'];
    }

    return empty($dictionary[$locale]) ? (empty($dictionary[$fallbackLocale]) ? $default : $dictionary[$fallbackLocale]) : $dictionary[$locale];
}

/**
 * @param object $dictionary
 * @param bool $isBakend
 * @param string $default
 * @return string
 */
function extractTranslationFromObject($dictionary, bool $isBakend = false, string $default = ''): string
{
    $locale = app()->getLocale();
    $fallbackLocale = config('app.fallback_locale');
    if ($isBakend) {
        return empty($dictionary->backend) ? $default : $dictionary->backend;
    }

    return empty($dictionary->{$locale}) ? (empty($dictionary->{$fallbackLocale}) ? $default : $dictionary->{$fallbackLocale}) : $dictionary->{$locale};
}

/**
 * @param array|object|null $entity
 * @param string $key
 * @param string|null $translationsKey
 * @param bool $isBakend
 * @param string $default
 * @param callable|null $dictionaryGenerator
 * @return string|null
 */
function translateEntity($entity, string $key = 'name', string $translationsKey = null, bool $isBakend = false, string $default = '', callable $dictionaryGenerator=null): ?string
{
    if(is_null($entity)){
        return null;
    }
    $translationsKey = $translationsKey ?? ($key . '_translations');
    if (is_array($entity)) {
        $dictionary = !isset($entity[$translationsKey]) ? [] : (isset($dictionaryGenerator) ? $dictionaryGenerator($entity[$translationsKey]) : $entity[$translationsKey]);
        if (!isset($entity[$translationsKey])) {
            return empty($entity[$key]) ? $default : $entity[$key];
        } elseif (is_array($dictionary)) {
            return extractTranslationFromAssociativeArray($dictionary, $isBakend, empty($entity[$key]) ? $default : $entity[$key]);
        } elseif (is_object($dictionary)) {
            return extractTranslationFromObject($dictionary, $isBakend, empty($entity[$key]) ? $default : $entity[$key]);
        } else {
            throw new \RuntimeException('Dictionary is not of valid type');
        }
    } elseif (is_object($entity)) {
        $dictionary = !isset($entity->{$translationsKey}) ? [] : (isset($dictionaryGenerator) ? $dictionaryGenerator($entity->{$translationsKey}) : $entity->{$translationsKey});
        if (!isset($entity->{$translationsKey})) {
            return empty($entity->{$key}) ? $default : $entity->{$key};
        } elseif (is_array($dictionary)) {
            return extractTranslationFromAssociativeArray($dictionary, $isBakend, empty($entity->{$key}) ? $default : $entity->{$key});
        } elseif (is_object($dictionary)) {
            return extractTranslationFromObject($dictionary, $isBakend, empty($entity->{$key}) ? $default : $entity->{$key});
        } else {
            throw new \RuntimeException('Dictionary is not of valid type');
        }
    }
    throw new \InvalidArgumentException('Entity can be either array or object');
}