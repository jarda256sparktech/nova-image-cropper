Nova.booting((Vue, router) => {
    Vue.component('index-nova-image-cropper', require('./components/IndexField'));
    Vue.component('detail-nova-image-cropper', require('./components/DetailField'));
    Vue.component('form-nova-image-cropper', require('./components/FormField'));
    Vue.component('confirm-upload-reset-modal', require('./components/ConfirmUploadResetModal'));
});
