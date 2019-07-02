@php $id = uniqid(); $currentFiles=$currentFiles??[]; @endphp
<div class="form-group row uploader" data-name="{{ $name ?? 'attachments' }}" data-number="{{ $number ?? 1 }}"
     data-bounded="{{ ($bounded ?? true) ? 'yes' : 'no' }}" data-counter="{{ count($currentFiles) }}"
     data-confirmation-template="{{ __('Are you sure you want to upload :file', ['file' => '||FILE||']) }}"
     data-unknown-error="{{ __('Upload failed for an unknown reason') }}"
>
    <label for="{{ $id }}" class="col-md-4 col-form-label text-md-right">{{ $label ?? __('Attachments') }}</label>
    <div class="col-md-8">
        <input type="file" id="{{ $id }}" class="form-control">
        @if(($number ?? 1) === 1)
            <div class="help-block">{{ __('Maximum 1 file can be uploaded') }}</div>
        @else
            <div class="help-block">{{ __('Maximum :max files can be uploaded', ['max' => $number]) }}</div>
        @endif
        <div class="files-section">
            @foreach($currentFiles as $currentFile)
                <div class="alert alert-success" data-serial="{{ $loop->index }}">
                    <span>{{ $currentFile->original_name }}</span>
                    <input type="hidden" name="{{ $name ?? 'attachments' }}[]" value="{{ $currentFile->claim_code }}">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endforeach
        </div>
        <div class="errors-section"></div>
    </div>
</div>