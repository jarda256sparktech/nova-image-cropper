<template>
    <panel-item :field="field">
        <div slot="value">
            <img
                :src="field.thumbnailUrl"
            />
        </div>
    </panel-item>
</template>

<script>
	export default {
		props: ['resource', 'resourceName', 'resourceId', 'field']
	};
</script>
<style scoped>
</style>
