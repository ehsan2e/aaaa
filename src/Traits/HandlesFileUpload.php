<?php

namespace NovaVoip\Traits;


use App\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait HandlesFileUpload
{
    /**
     * @param Request $request
     * @param string $path
     * @param array $rules
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @throws \NovaVoip\Exceptions\SupervisedTransactionException
     */
    protected function handleFileUpload(Request $request, string $path, array $rules = [])
    {
        $request->validate(['file' => $rules]);
        if (is_null($attachment = Attachment::createNewAttachment(Auth::user(), $request->file('file'), $path))) {
            return response()->json(['message' => __('An unknown error happened please try again later')], 500);
        }

        return response()->json(['claim_code' => $attachment->claim_code]);
    }

    protected function loadCurrentFiles()
    {
        $currentFiles = [];
        $attachments = old('attachments', []);
        if (is_array($attachments) && (count($attachments) > 0)) {
            $currentFiles = Attachment::whereClaimCodeIn($attachments)->get();
        }
        return $currentFiles;
    }
}