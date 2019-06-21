<template>
    <component :is="editing ? 'div' : 'tr'">
        <template v-if="editing">
            <h6>Customizing {{ value.caption || value.name }}</h6>
            <template v-if="showLookupValueTranslateCaptions">
                <h6>Translate {{ lookupValues[activeLookupValue].caption || '??' }}</h6>
                <div v-for="(val, key) in lookupValues[activeLookupValue].captions" :key="key"
                     class="form-group row">
                    <label :for="'caption' + key" class="col-md-4 col-form-label text-md-right">{{ key }}</label>
                    <div class="col-md-6">
                        <input :id="'caption' + key" type="text" class="form-control"
                               :value="lookupValues[activeLookupValue].captions[key]"
                               @input="emitValue('lookupValues.' + activeLookupValue + '.captions.' + key ,$event.target.value)">
                    </div>
                </div>
                <button type="button" class="btn btn-success btn-block"
                        @click.prevent="endLookupValueCaptionTranslation">Ok
                </button>
            </template>
            <template v-else>
                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>
                    <div class="col-md-6">
                        <input id="name" type="text" class="form-control" :value="value.name"
                               @input="emitValue('name',$event.target.value)"
                               :class="{'is-invalid': errors['custom_attributes.'+index+'.name']}">
                        <span class="invalid-feedback" role="alert" v-if="errors['custom_attributes.'+index+'.name']">
                            <strong>{{ errors['custom_attributes.'+index+'.name'][0] }}</strong>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="caption" class="col-md-4 col-form-label text-md-right">Caption</label>
                    <div class="col-md-6">
                        <input id="caption" type="text" class="form-control" :value="value.caption"
                               @input="emitValue('caption',$event.target.value)"
                               :class="{'is-invalid': errors['custom_attributes.'+index+'.caption']}">
                        <span class="invalid-feedback" role="alert"
                              v-if="errors['custom_attributes.'+index+'.caption']">
                            <strong>{{ errors['custom_attributes.'+index+'.caption'][0] }}</strong>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6 offset-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="required"
                                   @input="emitValue('required', !value.required)" :value="value.required"
                                   :checked="value.required">
                            <label class="form-check-label" for="required">Required</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="type" class="col-md-4 col-form-label text-md-right">Type</label>
                    <div class="col-md-6">
                        <select id="type" class="form-control" :value="value.type"
                                @input="emitValue('type',$event.target.value)"
                                :class="{'is-invalid': errors['custom_attributes.'+index+'.type']}">
                            <option v-for="(caption, value) in types" :value="value" :key="value">{{ caption }}</option>
                        </select>
                        <span class="invalid-feedback" role="alert" v-if="errors['custom_attributes.'+index+'.type']">
                            <strong>{{ errors['custom_attributes.'+index+'.type'][0] }}</strong>
                        </span>
                    </div>
                </div>
                <template v-if="value.type==='lookup'">
                    <div class="my-5">
                        <table class="table table-striped">
                            <thead>
                            <th>Value</th>
                            <th>Caption</th>
                            <th>Action</th>
                            </thead>
                            <tbody>
                            <tr v-if="errors['custom_attributes.'+index+'.lookupValues'] && lookupValues.length>0">
                                <td colspan="3">
                                    <div class="alert alert-danger" role="alert">
                                        <strong>{{ errors['custom_attributes.'+index+'.lookupValues'][0] }}</strong>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="(itemv, indexv) in lookupValues" :key="'lookupvalu-'+indexv">
                                <td>
                                    <input type="text" class="form-control" :value="itemv.value"
                                           :class="{'is-invalid': errors['custom_attributes.'+index+'.lookupValues.'+indexv+'.value']}"
                                           @input="emitValue('lookupValues.' + indexv + '.value',$event.target.value)">
                                    <span class="invalid-feedback" role="alert"
                                          v-if="errors['custom_attributes.'+index+'.lookupValues.'+indexv+'.value']">
                                        <strong>{{ errors['custom_attributes.'+index+'.lookupValues.'+indexv+'.value'][0] }}</strong>
                                    </span>
                                </td>
                                <td>
                                    <input type="text" class="form-control" :value="itemv.caption"
                                           :class="{'is-invalid': errors['custom_attributes.'+index+'.lookupValues.'+indexv+'.caption']}"
                                           @input="emitValue('lookupValues.' + indexv + '.caption',$event.target.value)">
                                    <span class="invalid-feedback" role="alert"
                                          v-if="errors['custom_attributes.'+index+'.lookupValues.'+indexv+'.caption']">
                                        <strong>{{ errors['custom_attributes.'+index+'.lookupValues.'+indexv+'.caption'][0] }}</strong>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary"
                                            @click.prevent="startLookupValueCaptionTranslation(indexv)">Translate
                                        captions
                                    </button>
                                    <button type="button" class="btn btn-danger"
                                            @click.prevent="removeLookupValue(indexv)">remove
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="lookupValues.length===0" key="no-lookupvalue">
                                <td class="text-center" colspan="3">
                                    <div class="alert alert-danger" role="alert"
                                         v-if="errors['custom_attributes.'+index+'.lookupValues']">
                                        <strong>{{ errors['custom_attributes.'+index+'.lookupValues'][0] }}</strong>
                                    </div>
                                    <div v-else>No value available</div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-primary btn-block" @click="addValueToLookup">Add value
                        </button>
                    </div>
                </template>
                <div class="mb-5">
                    <a href="#" v-show="!showTranslateCaptions"
                       @click="showTranslateCaptions = !showTranslateCaptions;">Show caption translation</a>
                    <div v-show="showTranslateCaptions" ref="translateCaptionSection">
                        <h6>Caption Translations</h6>
                        <div v-for="(val, key) in value.captions" :key="key" class="form-group row">
                            <label :for="'caption' + key" class="col-md-4 col-form-label text-md-right">{{ key
                                }}</label>
                            <div class="col-md-6">
                                <input :id="'caption' + key" type="text" class="form-control"
                                       :value="value.captions[key]"
                                       @input="emitValue('captions.' + key ,$event.target.value)">
                            </div>
                        </div>
                    </div>
                    <a href="#" v-show="showTranslateCaptions" @click="showTranslateCaptions = !showTranslateCaptions">Hide
                        caption translation</a>
                </div>
                <button type="button" class="btn btn-success btn-block" @click.prevent="endEditing">Ok</button>
            </template>
        </template>
        <template v-else>
            <td><i class="fa fa-warning text-danger" v-if="hasError"></i>{{ value.name}}</td>
            <td>{{ value.caption}}</td>
            <td>{{ typeLabel}}</td>
            <td>
                <button type="button" class="btn btn-primary" @click.prevent="startEditing">Edit</button>
                <button type="button" class="btn btn-danger" @click.prevent="remove">Delete</button>
            </td>
        </template>
    </component>
</template>

<script>
    export default {
        name: 'value-component',
        data() {
            return {
                activeLookupValue: -1,
                showTranslateCaptions: false,
                showLookupValueTranslateCaptions: false,
                types: {
                    boolean: 'Boolean',
                    decimal: 'Decimal',
                    html: 'Html',
                    integer: 'Integer',
                    lookup: 'Lookup',
                    picture: 'Picture',
                    price: 'Price',
                    string: 'String',
                    text: 'Text',
                }
            };
        },
        computed: {
            hasError() {
                return this.errors['custom_attributes.' + this.index] || new RegExp('custom_attributes\\.' + this.index + '\\.').test(Object.keys(this.errors).join('|'));
            },
            lookupValues() {
                return this.value.lookupValues || []
            },
            typeLabel() {
                return this.types[this.value.type];
            }
        },
        methods: {
            addValueToLookup() {
                let lookupValue = {
                    caption: '',
                    captions: {
                        'backend': '',
                    },
                    value: ''
                };
                for (let lang in this.languages) {
                    if (this.languages.hasOwnProperty(lang)) {
                        lookupValue.captions[lang] = '';
                    }
                }

                this.emitValue('lookupValues.' + (this.lookupValues.length), lookupValue);
            },
            emitValue(what, value) {
                let attr = Object.assign({}, this.value);
                let obj = attr;
                what = what.split('.');
                for (let i = 0; i < what.length; i++) {
                    if (i === (what.length - 1)) {
                        obj[what[i]] = value;
                    } else {
                        if (typeof obj[what[i]] === typeof undefined) {
                            obj[what[i]] = isNaN(what[i + 1]) ? {} : [];
                        }
                        obj = obj[what[i]];
                    }
                }
                this.$emit('input', attr);
            },
            endEditing() {
                this.$emit('editing', {index: this.index, status: 0});
            },
            endLookupValueCaptionTranslation() {
                this.showLookupValueTranslateCaptions = false;
                this.activeLookupValue = -1;
            },
            remove() {
                this.$emit('removeMe')
            },
            removeLookupValue(index) {
                let attr = Object.assign({}, this.value);
                attr.lookupValues.splice(index, 1);
                this.$emit('input', attr);
            },
            startEditing() {
                this.$emit('editing', {index: this.index, status: 1});
            },
            startLookupValueCaptionTranslation(index) {
                this.showLookupValueTranslateCaptions = true;
                this.activeLookupValue = index;
            }
        },
        props: {
            errors: {
                type: Object,
                required: true,
            },
            editing: {
                type: Boolean
            },
            value: {
                type: Object,
                required: true,
            },
            index: {
                type: Number,
                required: true,
            },
            languages: {
                type: Object,
                required: true,
            }
        }
    }
</script>
