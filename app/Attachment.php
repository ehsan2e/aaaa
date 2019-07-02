<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\UploadedFile;
use NovaVoip\Exceptions\SupervisedTransactionException;
use function NovaVoip\supervisedTransaction;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class Attachment extends Model
{
    protected $appends = ['claim_code'];
    protected $casts = ['is_orphan' => 'boolean', 'keep_while_orphan' => 'boolean'];
    protected $fillable = ['disk_name', 'is_orphan', 'keep_while_orphan', 'original_name', 'path', 'size', 'uuid'];
    protected $table = 'attachments';

    public function attachmentable(): MorphTo
    {
        return $this->morphTo('attachmentable');
    }

    public function getClaimCodeAttribute()
    {
        return base64_encode(json_encode(['id' => $this->id, 'uuid' => $this->uuid]));
    }

    public function scopeWhereClaimCodeIn(Builder $query, array $claimCodes)
    {
        $c = 0;
        if (count($claimCodes) > 0) {

            $query->where(function (Builder $query1) use ($claimCodes, &$c) {
                foreach ($claimCodes as $claimCode) {
                    $claimCode = json_decode(base64_decode($claimCode));
                    if (isset($claimCode->id, $claimCode->uuid)) {
                        $query1->{$c === 0 ? 'where' : 'orWhere'}(function (Builder $query2) use ($claimCode) {
                            $query2->where('id', $claimCode->id)->where('uuid', $claimCode->uuid);
                        });
                        $c++;
                    }
                }
            });
        }
        if($c === 0){
            $query->where('id', -1);
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @param User|null $user
     * @param UploadedFile $file
     * @param string $path
     * @return Attachment|null
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    public static function createNewAttachment(?User $user, UploadedFile $file, string $path): ?Attachment
    {
        return supervisedTransaction(function () use ($file, $path, $user): ?Attachment {
            $hash = $file->hashName();
            $path .= DIRECTORY_SEPARATOR . substr($hash, 0, 2) . DIRECTORY_SEPARATOR . substr($hash, 2, 1);
            $data = [
                'disk_name' => $hash,
                'is_orphan' => true,
                'keep_while_orphan' => false,
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'uuid' => Uuid::uuid4()->toString(),
            ];
            $instance = new Attachment($data);
            if ($user) {
                $instance->user()->associate($user);
            }
            if (!$instance->save()) {
                return null;
            }

            try {
                $file->move($path, $hash);
                return $instance;
            } catch (FileException $fileException) {
                throw new SupervisedTransactionException('Could not move the file', 0, $fileException);
            }
        }, null, false, false);
    }

    /**
     * @param Model $model
     * @param array $claimCodes
     * @return int
     */
    public static function claim(Model $model, array $claimCodes): int
    {
        return Attachment::whereClaimCodeIn($claimCodes)->update([
            'attachmentable_type' => get_class($model),
            'attachmentable_id' => $model->id,
            'is_orphan' => false,
        ]);
    }
}
