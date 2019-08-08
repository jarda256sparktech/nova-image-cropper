<template>
	<div class="el-row--flex">
		<div class="width50">
			<img :src="image">
		</div>
		<div class="width50">
			<img :src="image">
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
	}
</style>
