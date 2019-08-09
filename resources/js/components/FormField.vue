<template>
    <default-field :field="field">
        <template slot="field">
            <loading-view :loading="deleting">
                <div class="picker-wrapper">
                    <PicturePicker
                        :aspect-ratio="field.aspectRatio"
                        :value-object="valueObject"
                        @update-value-object="onUpdateValueObject"
                        @deleteImage="confirmRemoval"
                        @fileChanged="setFile"
                        @cropFileChanged="setCropFile"
                        ref="picturePicker"
                        v-model="value"
                    />
                    <p
                        class="my-2 text-danger"
                        v-if="hasError"
                    >{{ firstError }}</p>

                    <portal to="modals">
                        <transition name="fade">
                            <confirm-upload-removal-modal
                                @close="closeRemoveModal"
                                @confirm="removeFile"
                                v-if="removeModalOpen"
                            />
                        </transition>
                    </portal>
                </div>
            </loading-view>
        </template>
    </default-field>
</template>

<script>
	import {Errors, FormField, HandlesValidationErrors} from 'laravel-nova'
	import PicturePicker from './PicturePicker'

	require('element-ui/lib/theme-chalk/index.css');

	export default {
		components: {PicturePicker},

		mixins: [FormField, HandlesValidationErrors],

		props: ['resourceName', 'resourceId', 'field'],

		data() {
			return {
				isNew: false,
				valueObject: {},
				originalValueObject: {},
				editingImage: true,
				resetModalOpen: false,
				removeModalOpen: false,
				deleting: false,


				cropFile: null,
				file: null,
				origFile: null,
				fileName: '',
				uploadErrors: new Errors()
			}
		},

		computed: {
			isNew() {
				return !Boolean(this.field.previewUrl)
			}
		},
		watch: {
			valueObject(value) {
				this.value = JSON.stringify(value);
			}
		},

		beforeMount() {
			console.log('before mounted FormField');
			this.value = this.field.value || '';
			this.valueObject = JSON.parse(this.field.value) || {};
			this.valueObject.modified = false;
			if (!this.isNew) {
				let file = new File(this.field.previewUrl);
				if (typeof FileReader === 'function') {
					const reader = new FileReader();
					reader.onload = event => {
						this.valueObject.binaryImg = event.target.result
					};
					reader.readAsDataURL(file)
				} else {
					alert('Sorry, FileReader API not supported')
				}
				this.originalValueObject = this.valueObject;
			}
		},

		methods: {
			setFile(file) {
				this.file = file;
				this.fileName = file.name
			},
			setCropFile(file) {
				// console.log('setThumbFile');
				this.cropFile = file
			},

			/*
             * Reset the file state.
             */
			resetFile(file) {
				this.file = null;
				this.cropFile = null;
				this.fileName = '';
				this.value = '';
				this.valueObject = {};
			},


			async removeFile() {
				this.closeRemoveModal();

				if (this.isNew) {
					this.resetFile();
					return
				}

				this.deleting = true;
				this.uploadErrors = new Errors();

				const {
					resourceName,
					resourceId,
					relatedResourceName,
					relatedResourceId,
					viaRelationship
				} = this;
				const attribute = this.field.attribute;

				const uri = this.viaRelationship
					? `/nova-api/${resourceName}/${resourceId}/${relatedResourceName}/${relatedResourceId}/field/${attribute}?viaRelationship=${viaRelationship}`
					: `/nova-api/${resourceName}/${resourceId}/field/${attribute}`;

				try {
					await Nova.request().delete(uri);
					this.deleting = false;
					this.resetFile();
					this.$emit('file-deleted')
				} catch (error) {
					this.deleting = false;

					if (error.response.status == 422) {
						this.uploadErrors = new Errors(error.response.data.errors)
					}
				}
			},

			// setInitialValue() {
			// 	console.log('init field value',this.field.value);
			// 	this.value = this.field.value || '';
			// 	this.valueObject = JSON.parse(this.field.value) || {};
			//
			// },

			/**
			 * Update the field's internal value.
			 */
			handleChange(value) {
				this.value = value
			},

			onUpdateValueObject(valueObject) {
				console.log('onUpdateValueObject', valueObject);
				this.valueObject = valueObject;
			},
			confirmRemoval() {
				this.removeModalOpen = true
			},
			closeRemoveModal() {
				this.removeModalOpen = false
			},
			confirmReset() {
				this.resetModalOpen = true
			},
			closeResetModal() {
				this.resetModalOpen = false
			},
			resetChanges() {
				this.valueObject = this.originalValueObject;
				this.file = null;
				this.cropFile = null;
				this.fileName = '';
				this.value = '';
				this.valueObject = {};
			},
		}
	}
</script>
<style scoped>
</style>
