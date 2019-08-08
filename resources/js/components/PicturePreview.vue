<template>
	<div class="el-row--flex">
		<div class="width50">
			<img :src="image">
		</div>
		<div class="width50">
			<div id="cropped-preview"></div>
		</div>
	</div>
</template>
<script>

	export default {
		name: 'PicturePreview',
		props: ['image'],

		data() {
			return {}
		},

		computed: {},

		watch: {},

		methods: {}
	}
</script>
<style scoped>
	.width50 {
		width: 50%;
		box-sizing: border-box;
		padding: 15px;
		background-color: #808080;
	}

	#cropped-preview {
		overflow: hidden;
	}
</style>
