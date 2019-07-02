@php
    $type=$type ?? 'select-option';
    $inline = $inline ?? false;
@endphp
@switch($type)
    @case('select-option')
    <option value="{{ $value ?? '' }}" @if($value == ($selectedValue ?? '')) selected @endif>{{  $slot }}</option>
    @break
    @case('multi-choice')
    @if($inline)
        <label class="checkbox-inline">
            <input type="checkbox" name="{{ $name }}[]" value="{{ $value ?? '' }}"
                   @if(in_array($value,$selectedValue ?? [])) checked @endif
            >{{ $slot }}</label>
    @else
        <div class="checkbox">
            <label>
                <input type="checkbox" name="{{ $name }}[]" value="{{ $value ?? '' }}"
                       @if(in_array($value,$selectedValue ?? [])) checked @endif
                >{{ $slot }}
            </label>
        </div>
    @endif
    @break
    @default
    @if($inline)
        <label class="radio-inline">
            <input type="radio" name="{{ $name }}" value="{{ $value ?? '' }}"
                   @if($value == ($selectedValue ?? '')) checked @endif
            >{{ $slot }}</label>
    @else
        <div class="radio">
            <label>
                <input type="radio" name="{{ $name }}" value="{{ $value ?? '' }}"
                       @if($value == ($selectedValue ?? '')) checked @endif
                >{{ $slot }}
            </label>
        </div>
    @endif
@endswitch