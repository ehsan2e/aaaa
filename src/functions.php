<?php

namespace NovaVoip;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use NovaVoip\Exceptions\SupervisedTransactionException;

function sortedLanguages(): array
{
    $languages = config('nova.language');
    uasort($languages, function($a, $b){
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
function supervisedTransaction(callable $fn, $errorResult=false, bool $rethrow=false, bool $silent=false, &$insight)
{
    try{
        $result=new \stdClass();
        $result->succeed=$errorResult;
        $result->bag = new \stdClass();
        $result->bag->message = null;
        $result->bag->normal = true;
        DB::transaction(function() use ($fn, $result) {
            $result->succeed = $fn($result->bag);
        });
        return $result->succeed;
    }catch(QueryException | SupervisedTransactionException $xException){
        $result->bag->normal = false;
        $result->bag->premtionType = get_class($xException);
        $result->bag->exception =$xException;
        logger($xException->getMessage());
        if($rethrow){
            throw $xException;
        }
        return $errorResult;
    }catch (Exception $exception){
        $result->bag->normal = false;
        $result->bag->premtionType = SupervisedTransactionException::class;
        $result->bag->exception =$exception;
        logger($exception);
        if(!$silent){
            throw $exception;
        }
        return $errorResult;
    }finally{
        $insight = $result->bag;
    }
}