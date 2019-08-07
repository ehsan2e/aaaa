<template>
    <div>

        <template v-if="editingAttribute">
            <attribute-component v-model="attributeList[activeAttribute]" :index="activeAttribute"
                                 key="editing-attribute"
                                 @editing="handleEditing"
                                 :languages="languages" :errors="errors" editing/>
        </template>
        <template v-else>
            <table class="table table-striped">
                <thead>
                <th>Name</th>
                <th>Caption</th>
                <th>Type</th>
                <th>Action</th>
                </thead>
                <tbody>
                <attribute-component v-for="(attribute, index) in attributeList"
                                     v-model="attributeList[index]" :index="index"
                                     :key="'' + index + '-' + attribute.name"
                                     @removeMe="removeAttribute(index)"
                                     @editing="handleEditing"
                                     :languages="languages"  :errors="errors"/>
                <tr v-if="attributeList.length === 0">
                    <td class="text-center" colspan="4">Attribute set is empty</td>
                </tr>
                </tbody>
            </table>
        </template>
        <button class="btn btn-primary btn-block" type="button" @click.prevent="addAttribute"
                v-show="!editingAttribute">Add Attribute
        </button>
        <span v-for="(item, index) in attributeList" :key="index">
            <input type="hidden" :name="'custom_attributes['+index+'][name]'" :value="item.name">
            <input type="hidden" :name="'custom_attributes['+index+'][caption]'" :value="item.caption">
            <input type="hidden" :name="'custom_attributes['+index+'][required]'" value="1" v-if="item.required">
            <input type="hidden" :name="'custom_attributes['+index+'][type]'" :value="item.type">
            <template v-for="(itemc, indexc) in item.captions">
                <input type="hidden" :name="'custom_attributes['+index+'][captions]['+indexc+']'" :value="itemc">
            </template>
            <template v-if="item.type==='lookup'" v-for="(iteml, indexl) in item.lookupValues">
                <input type="hidden" :name="'custom_attributes['+index+'][lookupValues]['+indexl+'][caption]'"
                       :value="iteml.caption">
                <input type="hidden" :name="'custom_attributes['+index+'][lookupValues]['+indexl+'][value]'"
                       :value="iteml.value">
                <template v-for="(itemlc, indexlc) in iteml.captions">
                    <input type="hidden"
                           :name="'custom_attributes['+index+'][lookupValues]['+indexl+'][captions]['+indexlc+']'"
                           :value="itemlc">
                </template>
            </template>
        </span>
    </div>
</template>

<script>
    export default {
        name: 'attribute-set-component',
        data() {
            return {
                activeAttribute: -1,
                cnt: 1,
                editingAttribute: false,
                attributeList: [],
            };
        },
        methods: {
            addAttribute() {
                this.attributeList.push(this.getAttributeSkeleton());
                this.handleEditing({status: 1, index: this.attributeList.length - 1});
            },
            getAttributeSkeleton() {
                let captions = {
                    'backend': '',
                };
                for (let lang in this.languages) {
                    if (this.languages.hasOwnProperty(lang)) {
                        captions[lang] = '';
                    }
                }
                return {
                    caption: '',
                    captions: captions,
                    lookupValues: [],
                    name: 'v' + (this.cnt++),
                    required: false,
                    type: 'string'
                };
            },
            handleEditing(payload) {
                if (payload.status === 1) {
                    this.editingAttribute = true;
                    this.activeAttribute = payload.index;
                } else {
                    this.editingAttribute = false;
                    this.activeAttribute = -1;
                }
            },
            removeAttribute(index) {
                this.attributeList.splice(index, 1);
            }

        },
        mounted() {
            let attributeList = [];
            console.log(this.attributes.length, this.attributes);
            for (let i=0; i < this.attributes.length; i++) {
                console.log(i, this.attributes[i]);
                attributeList.push(Object.assign({}, attributeList, this.attributes[i]));
            }
            this.attributeList = attributeList;
            this.cnt = attributeList.length + 1;
        },
        props: {
            attributes: {
                type: Array,
                required: true,
            },
            errors: {
                type: Object,
                required: true,
            },
            languages: {
                type: Object,
                required: true,
            }
        }
    }
</script>
